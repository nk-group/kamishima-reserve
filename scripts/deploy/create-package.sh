#!/bin/bash

# FTP用パッケージ作成スクリプト（簡潔版）
# 配置場所: scripts/deploy/create-package.sh
# 実行方法: npm run ftp:package

# プロジェクトルートに移動
cd "$(dirname "$0")/../.."

echo "📦 FTPアップロード用パッケージ作成"
echo "================================"

# メッセージ表示用関数
success() { echo -e "\033[32m✅ $1\033[0m"; }
error() { echo -e "\033[31m❌ $1\033[0m"; }
info() { echo -e "\033[34mℹ️  $1\033[0m"; }

# プロジェクトルート確認
if [ ! -f "package.json" ]; then
    error "package.json が見つかりません。プロジェクトルートで実行してください。"
    exit 1
fi

# 不要ファイルの削除関数（先に定義）
cleanup_files() {
    local package_dir=$1
    
    info "クリーンアップ中..."
    
    # ログとキャッシュをクリア
    find "$package_dir" -path "*/writable/logs/*" ! -name "index.html" -delete 2>/dev/null || true
    find "$package_dir" -path "*/writable/cache/*" ! -name "index.html" -delete 2>/dev/null || true
    find "$package_dir" -path "*/writable/session/*" ! -name "index.html" -delete 2>/dev/null || true
    
    # 不要なファイルを削除
    find "$package_dir" -name ".DS_Store" -delete 2>/dev/null || true
    find "$package_dir" -name "*.log" -delete 2>/dev/null || true
    find "$package_dir" -name "*.tmp" -delete 2>/dev/null || true
    find "$package_dir" -name "*.bak" -delete 2>/dev/null || true
    
    # 環境設定ファイルを削除（本番では別途設定）
    rm -f "$package_dir/.env" 2>/dev/null || true
    rm -f "$package_dir/.env.local" 2>/dev/null || true
}

# 完全パッケージの作成関数
create_full_package() {
    info "完全パッケージを作成中..."
    
    PACKAGE_NAME="ftp-complete-${TIMESTAMP}"
    
    # 一時ディレクトリ作成
    mkdir "$PACKAGE_NAME"
    
    # アプリケーション全体をコピー
    info "ファイルをコピー中..."
    cp -r src/ "$PACKAGE_NAME/"
    
    # 不要ファイルの削除
    info "不要なファイルを削除中..."
    cleanup_files "$PACKAGE_NAME"
    
    # 使用方法の説明ファイルを追加
    cat > "$PACKAGE_NAME/アップロード手順.txt" << 'EOF'
FTPアップロード手順
================

1. このフォルダの中身をすべてサーバーにアップロードしてください

2. 以下のフォルダが書き込み可能か確認してください:
   - writable/logs/
   - writable/cache/
   - writable/session/
   - writable/uploads/

3. .env ファイルを本番環境用に設定してください

4. ブラウザでアクセスして動作確認してください

注意: public フォルダがドキュメントルートになるようにしてください
EOF
    
    # 圧縮
    info "圧縮中..."
    zip -r "${PACKAGE_NAME}.zip" "$PACKAGE_NAME" > /dev/null
    rm -rf "$PACKAGE_NAME"
    
    success "完全パッケージを作成しました: ${PACKAGE_NAME}.zip"
}

# アセットのみパッケージの作成関数
create_assets_package() {
    info "アセットパッケージを作成中..."
    
    # ビルドファイルの存在確認
    if [ ! -d "src/public/build-vite" ]; then
        error "ビルドファイルが見つかりません。先に npm run build:prod を実行してください。"
        exit 1
    fi
    
    PACKAGE_NAME="ftp-assets-${TIMESTAMP}"
    
    # アセット用ディレクトリ作成
    mkdir -p "$PACKAGE_NAME/public"
    
    # ビルド成果物のみコピー
    info "ビルドファイルをコピー中..."
    cp -r src/public/build-vite "$PACKAGE_NAME/public/"
    
    # アップロード手順書を追加
    cat > "$PACKAGE_NAME/アップロード手順.txt" << 'EOF'
アセットファイルアップロード手順
============================

このパッケージにはCSS/JavaScriptファイルのみが含まれています。

1. public/build-vite/ フォルダをサーバーにアップロードしてください

2. アップロード先: あなたのサイト/public/build-vite/

3. ブラウザでサイトにアクセスして、デザインが正しく表示されるか確認してください

4. 問題がある場合は、ブラウザのキャッシュをクリアしてください
EOF
    
    # 圧縮
    info "圧縮中..."
    zip -r "${PACKAGE_NAME}.zip" "$PACKAGE_NAME" > /dev/null
    rm -rf "$PACKAGE_NAME"
    
    success "アセットパッケージを作成しました: ${PACKAGE_NAME}.zip"
}

