<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?? 'ja' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="<?= esc($meta_viewport ?? 'width=device-width, initial-scale=1.0') ?>">
    
    <?php if (isset($meta_robots)): ?>
    <meta name="robots" content="<?= esc($meta_robots) ?>">
    <?php endif; ?>
    
    <!-- プリコネクト設定 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- フォント読み込み -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title><?= $this->renderSection('title', esc($page_title ?? 'Clear車検予約 | 上嶋自動車')) ?></title>
    
    <?php // Vite 顧客向けアセットの読み込み ?>
    <?= vite_tags(['assets/scss/customer/customer.scss', 'assets/js/customer.js']) ?>
    
    <?php // iframe埋め込み対応CSS ?>
    <?php if ($is_iframe ?? false): ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: transparent;
        }
        .customer-reservation-wrapper {
            border: none;
            box-shadow: none;
            border-radius: 0;
        }
    </style>
    <?php endif; ?>
    
    <?= $this->renderSection('page_specific_head') ?>
</head>
<body id="<?= esc($body_id ?? 'page-customer-default') ?>" class="<?= $is_iframe ? 'iframe-mode' : 'standalone-mode' ?>">
    
    <!-- 顧客向けページ専用ラッパー（CSS競合回避） -->
    <div class="customer-reservation-wrapper">
        
        <?php // iframe以外の場合はヘッダーを表示 ?>
        <?php if (!($is_iframe ?? false)): ?>
            <?= $this->include('Partials/_customer_header') ?>
        <?php endif; ?>
        
        <!-- メインコンテンツエリア -->
        <main class="main-content">
            
            <?php // ページヘッダーセクション ?>
            <?= $this->renderSection('page_header_content') ?>
            
            <?php // アラートメッセージ表示 ?>
            <?= $this->include('Partials/_customer_alert_messages') ?>
            
            <?php // メインコンテンツセクション ?>
            <div class="content-container">
                <?= $this->renderSection('content') ?>
            </div>
            
        </main>
        
        <?php // iframe以外の場合はフッターを表示 ?>
        <?php if (!($is_iframe ?? false)): ?>
            <?= $this->include('Partials/_customer_footer') ?>
        <?php endif; ?>
        
    </div>
    
    <?php // ページ固有のスクリプト ?>
    <?= $this->renderSection('page_specific_scripts') ?>
    
    <?php // iframe対応のスクリプト ?>
    <?php if ($is_iframe ?? false): ?>
    <script>
        // 親ウィンドウとの通信機能（必要に応じて）
        window.addEventListener('message', function(event) {
            // 親サイトからのメッセージ処理
            if (event.origin !== window.location.origin) {
                return; // セキュリティ上、同一オリジンのみ許可
            }
            
            // メッセージハンドリング（今後の拡張用）
            console.log('Message from parent:', event.data);
        });
        
        // iframe内でのページ高さ調整
        function adjustIframeHeight() {
            const height = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.clientHeight,
                document.documentElement.scrollHeight,
                document.documentElement.offsetHeight
            );
            
            if (window.parent !== window) {
                window.parent.postMessage({
                    type: 'resize',
                    height: height
                }, '*');
            }
        }
        
        // ページ読み込み完了時とリサイズ時に高さ調整
        document.addEventListener('DOMContentLoaded', adjustIframeHeight);
        window.addEventListener('resize', adjustIframeHeight);
        
        // コンテンツ変更時にも高さ調整（MutationObserver使用）
        const observer = new MutationObserver(adjustIframeHeight);
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true
        });
    </script>
    <?php endif; ?>
    
</body>
</html>