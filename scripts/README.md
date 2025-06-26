# Scripts Usage Guide / スクリプト使用方法

## 簡単な使い方 / Basic Usage

### 1. 開発時 / Development
```bash
# 開発サーバー起動（自動リロード）
npm run dev
```

### 2. 本番ビルド / Production Build
```bash
# 本番用ビルド実行
npm run build:prod
```

**実行されること / What happens:**
- 既存ビルドファイルの自動バックアップ / Auto backup of existing build
- 本番用CSS/JavaScriptの生成 / Generate production CSS/JavaScript
- ビルド成果物の表示 / Display build artifacts

### 3. テストビルド / Test Build
```bash
# テスト用ビルド実行
npm run build:test

# テストファイルクリーンアップ
npm run build:clean-test
```

### 4. FTPアップロード用パッケージ作成 / FTP Package Creation
```bash
# FTP用ZIPファイル作成
npm run ftp:package
```

**選択できるオプション / Available options:**
1. **完全パッケージ / Full Package** - アプリケーション全体をZIP化
2. **アセットのみ / Assets Only** - CSS/JavaScriptファイルのみ

### 5. 問題が起きた時の復元 / Restore from Backup
```bash
# バックアップから復元
npm run build:restore
```

## ファイル構成 / File Structure

```
プロジェクトフォルダ / Project Root/
├── scripts/
│   ├── build/
│   │   ├── build-production.sh    # 本番ビルド / Production build
│   │   ├── build-test.sh          # テストビルド / Test build
│   │   ├── clean-test.sh          # テストクリーンアップ / Test cleanup
│   │   └── restore-backup.sh      # バックアップ復元 / Backup restore
│   ├── deploy/
│   │   └── create-package.sh      # FTPパッケージ作成 / FTP package creation
│   └── README.md                  # このファイル / This file
├── src/public/build-vite/         # ビルド成果物（Git管理）/ Build artifacts (Git managed)
├── src/public/build-vite-test/    # テストビルド（Git除外）/ Test build (Git ignored)
└── ftp-complete-YYYYMMDD.zip      # 作成されるFTPパッケージ / Generated FTP package
```

## 実際の作業フロー / Actual Workflow

### 新機能開発時 / New Feature Development
1. `npm run dev` で開発 / Development with dev server
2. `npm run build:test` でテストビルド（必要に応じて）/ Test build if needed
3. `npm run build:prod` で本番ビルド / Production build
4. 動作確認 / Verify functionality
5. Git にコミット・プッシュ / Git commit and push

### FTPデプロイ時 / FTP Deployment
1. `npm run ftp:package` でパッケージ作成 / Create package
2. 生成されたZIPファイルをFTPでアップロード / Upload ZIP via FTP
3. サーバーでZIPファイルを展開 / Extract ZIP on server
4. ブラウザで動作確認 / Verify in browser

## 利用可能なコマンド / Available Commands

| Command | Description (Japanese) | Description (English) |
|---------|----------------------|----------------------|
| `npm run dev` | 開発サーバー起動 | Start development server |
| `npm run build:prod` | 本番ビルド | Production build |
| `npm run build:test` | テストビルド | Test build |
| `npm run build:clean-test` | テストファイル削除 | Clean test files |
| `npm run build:restore` | バックアップ復元 | Restore from backup |
| `npm run ftp:package` | FTPパッケージ作成 | Create FTP package |
| `npm run clean` | 全クリーンアップ | Clean all |

## よくある質問 / FAQ

### Q: ビルドに失敗した / Build failed
**A:** 自動でバックアップから復元されます。エラーメッセージを確認して修正後、再度実行してください。
**A:** Auto restore from backup. Check error message, fix issues, and retry.

### Q: 古いバックアップファイルがたまる / Old backup files accumulate
**A:** `src/public/build-vite.backup.*` のファイルは手動で削除できます。
**A:** You can manually delete `src/public/build-vite.backup.*` files.

### Q: 開発中にビルドファイルが必要？/ Need build files during development?
**A:** 不要です。`npm run dev` で開発サーバーを使用してください。
**A:** No. Use `npm run dev` for development server.

### Q: どのファイルをFTPでアップロードする？/ Which files to upload via FTP?
**A:** `npm run ftp:package` で作成されるZIPファイルのみアップロードしてください。
**A:** Only upload the ZIP file created by `npm run ftp:package`.

## トラブルシューティング / Troubleshooting

### エラー: "package.json が見つかりません" / Error: "package.json not found"
→ プロジェクトルートフォルダで実行してください / Run from project root directory

### エラー: "npm install に失敗" / Error: "npm install failed"
→ Node.js とnpm が正しくインストールされているか確認してください / Verify Node.js and npm installation

### エラー: "ビルドファイルが見つかりません" / Error: "Build files not found"
→ 先に `npm run build:prod` を実行してください / Run `npm run build:prod` first

### 実行権限エラー / Permission errors
```bash
# 実行権限を付与 / Grant execution permissions
chmod +x scripts/build/*.sh scripts/deploy/*.sh
```