# プロジェクト構造 / Project Structure

## 概要 / Overview

車検予約管理システムのプロジェクト構造とファイル配置について説明します。
This document describes the project structure and file organization for the Vehicle Inspection Reservation Management System.

## ディレクトリ構造 / Directory Structure

```
kamishima-reserve/                      # プロジェクトルート / Project Root
├── src/                                # CodeIgniter 4 アプリケーション / CodeIgniter 4 Application
│   ├── app/                            # アプリケーション本体 / Application Core
│   │   ├── Controllers/                # コントローラー / Controllers
│   │   ├── Models/                     # モデル / Models
│   │   ├── Views/                      # ビュー / Views
│   │   │   ├── Admin/                  # 管理画面ビュー / Admin Views
│   │   │   ├── Customer/               # 顧客向けビュー / Customer Views
│   │   │   ├── Layouts/                # レイアウトテンプレート / Layout Templates
│   │   │   └── Partials/               # 部分テンプレート / Partial Templates
│   │   ├── Config/                     # 設定ファイル / Configuration Files
│   │   ├── Database/                   # データベース関連 / Database Related
│   │   │   ├── Migrations/             # マイグレーション / Migrations
│   │   │   └── Seeds/                  # シーダー / Seeds
│   │   ├── Helpers/                    # ヘルパー関数 / Helper Functions
│   │   ├── Libraries/                  # カスタムライブラリ / Custom Libraries
│   │   └── Filters/                    # フィルター / Filters
│   ├── public/                         # 公開ディレクトリ / Public Directory
│   │   ├── index.php                   # エントリーポイント / Entry Point
│   │   ├── build-vite/                 # ✅ 本番ビルド成果物 / Production Build (Git Managed)
│   │   ├── build-vite-test/            # ❌ テストビルド成果物 / Test Build (Git Ignored)
│   │   ├── images/                     # 画像ファイル / Image Files
│   │   └── uploads/                    # アップロードファイル / Upload Files
│   ├── writable/                       # 書き込み可能ディレクトリ / Writable Directory
│   │   ├── cache/                      # キャッシュ / Cache
│   │   ├── logs/                       # ログファイル / Log Files
│   │   ├── session/                    # セッション / Session
│   │   └── uploads/                    # アップロード一時ファイル / Temporary Uploads
│   └── vendor/                         # Composer依存関係 / Composer Dependencies
├── assets/                             # フロントエンド開発ソース / Frontend Development Source
│   ├── js/                             # JavaScript開発ファイル / JavaScript Development Files
│   │   ├── admin.js                    # 🔄 管理画面メイン（新ページ追加時要修正）/ Admin Main (Update when adding new pages)
│   │   ├── customer.js                 # 🔄 顧客向けメイン（新ページ追加時要修正）/ Customer Main (Update when adding new pages)
│   │   ├── common.js                   # 共通処理 / Common Functions
│   │   └── admin/                      # 管理画面専用 / Admin Specific
│   │       ├── pages/                  # ページ別JavaScript（構成例）/ Page Specific JavaScript (Example Structure)
│   │       │   ├── [feature-name]/     # 📁 機能名フォルダ（例：reservations, shop-closing-days）
│   │       │   │   ├── index.js        # 一覧ページ / List Page
│   │       │   │   ├── new.js          # 新規作成ページ / New Page
│   │       │   │   ├── edit.js         # 編集ページ / Edit Page
│   │       │   │   ├── form-common.js  # フォーム共通 / Form Common
│   │       │   │   └── ...             # その他機能固有ファイル / Other feature-specific files
│   │       │   └── 📝 実装例: reservations/, shop-closing-days/ etc.
│   │       ├── plugins/                # プラグイン設定 / Plugin Settings
│   │       ├── utils/                  # ユーティリティ / Utilities
│   │       └── ui-interactions.js      # UI操作 / UI Interactions
│   ├── scss/                           # SCSS開発ファイル / SCSS Development Files
│   │   ├── admin/                      # 管理画面スタイル / Admin Styles
│   │   │   ├── admin.scss              # 🔄 メインエントリーポイント（新ページ追加時要修正）/ Main Entry Point (Update when adding new pages)
│   │   │   ├── base/                   # 基本スタイル / Base Styles
│   │   │   ├── components/             # コンポーネント / Components
│   │   │   ├── layout/                 # レイアウト / Layout
│   │   │   └── pages/                  # ページ別スタイル（構成例）/ Page Specific Styles (Example Structure)
│   │   │       ├── [feature-name]/     # 📁 機能名フォルダ（例：reservations, shop-closing-days）
│   │   │       │   ├── _index.scss     # 一覧ページスタイル / List Page Styles
│   │   │       │   ├── _form.scss      # フォームスタイル / Form Styles
│   │   │       │   └── ...             # その他機能固有スタイル / Other feature-specific styles
│   │   │       └── 📝 実装例: reservations/, shop-closing-days/ etc.
│   │   └── customer/                   # 顧客向けスタイル / Customer Styles
│   │       └── customer.scss           # 🔄 メインエントリーポイント（新ページ追加時要修正）/ Main Entry Point (Update when adding new pages)
│   └── images/                         # 開発用画像 / Development Images
├── scripts/                            # 🆕 プロジェクト管理スクリプト / Project Management Scripts
│   ├── build/                          # ビルド関連スクリプト / Build Related Scripts
│   │   ├── build-production.sh         # 本番ビルド / Production Build
│   │   ├── build-test.sh               # テストビルド / Test Build
│   │   ├── clean-test.sh               # テストクリーンアップ / Test Cleanup
│   │   └── restore-backup.sh           # バックアップ復元 / Backup Restore
│   ├── deploy/                         # デプロイ関連スクリプト / Deploy Related Scripts
│   │   └── create-package.sh           # FTPパッケージ作成 / FTP Package Creation
│   └── README.md                       # スクリプト使用方法 / Scripts Usage Guide
├── docs/                               # プロジェクトドキュメント / Project Documentation
│   ├── basic_design.md                 # 基本設計書 / Basic Design Document
│   ├── database_specification.md       # データベース仕様書 / Database Specification
│   ├── user_interface_design.md        # UI設計書 / UI Design Document
│   ├── naming_conventions.md           # 命名規約 / Naming Conventions
│   ├── coding_rules.md                 # コーディング規約 / Coding Rules
│   └── project_structure.md            # 📄 このファイル / This File
├── node_modules/                       # ❌ NPM依存関係（Git除外）/ NPM Dependencies (Git Ignored)
├── .gitignore                          # Git除外設定 / Git Ignore Settings
├── .gitattributes                      # Git属性設定 / Git Attributes
├── package.json                        # NPM設定 / NPM Configuration
├── package-lock.json                   # NPM依存関係ロック / NPM Dependencies Lock
├── vite.config.js                      # Vite設定 / Vite Configuration
├── composer.json                       # Composer設定 / Composer Configuration
├── composer.lock                       # Composer依存関係ロック / Composer Dependencies Lock
└── README.md                           # プロジェクト説明 / Project Description
```

