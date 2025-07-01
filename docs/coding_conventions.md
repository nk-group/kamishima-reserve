# プロジェクト コーディング規約書

**最終更新日:** 2025年6月30日  
**バージョン:** 1.2

## 1. 基本方針

- この規約は、プロジェクト全体のコードの品質、保守性、一貫性を保つことを目的とします
- 新機能開発時や既存機能修正時に、この規約に従ってコードを記述します
- 迷った場合は、この規約に立ち返り、チーム内で議論して決定します
- 規約は随時更新可能ですが、変更時はチーム全体に共有します

---

## 2. PHP（CodeIgniter4）コーディング規約

### 2.1 MVC アーキテクチャの遵守

#### Controller（コントローラー）
- **責務**: HTTP リクエストの処理とレスポンスの生成
- **ビジネスロジック禁止**: 複雑な処理はモデルやライブラリに委譲
- **body_id設定**: 各アクションで JavaScript 読み込み用の `body_id` を設定

```php
// ✅ 良い例
public function index()
{
    $data = [
        'page_title' => 'ユーザー一覧',
        'body_id' => 'page-admin-users-index', // JavaScript動的読み込み用
        'users' => $this->userModel->getActiveUsers(),
    ];
    
    return view('Admin/Users/index', $data);
}

// ❌ 悪い例（ビジネスロジックがコントローラーに含まれている）
public function index()
{
    $users = [];
    $rawUsers = $this->userModel->findAll();
    foreach ($rawUsers as $user) {
        if ($user->is_active && $user->email_verified) {
            $user->full_name = $user->first_name . ' ' . $user->last_name;
            $users[] = $user;
        }
    }
    // ...
}
```

#### Model（モデル）
- **バリデーション**: すべてのバリデーションルールはモデルで定義
- **ビジネスロジック**: データに関連する処理はモデルに実装
- **Config/Validation.php は使用しない**: CodeIgniter の標準的なモデルバリデーションを使用

```php
// ✅ 良い例
class UserModel extends Model
{
    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'name' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'メールアドレスを入力してください。',
            'valid_email' => '正しいメールアドレスを入力してください。',
            'is_unique' => 'このメールアドレスは既に登録されています。',
        ],
    ];

    public function getActiveUsers(): array
    {
        return $this->where('is_active', 1)
                   ->where('email_verified', 1)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
}

// ❌ 悪い例（バリデーションがコントローラーで実行されている）
// Controller内で
$validation = \Config\Services::validation();
if (!$validation->run($postData, 'user_create')) {
    // エラー処理
}
```

#### **⚠️ 例外：認証系コントローラー**

**UserController など CodeIgniter Shield 認証システムを使用するコントローラーは例外とする**

Shield認証システムの特殊な実装要件により、以下のコントローラーではコントローラー側でのバリデーションを許可する：

- `UserController.php` - ユーザー管理（Shield使用）
- その他Shield関連のコントローラー

```php
// ✅ Shield使用時は例外として許可
class UserController extends BaseController
{
    public function create()
    {
        $rules = [
            'full_name' => 'required|string|max_length[20]',
            'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            // Shield特有のバリデーション
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Shield特有の処理...
    }
}
```

**例外理由:**
- Shield認証システムの複雑な実装要件
- 標準的なモデルバリデーションでは対応困難な認証特有の処理
- セキュリティ要件とフレームワーク制約のバランス

### 2.2 バリデーション表示統一規約

プロジェクト全体でバリデーションエラー表示の一貫性を保つため、以下の規約に従う。

#### 上部エラー表示の統一
すべてのフォームページで `_alert_messages` パーシャルを使用する。

```php
// ✅ 良い例：統一されたエラー表示
<?= $this->section('content') ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') ?>
        
        <?= form_open($form_action) ?>
            <!-- フォームフィールド -->
        <?= form_close() ?>
    </div>
<?= $this->endSection() ?>

// ❌ 悪い例：直接記述パターン
<?php if (isset($validation) && $validation->getErrors()): ?>
    <div class="alert alert-danger">
        <h6>入力エラーがあります</h6>
        <ul>
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
```

#### 個別フィールドエラー表示の統一
各フィールドには統一されたバリデーションパターンを適用する。

