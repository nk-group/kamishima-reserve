<?php

// app/Helpers/vite_helper.php

//use CodeIgniter\Files\Exceptions\FileNotFoundException;

if (! function_exists('vite_tags')) {
    /**
     * Vite (laravel-vite-plugin 使用前提) のための <script> および <link> タグを生成します。
     * 開発モードと本番モードを自動で判別します。
     *
     * @param string|string[] $entrypoints エントリーポイントのファイルパス（単数または複数）
     * 例: 'assets/js/app.js' または ['assets/js/user.js', 'assets/css/calendar-page.scss']
     * プロジェクトルートからの相対パス。
     * @param string|null     $buildDirectory ビルドディレクトリ名 (vite.config.js の buildDirectory と合わせる)
     * nullの場合は 'build-vite' が使用されます。
     * @return string 生成されたHTMLタグ文字列
     */
    function vite_tags(string|array $entrypoints, ?string $buildDirectory = null): string
    {
        static $manifestCache = null; // マニフェストを静的キャッシュ
        $html = '';
        $buildDir = $buildDirectory ?? 'build-vite';

        $entrypoints = (array) $entrypoints;

        // --- 開発モード (ENVIRONMENT === 'development') ---
        if (ENVIRONMENT === 'development') {
            $viteDevServer = env('VITE_DEV_SERVER', 'http://localhost:5173');
            $viteDevServer = rtrim($viteDevServer, '/');

            $html .= '<script type="module" src="' . $viteDevServer . '/@vite/client"></script>' . "\n";

            foreach ($entrypoints as $entry) {
                // 開発モードでは、ViteがJSもCSSもエントリーとして適切に処理するため、
                // そのままのパスでscriptタグとして読み込みを試みます。
                // (laravel-vite-pluginがCSSエントリーもこのように扱うため)
                $html .= '<script type="module" src="' . $viteDevServer . '/' . ltrim($entry, '/') . '"></script>' . "\n";
            }
            return $html;
        }

        // --- 本番モード (production など) ---
        if ($manifestCache === null) {
            $manifestPath = FCPATH . $buildDir . DIRECTORY_SEPARATOR . 'manifest.json';

            if (!is_file($manifestPath)) {
                log_message('error', "[ViteTagsHelper] Manifest not found at: {$manifestPath}. Did you run 'npm run build'?");
                return "";
            }
            $manifestContent = file_get_contents($manifestPath);
            if ($manifestContent === false) {
                log_message('error', "[ViteTagsHelper] Could not read manifest file: {$manifestPath}");
                return "";
            }
            $manifestData = json_decode($manifestContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', '[ViteTagsHelper] Failed to parse manifest.json. Error: ' . json_last_error_msg());
                return "";
            }
            $manifestCache = $manifestData;
        }

        $tags = [];
        $processedAssets = []; // CSSやJSの重複出力を防ぐ

        foreach ($entrypoints as $originalEntry) {
            $entryKey = ltrim($originalEntry, '/');

            if (!isset($manifestCache[$entryKey])) {
                log_message('warning', "[ViteTagsHelper] Entrypoint '{$entryKey}' not found in manifest.json.");
                $tags[] = "";
                continue;
            }

            $entryData = $manifestCache[$entryKey];

            // 1. JSエントリーに紐づくCSSファイル (manifestの 'css' 配列)
            if (!empty($entryData['css'])) {
                foreach ($entryData['css'] as $cssFile) {
                    if (!isset($processedAssets[$cssFile])) {
                        $tags[] = link_tag($buildDir . '/' . $cssFile);
                        $processedAssets[$cssFile] = true;
                    }
                }
            }

            // 2. エントリーファイル自体 (manifestの 'file' キー)
            if (!empty($entryData['file'])) {
                $assetPath = $entryData['file'];
                if (!isset($processedAssets[$assetPath])) {
                    $fullAssetPath = base_url($buildDir . '/' . $assetPath);
                    if (preg_match('/\.css$/i', $assetPath)) {
                        $tags[] = link_tag($buildDir . '/' . $assetPath); // CSSファイルの場合
                    } elseif (preg_match('/\.js$/i', $assetPath)) {
                        $tags[] = script_tag(['src' => $fullAssetPath, 'type' => 'module']); // JSファイルの場合
                    }
                    $processedAssets[$assetPath] = true;
                }
            }

            // 3. imports (チャンクや依存関係) - CSSとJSを処理
            if (!empty($entryData['imports'])) {
                foreach ($entryData['imports'] as $importKey) {
                    if (isset($manifestCache[$importKey])) {
                        $importedEntryData = $manifestCache[$importKey];
                        // インポートされたチャンクのCSS
                        if (!empty($importedEntryData['css'])) {
                            foreach ($importedEntryData['css'] as $cssFile) {
                                if (!isset($processedAssets[$cssFile])) {
                                    $tags[] = link_tag($buildDir . '/' . $cssFile);
                                    $processedAssets[$cssFile] = true;
                                }
                            }
                        }
                        // インポートされたチャンクのJS (modulepreloadとして)
                        if (!empty($importedEntryData['file']) && preg_match('/\.js$/i', $importedEntryData['file'])) {
                            if (!isset($processedAssets[$importedEntryData['file']])) { // preloadの重複も避ける
                                $tags[] = '<link rel="modulepreload" href="' . base_url($buildDir . '/' . $importedEntryData['file']) . '">';
                                // ここでは $processedAssets に preload したJSも記録するかはポリシーによる
                                // $processedAssets[$importedEntryData['file']] = true;
                            }
                        }
                    }
                }
            }
        }
        return implode("\n", array_unique($tags)); // 重複タグを最終的に排除
    }
}