## 実行時生成ファイル / Runtime Generated Files

以下のファイルは実行時に生成され、Gitで管理されません。
The following files are generated at runtime and are not managed by Git.

```
# ビルド関連 / Build Related
src/public/build-vite-test/             # テストビルド成果物 / Test Build Artifacts
src/public/build-vite.backup.*          # バックアップファイル / Backup Files

# パッケージ関連 / Package Related
ftp-complete-YYYYMMDD_HHMMSS.zip        # 完全パッケージ / Full Package
ftp-assets-YYYYMMDD_HHMMSS.zip          # アセットパッケージ / Assets Package

# 開発環境関連 / Development Environment Related
node_modules/                           # NPM依存関係 / NPM Dependencies
.vite/                                  # Viteキャッシュ / Vite Cache
```

## Git管理方針 / Git Management Policy

### ✅ Git管理対象 / Git Managed Files
- `src/public/build-vite/` - 本番ビルド成果物（FTPデプロイ用）/ Production build artifacts (for FTP deployment)
- `assets/` - 開発ソースファイル / Development source files
- `scripts/` - プロジェクト管理スクリプト / Project management scripts
- `docs/` - プロジェクトドキュメント / Project documentation
- 設定ファイル / Configuration files

### ❌ Git除外対象 / Git Ignored Files
- `src/public/build-vite-test/` - テストビルド成果物 / Test build artifacts
- `ftp-*.zip` - FTPパッケージファイル / FTP package files
- `*.backup.*` - バックアップファイル / Backup files
- `node_modules/` - NPM依存関係 / NPM dependencies
- `.vite/` - Viteキャッシュ / Vite cache
- ログファイル / Log files