```php
// ✅ 良い例：統一されたフィールドバリデーション
<div class="form-group">
    <label for="shop_id" class="form-label">
        店舗 <span class="required-mark">※</span>
    </label>
    <?= form_dropdown(
        'shop_id',
        $shops,
        old('shop_id', $form_data['shop_id'] ?? ''),
        [
            'class' => 'form-select' . (isset($validation) && $validation->hasError('shop_id') ? ' is-invalid' : ''),
            'id' => 'shop_id',
            'required' => true
        ]
    ) ?>
    <?php if (isset($validation) && $validation->hasError('shop_id')): ?>
        <div class="invalid-feedback">
            <?= $validation->getError('shop_id') ?>
        </div>
    <?php endif; ?>
</div>
```

#### 必須マークの統一
すべてのフォームで必須項目のマークを統一する。

```php
// ✅ 良い例：統一された必須マーク
<label for="field_name" class="form-label">
    項目名 <span class="required-mark">※</span>
</label>

// ❌ 悪い例：混在パターン
<span class="text-danger">※</span>
<span class="text-danger">*</span>
```

#### コントローラー側でのエラー連携
`_alert_messages` パーシャルとの連携のため、エラーは以下の形式で渡す。

```php
// ✅ 良い例：_alert_messages パーシャル連携
return redirect()->back()
    ->withInput()
    ->with('errors', $this->modelName->errors());

// または単一エラーメッセージ
return redirect()->back()
    ->withInput()
    ->with('error', 'エラーメッセージ');
```

### 2.3 エラーハンドリング統一規約

プロジェクト全体でエラーハンドリングの一貫性を保つため、以下の統一パターンに従う。

#### 基本パターン
すべてのコントローラーで以下の統一されたエラーハンドリングパターンを使用する。

```php
// ✅ 良い例：統一されたエラーハンドリング
try {
    if ($this->modelName->save($data)) {
        return redirect()->to('/target-url')
                       ->with('message', '操作が完了しました。');
    } else {
        $errors = $this->modelName->errors();
        return redirect()->back()
                       ->withInput()
                       ->with('errors', $errors);
    }
} catch (\Throwable $e) {
    log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Operation failed: ' . $e->getMessage());
    
    if (ENVIRONMENT === 'development') {
        log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
    }
    
    return redirect()->back()
        ->withInput()
        ->with('error', '処理に失敗しました。再度お試しください。');
}
```

#### エラーキャッチの統一
- **`\Throwable` を使用**: `\Exception` と `\Error` の両方を捕捉
- **`\Exception` や `\RuntimeException` 単体の使用禁止**

```php
// ✅ 良い例
catch (\Throwable $e) {
    // 包括的なエラーキャッチ
}

// ❌ 悪い例
catch (\Exception $e) {
    // Error クラスの例外を捕捉できない
}

catch (\RuntimeException $e) {
    // 特定例外のみで、他の例外が漏れる可能性
}
```

#### ログ出力の統一
- **基本ログ**: `[ClassName::methodName] operation failed: message` 形式
- **開発環境詳細ログ**: ファイル名・行番号を追加出力

```php
// ✅ 良い例：統一されたログ形式
log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] User creation failed: ' . $e->getMessage());

if (ENVIRONMENT === 'development') {
    log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
}

// ❌ 悪い例：非統一のログ形式
log_message('error', 'User creation failed: ' . $e->getMessage());
log_message('error', 'Failed data: ' . json_encode($userData)); // 冗長
```

#### ユーザー向けメッセージの統一
- **汎用的で安全なメッセージ**: 技術的詳細を含まない
- **一貫性のある文言**: 操作毎に統一されたメッセージ

```php
// ✅ 良い例：統一されたメッセージ
->with('error', '登録に失敗しました。再度お試しください。');
->with('error', '更新に失敗しました。再度お試しください。');
->with('error', '削除に失敗しました。再度お試しください。');

// ❌ 悪い例：非統一・技術的詳細含む
->with('error', 'Database error occurred');
->with('error', $e->getMessage()); // 技術的エラーメッセージをそのまま表示
```

### 2.4 従来のエラーハンドリング（参考）

```php
// 参考：従来のエラーハンドリング例
try {
    if ($this->userModel->save($data)) {
        return redirect()->to('/admin/users')
                       ->with('success', 'ユーザーを登録しました。');
    } else {
        $errors = $this->userModel->errors();
        return redirect()->back()
                       ->withInput()
                       ->with('errors', $errors);
    }
} catch (\RuntimeException $e) {
    return redirect()->back()
                   ->withInput()
                   ->with('error', $e->getMessage());
}
```

### 2.5 デバッグコードの禁止

```php
// ❌ 本番環境に残してはいけないコード
log_message('debug', 'User data: ' . json_encode($userData));
var_dump($data);
die('Debug stop');
console.log('Debug info'); // JavaScript でも同様
```