# パッケージタイプの選択
echo "作成するパッケージタイプを選択してください:"
echo "1) 完全パッケージ（アプリケーション全体）"
echo "2) アセットのみ（CSS/JSファイルのみ）"
echo ""
read -p "番号を入力してください (1-2): " PACKAGE_TYPE

# タイムスタンプ
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

case $PACKAGE_TYPE in
    1)
        create_full_package
        ;;
    2)
        create_assets_package
        ;;
    *)
        error "無効な選択です"
        exit 1
        ;;
esac

echo ""
info "作成されたZIPファイルをFTPでサーバーにアップロードしてください"

# 完全パッケージの作成
create_full_package() {
    info "完全パッケージを作成中..."
    
    PACKAGE_NAME="ftp-complete-${TIMESTAMP}"
    
    # 一時ディレクトリ作成
    mkdir "$PACKAGE_NAME"
    
    # アプリケーション全体をコピー
    info "ファイルをコピー中..."
    cp -r src/ "$PACKAGE_NAME/"
    
    # 不要ファイルの削除
    info "不要なファイルを削除中..."
    cleanup_files "$PACKAGE_NAME"
    
    # 使用方法の説明ファイルを追加
    cat > "$PACKAGE_NAME/アップロード手順.txt" << 'EOF'
FTPアップロード手順
================

1. このフォルダの中身をすべてサーバーにアップロードしてください

2. 以下のフォルダが書き込み可能か確認してください:
   - writable/logs/
   - writable/cache/
   - writable/session/
   - writable/uploads/

3. .env ファイルを本番環境用に設定してください

4. ブラウザでアクセスして動作確認してください

注意: public フォルダがドキュメントルートになるようにしてください
EOF
    
    # 圧縮
    info "圧縮中..."
    zip -r "${PACKAGE_NAME}.zip" "$PACKAGE_NAME" > /dev/null
    rm -rf "$PACKAGE_NAME"
    
    success "完全パッケージを作成しました: ${PACKAGE_NAME}.zip"
}

# アセットのみパッケージの作成
create_assets_package() {
    info "アセットパッケージを作成中..."
    
    # ビルドファイルの存在確認
    if [ ! -d "src/public/build-vite" ]; then
        error "ビルドファイルが見つかりません。先に npm run build:prod を実行してください。"
        exit 1
    fi
    
    PACKAGE_NAME="ftp-assets-${TIMESTAMP}"
    
    # アセット用ディレクトリ作成
    mkdir -p "$PACKAGE_NAME/public"
    
    # ビルド成果物のみコピー
    info "ビルドファイルをコピー中..."
    cp -r src/public/build-vite "$PACKAGE_NAME/public/"
    
    # アップロード手順書を追加
    cat > "$PACKAGE_NAME/アップロード手順.txt" << 'EOF'
アセットファイルアップロード手順
============================

このパッケージにはCSS/JavaScriptファイルのみが含まれています。

1. public/build-vite/ フォルダをサーバーにアップロードしてください

2. アップロード先: あなたのサイト/public/build-vite/

3. ブラウザでサイトにアクセスして、デザインが正しく表示されるか確認してください

4. 問題がある場合は、ブラウザのキャッシュをクリアしてください
EOF
    
    # 圧縮
    info "圧縮中..."
    zip -r "${PACKAGE_NAME}.zip" "$PACKAGE_NAME" > /dev/null
    rm -rf "$PACKAGE_NAME"
    
    success "アセットパッケージを作成しました: ${PACKAGE_NAME}.zip"
}

# 不要ファイルの削除
cleanup_files() {
    local package_dir=$1
    
    info "クリーンアップ中..."
    
    # ログとキャッシュをクリア
    find "$package_dir" -path "*/writable/logs/*" ! -name "index.html" -delete 2>/dev/null || true
    find "$package_dir" -path "*/writable/cache/*" ! -name "index.html" -delete 2>/dev/null || true
    find "$package_dir" -path "*/writable/session/*" ! -name "index.html" -delete 2>/dev/null || true
    
    # 不要なファイルを削除
    find "$package_dir" -name ".DS_Store" -delete 2>/dev/null || true
    find "$package_dir" -name "*.log" -delete 2>/dev/null || true
    find "$package_dir" -name "*.tmp" -delete 2>/dev/null || true
    find "$package_dir" -name "*.bak" -delete 2>/dev/null || true
    
    # 環境設定ファイルを削除（本番では別途設定）
    rm -f "$package_dir/.env" 2>/dev/null || true
    rm -f "$package_dir/.env.local" 2>/dev/null || true
}

echo ""
info "作成されたZIPファイルをFTPでサーバーにアップロードしてください"