## 主要な技術スタック / Technology Stack

### バックエンド / Backend
- **PHP** 8.1.x
- **CodeIgniter** 4.6.x
- **MySQL** 5.7.x
- **Composer** - PHP依存関係管理 / PHP dependency management

### フロントエンド / Frontend
- **Vite** 5.4.x - ビルドツール / Build tool
- **SCSS** - CSSプリプロセッサ / CSS preprocessor
- **JavaScript** ES6+ - モジュール化されたJavaScript / Modularized JavaScript
- **Bootstrap** 5.3.x - CSSフレームワーク / CSS framework

### 開発ツール / Development Tools
- **NPM Scripts** - タスク管理 / Task management
- **Bash Scripts** - デプロイ・ビルド自動化 / Deploy & build automation
- **Git** - バージョン管理 / Version control

## ファイル命名規則 / File Naming Conventions

### PHP ファイル / PHP Files
- **Controllers**: `PascalCase` + `Controller` suffix
  - 例: `ReservationController.php`
- **Models**: `PascalCase` + `Model` suffix
  - 例: `ReservationModel.php`
- **Views**: `snake_case`
  - 例: `reservation_list.php`

### JavaScript ファイル / JavaScript Files
- **メインファイル**: `kebab-case`
  - 例: `admin.js`, `customer.js`
- **ページ別ファイル**: `kebab-case`
  - 例: `reservation-list.js`, `new-reservation.js`

### SCSS ファイル / SCSS Files
- **パーシャル**: `_` prefix + `kebab-case`
  - 例: `_variables.scss`, `_components.scss`
- **メインファイル**: `kebab-case`
  - 例: `admin.scss`, `customer.scss`

## 開発ワークフロー / Development Workflow

### 1. 開発時 / Development
```bash
npm run dev                 # 開発サーバー起動 / Start development server
```

### 2. テスト時 / Testing
```bash
npm run build:test          # テストビルド / Test build
npm run build:clean-test    # テストクリーンアップ / Clean test files
```

### 3. 本番デプロイ時 / Production Deployment
```bash
npm run build:prod          # 本番ビルド / Production build
npm run ftp:package         # FTPパッケージ作成 / Create FTP package
```

### 4. 問題発生時 / Troubleshooting
```bash
npm run build:restore       # バックアップから復元 / Restore from backup
npm run clean               # 全クリーンアップ / Clean all
```

## セキュリティ考慮事項 / Security Considerations

### ファイルアクセス制御 / File Access Control
- `src/writable/` - 書き込み権限必須 / Write permission required
- `src/public/` - Web公開ディレクトリ / Web accessible directory
- `src/app/`, `assets/`, `scripts/` - Web非公開 / Not web accessible

### 機密情報管理 / Sensitive Information Management
- `.env` ファイル - 環境設定（Git除外）/ Environment settings (Git ignored)
- データベース認証情報 - 環境変数で管理 / Database credentials via environment variables
- APIキー - 環境変数で管理 / API keys via environment variables

## 保守・運用 / Maintenance & Operations

### 定期作業 / Regular Tasks
- ログファイルの削除 / Log file cleanup
- バックアップファイルの整理 / Backup file organization
- 依存関係の更新確認 / Dependency update check

### 監視項目 / Monitoring Items
- ディスク使用量 / Disk usage
- ビルドファイルサイズ / Build file size
- パフォーマンス指標 / Performance metrics

