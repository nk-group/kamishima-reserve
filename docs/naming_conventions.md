## プロジェクト命名規約書

### 1. 基本方針

- この規約は、プロジェクト全体のコードの可読性、保守性、一貫性を高めることを目的とします。
- 各技術スタック（PHP, SCSS, JavaScript, HTML, データベース）の一般的なベストプラクティスと、プロジェクト固有の決定事項を組み合わせます。
- 迷った場合は、この規約に立ち返り、一貫性を保つように努めます。

### 2. ファイル・ディレクトリ命名規則

| 対象  | 命名規則 | 例   | 備考  |
| --- | --- | --- | --- |
| **CodeIgniter フォルダ** | パスカルケース (PascalCase) | `Controllers`, `Models`, `Views`, `Config`, `Libraries`, `Helpers` | `src/app/` 配下の主要な機能別ディレクトリ |
| **CodeIgniter コントローラー** | パスカルケース + `Controller` サフィックス | `DashboardController.php`, `ReservationController.php` |     |
| **CodeIgniter モデル** | パスカルケース + `Model` サフィックス | `UserModel.php`, `VehicleTypeModel.php` |     |
| **CodeIgniter ライブラリ** | パスカルケース | `PdfGenerator.php`, `EmailService.php` |     |
| **CodeIgniter ヘルパーファイル** | スネークケース + `_helper` サフィックス | `custom_functions_helper.php` |     |
| **CodeIgniter ビューファイル** | スネークケース (`_`) | `user_list.php`, `reservation_form.php` |     |
| **CodeIgniter ビューパーシャル** | アンダースコア (`_`) 始まりのスネークケース | `_header.php`, `_admin_sidebar.php` | `src/app/Views/Partials/` 配下など |
| **SCSS フォルダ** | ケバブケース (`-`) | `base`, `components`, `layout`, `pages`, `themes`, `vendors` | `assets/scss/` 配下 |
| **SCSS ファイル (パーシャル)** | アンダースコア (`_`) 始まりのケバブケース | `_variables.scss`, `_buttons.scss`, `_admin-layout.scss` | 他のSCSSファイルから `@use` される部品 |
| **SCSS ファイル (非パーシャル)** | ケバブケース (`-`) | `admin.scss`, `customer.scss`, `dashboard-page.scss` | エントリーポイントや、特定のページ/機能のスタイルをまとめたファイル |
| **JavaScript フォルダ** | ケバブケース (`-`) | `components`, `pages`, `utils` | `assets/js/` 配下 |
| **JavaScript ファイル** | ケバブケース (`-`) | `common-scripts.js`, `flatpickr-init.js`, `admin-dashboard.js` |     |
| **画像ファイル** | ケバブケース (`-`) または スネークケース (`_`) | `logo-main.png`, `icon-calendar.svg`, `user_avatar_default.jpg` | プロジェクト内でどちらかに統一 |
| **設定ファイル (.envなど)** | 原則としてファイル名固定 | `.env`, `routes.php` | フレームワークやライブラリの規約に従う |

### 3. PHP コード命名規則

| 対象  | 命名規則 | 例   | 備考  |
| --- | --- | --- | --- |
| **クラス名** | パスカルケース (PascalCase) | `Reservation`, `CustomValidationRules` | ファイル名と一致させる (コントローラー、モデル、ライブラリ以外) |
| **メソッド名** | キャメルケース (camelCase) | `getUserById()`, `calculateTotalPrice()`, `renderView()` |     |
| **変数名** | キャメルケース (camelCase) | `$userName`, `$reservationList`, `$isValid` | ループ変数 (`$i`, `$key`, `$value`) は短くても可 |
| **関数名 (ヘルパー)** | スネークケース (`_`) | `format_date()`, `get_active_user_count()` | CodeIgniterの組み込みヘルパーに合わせる |
| **定数** | 大文字スネークケース (UPPER_SNAKE_CASE) | `MAX_USERS`, `DEFAULT_TIMEOUT`, `API_ENDPOINT` | `define()` や `const` で定義 |
| **名前空間** | パスカルケース (PascalCase) | `App\Controllers`, `App\Models\Admin`, `MyLibrary\Services` | ディレクトリ構造と一致させる |
| **トレイト名** | パスカルケース + `Trait` サフィックス (推奨) | `TimestampableTrait`, `SoftDeleteTrait` |     |
| **インターフェース名** | パスカルケース + `Interface` サフィックス (推奨) | `UserRepositoryInterface`, `LoggerInterface` |     |

### 4. SCSS コード命名規則