---

## 3. フロントエンド アーキテクチャ規約

### 3.1 ファイル構成

```
assets/
├── scss/
│   ├── admin/
│   │   ├── admin.scss              # エントリーポイント
│   │   ├── base/                   # 変数、タイポグラフィ等
│   │   ├── components/             # 再利用可能なコンポーネント
│   │   ├── layout/                 # ヘッダー、フッター等
│   │   └── pages/                  # ページ固有スタイル
│   │       └── shop-closing-days/  # 機能毎にフォルダ分離
│   │           ├── _index.scss
│   │           ├── _form.scss
│   │           └── _batch.scss
│   └── customer/
│       └── （同様の構成）
└── js/
    ├── admin/
    │   ├── admin.js                # エントリーポイント
    │   ├── plugins/                # 外部ライブラリ設定
    │   ├── utils/                  # 共通ユーティリティ
    │   ├── common/                 # 全ページ共通機能
    │   │   ├── user-preferences.js # 個人設定機能（機能専用）
    │   │   └── ui-common.js        # UI操作関連共通（特定用途共通）
    │   └── pages/                  # ページ固有JavaScript
    │       ├── shop-closing-days/  # 機能毎にフォルダ分離
    │       │   ├── index.js        # 一覧ページ専用
    │       │   ├── form.js         # 新規・編集フォーム共通
    │       │   └── batch.js        # 一括作成専用
    │       └── reservations/       # 予約処理
    │           ├── index.js        # 一覧ページ専用
    │           ├── new.js          # 新規作成専用
    │           ├── edit.js         # 編集専用
    │           └── form-common.js  # フォーム共通機能
    └── customer/
        └── （同様の構成）
```

### 3.2 SCSS 規約

#### 機能毎の分離
```scss
// ✅ 良い例: assets/scss/admin/admin.scss
@use "pages/shop-closing-days/index" as shop-closing-days-index;
@use "pages/shop-closing-days/form" as shop-closing-days-form;
@use "pages/shop-closing-days/batch" as shop-closing-days-batch;

// ❌ 悪い例: 単一ファイルにすべて記述
@use "pages/shop-closing-days"; // すべてが一つのファイル
```

#### コンポーネント設計
```scss
// ✅ 良い例: 再利用可能なコンポーネント
// assets/scss/admin/components/_buttons.scss
.btn {
    &.btn-primary { /* ... */ }
    &.btn-secondary { /* ... */ }
}

// ページ固有で使用
// assets/scss/admin/pages/shop-closing-days/_form.scss
.shop-closing-days-form {
    .form-section {
        background: #f8f9fa;
        // ...
    }
}
```

### 3.3 JavaScript 規約

#### ファイル分割の原則
- **機能毎にフォルダを作成**：関連するページが複数ある場合
- **ページ毎にファイルを分離**：保守性と可読性の向上
- **共通機能の抽出**：重複コードの排除

```javascript
// ✅ 良い例: 機能毎のフォルダ分割
assets/js/admin/pages/
├── shop-closing-days/
│   ├── index.js        # 一覧ページ専用
│   ├── form.js         # 新規・編集フォーム共通
│   └── batch.js        # 一括作成専用
└── reservations/
    ├── index.js        # 一覧ページ専用
    ├── new.js          # 新規作成専用
    ├── edit.js         # 編集専用
    └── form-common.js  # フォーム共通機能

// ❌ 悪い例: 単一ファイルにすべて記述
assets/js/admin/pages/
└── shop-closing-days.js # すべての機能が混在
```

#### **3.3.1 共通ファイル命名規則（assets/js/admin/common/）**

`assets/js/admin/common/`配下のファイルは、以下の2つのパターンで命名する。

**パターン1: 機能専用ファイル**
- `[機能名].js`
- 特定の機能に専用のファイル
- 例: `user-preferences.js`（個人設定機能専用）

**パターン2: 特定用途共通ファイル**
- `[用途]-common.js`
- 複数の機能で共通利用される特定用途のファイル
- 例: `ui-common.js`（UI操作関連共通）、`modal-common.js`（モーダル関連共通）

```javascript
// ✅ 良い例: 一貫性のある命名
assets/js/admin/common/
├── user-preferences.js    # 個人設定機能専用
├── ui-common.js          # UI操作関連共通
├── modal-common.js       # モーダル関連共通（将来追加時）
└── api-common.js         # API通信関連共通（将来追加時）

// ❌ 悪い例: 混在した命名
assets/js/admin/common/
├── user-preferences.js
└── ui-interactions.js    # -common サフィックスがない
```

