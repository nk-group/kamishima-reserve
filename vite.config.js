// kamishima-reserve/vite.config.js

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin'; // laravel-vite-plugin をインポート

export default defineConfig({
    plugins: [
        laravel({
            /**
             * CodeIgniterの公開ディレクトリ (ドキュメントルート) を指定します。
             * プロジェクトルートからの相対パスです。
             * 例: 'src/public'
             */
            publicDirectory: 'src/public',

            /**
             * ビルドされたアセットが出力されるディレクトリ名。
             * publicDirectory 内にこの名前で作成されます (例: src/public/build-vite/)。
             * PHPヘルパー側でこのパスを参照します。
             */
            buildDirectory: 'build-vite',

            /**
             * Viteが処理するエントリーポイントファイルを指定します。
             * プロジェクトルートからの相対パスで記述します。
             * ここで指定したファイルが manifest.json に記録されます。
             * JavaScriptファイル内で対応するCSS (SCSS) をインポートする構成を推奨します。
             */
            input: [
                'assets/js/app.js',       // 管理者向けJavaScriptエントリーポイント
                'assets/js/user.js'       // 利用者向けJavaScriptエントリーポイント
            ],

            /**
             * 開発時のホットリロード(HMR)を有効にするかどうか。
             * true にすると、ファイルの変更が即座にブラウザに反映されます。
             * PHP (Bladeなど) ファイルの変更時にもリロードをトリガーできますが、
             * CodeIgniterのビューファイルに対する設定は追加で必要になる場合があります。
             * シンプルにJS/CSSのHMRのためであれば、trueで問題ありません。
             */
            refresh: true,
        }),
        // 他にViteプラグインを使用する場合はここに追加します
        // 例: Reactプラグイン, Vueプラグインなど
        // import react from '@vitejs/plugin-react';
        // react(),
    ],

    // 開発サーバーの設定
    server: {
        /**
         * Dockerコンテナ内などでVite開発サーバーを実行している場合に、
         * ホストOSや他のデバイスからアクセスできるように '0.0.0.0' を指定します。
         * これにより、コンテナのポートをフォワーディングすれば外部からアクセス可能です。
         */
        host: '0.0.0.0',

        /**
         * Vite開発サーバーが使用するポート番号。
         */
        port: 5173, // デフォルト値

        /**
         * ホットリロード (HMR) の設定。
         * ブラウザがHMRのために接続する際のホスト名を指定します。
         * Docker環境などで、コンテナ内部の 'localhost' とホストOSの 'localhost' が
         * 異なる場合に明示的に指定すると解決することがあります。
         */
        hmr: {
            host: 'localhost',
        },

        /**
         * (オプション) WSL2環境などでファイルの変更検知が遅い場合に、
         * ポーリングを有効にすると改善することがあります。
         * 必要であれば以下のコメントを解除して有効化してください。
         */
        // watch: {
        //     usePolling: true,
        // }
    },

    /**
     * ビルド関連の設定
     * laravel-vite-plugin を使用する場合、基本的なビルド設定の多くは
     * プラグイン側で適切に処理されるため、詳細なカスタマイズが
     * 不要な場合はこの build オブジェクト自体を省略することも可能です。
     * manifest や emptyOutDir などはプラグインが面倒を見てくれます。
     */
    build: {
        // manifest: true, // laravel-vite-plugin がデフォルトで true にします
        // emptyOutDir: true, // laravel-vite-plugin が適切に処理します
        // rollupOptions: { // より詳細なRollupの設定が必要な場合
        //     output: {
        //         // 例: アセットのファイル名やチャンク名のカスタマイズ
        //         // entryFileNames: `assets/[name].js`, // ハッシュを付けない場合 (非推奨)
        //         // chunkFileNames: `assets/[name].js`,
        //         // assetFileNames: `assets/[name].[ext]`
        //     }
        // }
    },

    /**
     * (オプション) モジュール解決のエイリアス設定
     * import文で短いエイリアスパスを使えるようにします。
     * 例: import MyComponent from '@/components/MyComponent.js';
     * パスはプロジェクトルート基準で解決されるように設定するのが一般的です。
     * (pathモジュールのインポートが必要になる場合があります)
     */
    // resolve: {
    //     alias: {
    //         // '@': path.resolve(__dirname, 'assets') // Node.jsのpathモジュールを使用
    //     }
    // }
});