| 対象  | 命名規則 | 例   | 備考  |
| --- | --- | --- | --- |
| **変数名** | ケバブケース (`-`) | `$primary-color`, `$font-size-base`, `$button-padding-y` | グローバル変数は `_variables.scss` に集約 |
| **ミックスイン名** | ケバブケース (`-`) | `@mixin button-variant($bg, $color)`, `@mixin clearfix` |     |
| **関数名** | ケバブケース (`-`) | `@function strip-unit($number)` |     |
| **プレースホルダーセレクタ** | `%` + ケバブケース (`-`) | `%alert-base`, `%visually-hidden` | `@extend` で使用 |
| **CSS クラス名** | ケバブケース (`-`) | `.main-navigation`, `.btn-submit`, `.user-profile-card` | 必要に応じてBEM (`.block__element--modifier`) も検討 |
| **CSS ID名** | ケバブケース (`-`) | `#main-header`, `#user-settings-modal` | 可能な限りクラスを使用し、IDの使用は最小限に |

### 5. JavaScript コード命名規則

| 対象  | 命名規則 | 例   | 備考  |
| --- | --- | --- | --- |
| **変数名** | キャメルケース (camelCase) | `currentUser`, `isLoading`, `totalAmount` |     |
| **関数名** | キャメルケース (camelCase) | `getUserData()`, `initializeSlider()`, `handleSubmit()` |     |
| **定数** | 大文字スネークケース (UPPER_SNAKE_CASE) | `MAX_RETRIES`, `API_URL`, `DEFAULT_SETTINGS` | `const` で宣言 |
| **クラス名** | パスカルケース (PascalCase) | `class UserModal { ... }`, `class ApiClient { ... }` |     |
| **メソッド名** | キャメルケース (camelCase) | `userModal.open()`, `apiClient.fetchData()` |     |
| **モジュール名** | ケバブケース (`-`) (ファイル名と一致) | `import * as userService from './user-service.js';` |     |

### 6. データベース命名規則

| 対象  | 命名規則 | 例   | 備考  |
| --- | --- | --- | --- |
| **テーブル名** | スネークケース (`_`) (複数形) | `users`, `reservations`, `vehicle_types`, `order_items` |     |
| **カラム名** | スネークケース (`_`) | `first_name`, `created_at`, `is_active`, `user_id` (FK) |     |
| **主キー** | `id` | `id` |     |
| **外部キー** | 参照テーブル単数形 + `_id` | `user_id` (usersテーブルを参照), `order_id` (ordersテーブルを参照) |     |
| **インデックス名** | `idx_` + テーブル名 + `_` + カラム名(複数可) | `idx_users_email`, `idx_reservations_date_time` |     |
| **ピボットテーブル名** | スネークケース (`_`) (アルファベット順) | `role_user`, `permission_role` | 多対多リレーションの中間テーブル |

### 7. HTML 命名規則

| 対象  | 命名規則 | 例   | 備考  |
| --- | --- | --- | --- |
| **クラス名** | ケバブケース (`-`) | `<div class="user-profile">`, `<button class="btn-primary">` | SCSSのクラス名と一致させる |
| **ID名** | ケバブケース (`-`) | `<div id="main-navigation">`, `<form id="login-form">` | SCSSのID名と一致させる。JavaScriptからの操作対象として明確な場合に限定 |
| **カスタムデータ属性** | `data-` + ケバブケース | `<div data-user-id="123">`, `<button data-action="submit">` | JavaScriptでのデータ受け渡しに利用 |

### 8. コミットメッセージ規約 (参考)

- Conventional Commits のような規約を採用することを推奨します。
  - 例: `feat: ユーザー登録機能を追加`
  - 例: `fix: 予約一覧の表示不具合を修正`
  - 例: `docs: READMEのセットアップ手順を更新`
  - 例: `style: コードフォーマットの修正 (ロジック変更なし)`
  - 例: `refactor: 予約処理のパフォーマンス改善`
  - 例: `test: ユーザー認証のテストケースを追加`

### 9. その他

- **コメント**:
  - 複雑なロジックや、一見して意図が分かりにくい箇所には積極的にコメントを記述します。
  - PHPではPHPDoc形式、JavaScriptではJSDoc形式を推奨します。
- **インデント**: プロジェクトで統一されたインデントスタイル（スペース2、スペース4、タブなど）を使用します。EditorConfigファイルで設定することを推奨します。
- **文字コード**: UTF-8
- **改行コード**: LF

---

この規約書は、プロジェクトの進行に合わせて見直し、更新していくことが重要です。 ご不明な点や、さらに追加したい項目があればお気軽にお知らせください。