**判断基準:**
- **単一機能に特化** → `[機能名].js`
- **複数機能で共通利用** → `[用途]-common.js`

#### 動的インポートパターン
```javascript
// ✅ 良い例: assets/js/admin/admin.js
document.addEventListener('DOMContentLoaded', async () => {
    const bodyId = document.body.id;

    switch (bodyId) {
        case 'page-admin-shop-closing-days-index':
            try {
                const { initShopClosingDaysIndex } = await import('./pages/shop-closing-days/index.js');
                initShopClosingDaysIndex();
            } catch (e) {
                console.error('Failed to load shop closing days index scripts:', e);
            }
            break;
            
        case 'page-admin-shop-closing-days-form':
            try {
                const { initShopClosingDaysForm } = await import('./pages/shop-closing-days/form.js');
                initShopClosingDaysForm();
            } catch (e) {
                console.error('Failed to load shop closing days form scripts:', e);
            }
            break;
            
        case 'page-admin-reservations-new':
            try {
                const { initNewReservation } = await import('./pages/reservations/new.js');
                initNewReservation();
            } catch (e) {
                console.error('Failed to load reservation new scripts:', e);
            }
            break;
    }
});
```

#### 機能毎のモジュール分離
```javascript
// ✅ 良い例: assets/js/admin/pages/shop-closing-days/index.js
export function initShopClosingDaysIndex() {
    console.log('Shop Closing Days Index page initialized.');
    // 一覧ページ固有の処理のみ
    setupDeleteConfirmation();
    setupSearchFilters();
}

// ✅ 良い例: assets/js/admin/pages/shop-closing-days/form.js
export function initShopClosingDaysForm() {
    console.log('Shop Closing Days Form page initialized.');
    // フォームページ固有の処理のみ
    setupRepeatTypeControl();
    setupValidation();
}

// ✅ 良い例: 共通機能の分離
// assets/js/admin/pages/reservations/form-common.js
export class ReservationFormManager {
    constructor(options = {}) {
        // 新規・編集で共通の処理
    }
    
    setupTimeSlots() { /* 共通処理 */ }
    validateForm() { /* 共通処理 */ }
}
```

---

## 4. データベース規約

### 4.1 マイグレーションファイル
- すべてのテーブル変更はマイグレーションファイルで管理
- 本番環境への直接的なDDL実行禁止

### 4.2 モデルとテーブルの対応
```php
// ✅ 良い例
class ShopClosingDayModel extends Model
{
    protected $table = 'shop_closing_days';
    protected $returnType = ShopClosingDayEntity::class;
    protected $useSoftDeletes = true;
    
    // バリデーションルールを必ず定義
    protected $validationRules = [
        'shop_id' => 'required|integer|is_not_unique[shops.id]',
        'holiday_name' => 'required|string|max_length[50]',
        // ...
    ];
}
```

---

## 5. セキュリティ規約

### 5.1 データの無害化
```php
// ✅ 良い例
echo esc($user->name); // XSS防止

// フォーム
echo form_input([
    'value' => old('name', $user->name ?? ''),
    'class' => 'form-control'
]);
```

### 5.2 SQLインジェクション対策
```php
// ✅ 良い例（Query Builder使用）
$users = $this->userModel->where('status', $status)->findAll();

// ❌ 悪い例（生SQL）
$query = "SELECT * FROM users WHERE status = '$status'";
```

---

## 6. パフォーマンス規約

### 6.1 データベース
- N+1問題の回避
- 適切なインデックスの設定
- 不要なSELECT * の禁止

### 6.2 フロントエンド
- JavaScript の動的インポート活用
- 不要なライブラリ読み込みの禁止
- 画像の適切な圧縮

---

## 7. コメント・ドキュメント規約

### 7.1 PHPDoc
```php
/**
 * ユーザーの有効なデータを取得
 *
 * @param bool $includeDeleted 削除済みユーザーを含めるか
 * @return array<UserEntity>
 */
public function getActiveUsers(bool $includeDeleted = false): array
{
    // 実装
}
```

### 7.2 複雑なロジックには必ずコメント
```php
// 繰り返し種別による日付マッチング判定
// 単発(0): 完全一致, 毎週(1): 曜日一致, 毎年(2): 月日一致
switch ($closingDay->repeat_type) {
    case self::REPEAT_TYPE_NONE:
        return $baseDate->format('Y-m-d') === $targetDate->format('Y-m-d');
    // ...
}
```