### 🔄 新機能追加時の必須メンテナンス / Required Maintenance When Adding New Features

新しい管理画面ページを追加する際は、以下のファイルの修正が**必須**です。
When adding new admin pages, the following files **must** be updated:

1. **assets/js/admin.js**
   - 動的インポートケースの追加 / Add dynamic import case
   - 新しい`body_id`に対応するスクリプト読み込み / Script loading for new body_id

2. **assets/scss/admin/admin.scss**
   - 新機能のSCSSファイルをインポート / Import new feature SCSS files
   - `@use "pages/[feature-name]/index";` などの追加 / Add @use statements

3. **src/app/Views/Layouts/admin_layout.php**
   - 新ページの`body_id`設定確認 / Verify body_id setting for new pages

### 📁 推奨フォルダ構成 / Recommended Folder Structure

新機能を追加する際は、以下の構成を参考にしてください。
When adding new features, use the following structure as reference:

```
assets/js/admin/pages/[feature-name]/
├── index.js         # 一覧ページ専用
├── new.js          # 新規作成専用  
├── edit.js         # 編集専用
├── form-common.js  # フォーム関連共通機能
├── common.js       # 機能全体共通機能
└── [other].js      # その他機能固有ファイル

assets/scss/admin/pages/[feature-name]/
├── _index.scss     # 一覧ページスタイル
├── _form.scss      # フォームスタイル
└── _[other].scss   # その他機能固有スタイル
```

### 📝 JavaScript共通ファイルの命名規則 / JavaScript Common Files Naming Convention

機能内で複数の共通ファイルが必要な場合は、以下の命名規則に従ってください。
When multiple common files are needed within a feature, follow these naming conventions:

#### 1. **common.js** - 機能全体共通 / Feature-wide Common
**用途 / Usage**: 機能全体で使用する汎用的な共通処理
- フラッシュメッセージの自動非表示 / Auto-hide flash messages
- ツールチップの初期化 / Tooltip initialization  
- 機能固有の汎用ユーティリティ / Feature-specific general utilities

```javascript
// 例 / Example: shop-closing-days/common.js
export function initCommonFeatures() {
    initAutoHideAlerts();    // 全ページで使用
    initTooltips();          // 全ページで使用
}
```

#### 2. **form-common.js** - フォーム関連共通 / Form-related Common
**用途 / Usage**: フォーム関連ページ（new.js, edit.js等）間での共通処理
- フォーム専用バリデーション / Form-specific validation
- 入力フィールドの動的制御 / Dynamic input field control
- フォーム送信処理 / Form submission handling

```javascript
// 例 / Example: reservations/form-common.js
export class ReservationFormManager {
    setupTimeSlots() { /* 新規・編集で共通のフォーム処理 */ }
    validateForm() { /* フォーム専用バリデーション */ }
}
```

#### 3. **[specific]-common.js** - 特定用途共通 / Specific Purpose Common
**用途 / Usage**: 特定の用途に特化した共通処理
- `table-common.js` - テーブル操作関連共通 / Table operation common
- `modal-common.js` - モーダル操作関連共通 / Modal operation common
- `api-common.js` - API通信関連共通 / API communication common

#### 使い分けの判断基準 / Decision Criteria

| ファイル名 / File Name | 使用場面 / Use Case | 影響範囲 / Impact Scope |
|---|---|---|
| `common.js` | 機能全体で使う汎用処理 | 機能内全ページ |
| `form-common.js` | フォーム関連ページ間の共通処理 | new.js, edit.js等 |
| `[specific]-common.js` | 特定用途に特化した共通処理 | 関連ページのみ |

#### メリット / Benefits
- **役割が明確** / Clear responsibilities - ファイル名から用途が分かる
- **保守性向上** / Improved maintainability - 変更影響範囲が把握しやすい  
- **再利用性** / Better reusability - 適切な粒度での共通化

---

**Last Updated:** 2025年6月26日 / June 26, 2025  
**Version:** 1.0