---

## 8. バージョン管理規約

### 8.1 コミットメッセージ
```
feat: 定休日マスタの一括作成機能を追加
fix: 繰り返し終了日フィールドが無効化されない問題を修正
refactor: ShopClosingDayControllerのデバッグコードを削除
docs: コーディング規約書を作成
style: インデントの統一
```

### 8.2 ブランチ戦略
- `main`: 本番環境用
- `develop`: 開発統合用
- `feature/機能名`: 機能開発用

---

## 9. テスト規約

### 9.1 テスト必須項目
- 新機能のビジネスロジック
- バリデーション処理
- 重要なヘルパー関数

### 9.2 テストファイル配置
```
tests/
├── unit/           # 単体テスト
├── integration/    # 結合テスト
└── _support/       # テスト支援ファイル
```

---

## 10. 規約の更新・追加ルール

### 10.1 更新フロー
1. **提案**: 新しいルールや変更提案をチーム内で共有
2. **議論**: 必要性と影響範囲を検討
3. **合意**: チーム全体での合意形成
4. **更新**: 本ファイルの更新とバージョンアップ
5. **周知**: 全メンバーへの変更内容共有

### 10.2 随時追加可能な項目例
- 新しいライブラリの使用規約
- API設計規約
- ログ出力規約
- 多言語対応規約
- アクセシビリティ規約
- **JavaScript ファイル構成の移行ガイドライン**

---

## 11. JavaScript ファイル構成 移行ガイドライン

### 11.1 移行の基本方針
- **新規機能**: 必ずフォルダ分割パターンを採用
- **既存機能**: 段階的にフォルダ分割パターンに移行
- **優先順位**: 複雑な機能や頻繁に更新される機能から先行実施

### 11.2 移行手順
1. **機能分析**: 既存単一ファイルの機能を分析
2. **フォルダ作成**: `assets/js/admin/pages/[機能名]/` フォルダを作成
3. **ファイル分割**: ページ毎・機能毎にファイルを分離
4. **共通機能抽出**: 重複コードを共通ファイルに移動
5. **インポート更新**: `admin.js` のインポートパスを更新
6. **動作確認**: 全ページの JavaScript 動作を確認

### 11.3 移行例：定休日マスタ
```javascript
// 移行前: assets/js/admin/pages/shop-closing-days.js
export function initShopClosingDaysIndex() { /* ... */ }
export function initShopClosingDaysForm() { /* ... */ }
export function initShopClosingDaysBatch() { /* ... */ }

// ↓ 移行後

// assets/js/admin/pages/shop-closing-days/index.js
export function initShopClosingDaysIndex() { /* 一覧専用 */ }

// assets/js/admin/pages/shop-closing-days/form.js
export function initShopClosingDaysForm() { /* フォーム専用 */ }

// assets/js/admin/pages/shop-closing-days/batch.js
export function initShopClosingDaysBatch() { /* 一括作成専用 */ }
```

---

## 付録

### A. 関連ドキュメント
- [プロジェクト構造 ](./project_structure.md)
- [プロジェクト命名規約書](./naming_conventions.md)
- [データベース仕様書](./database_specification.md)
- [基本設計書](./basic_design.md)

### B. 規約チェックリスト
開発完了時に以下を確認：

**PHP**
- [ ] バリデーションはモデルで定義されているか
- [ ] コントローラーにビジネスロジックが含まれていないか
- [ ] body_id が適切に設定されているか
- [ ] デバッグコードが残っていないか
- [ ] **バリデーション表示が統一パターンに従っているか**
- [ ] **エラーハンドリングが統一パターンに従っているか**
- [ ] **必須マークが `<span class="required-mark">※</span>` で統一されているか**
- [ ] **`_alert_messages` パーシャルが使用されているか**

**JavaScript**
- [ ] SCSS/JS ファイルが機能毎にフォルダ分割されているか
- [ ] ページ毎にファイルが適切に分離されているか
- [ ] 共通機能が再利用可能な形で抽出されているか
- [ ] 動的インポートが正しく設定されているか
- [ ] 各ファイルが単一責任の原則に従っているか
- [ ] **共通ファイルの命名規則に従っているか（[機能名].js または [用途]-common.js）**

**セキュリティ**
- [ ] XSS対策（esc関数使用）ができているか
- [ ] SQLインジェクション対策ができているか

---

**このドキュメントは定期的に見直し、プロジェクトの成長とともに改善していきます。**