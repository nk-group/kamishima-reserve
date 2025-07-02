//==========================================================================
// Customer Main JavaScript Entry Point - Clear車検予約システム顧客向け
//==========================================================================

/**
 * DOMContentLoaded時にページ固有のJavaScriptを読み込み
 * admin.jsの構造に準拠した動的インポートシステム
 */
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Customer JavaScript initialized');
    
    // body要素のIDを取得してページを判定
    const bodyId = document.body.id;
    console.log('Detected page body ID:', bodyId);
    
    // ページ固有のスクリプトを動的インポート
    switch (bodyId) {
        // === カレンダー関連 ===
        case 'page-customer-calendar-month':
            try {
                const { initCalendarMonth } = await import('./customer/pages/calendar/month.js');
                initCalendarMonth();
            } catch (e) {
                console.error('Failed to load calendar month scripts:', e);
            }
            break;

        case 'page-customer-calendar-week':
            try {
                const { initCalendarWeek } = await import('./customer/pages/calendar/week.js');
                initCalendarWeek();
            } catch (e) {
                console.error('Failed to load calendar week scripts:', e);
            }
            break;

        // === 予約フォーム ===
        case 'page-customer-reservation-form':
            try {
                const { initReservationForm } = await import('./customer/pages/reservation/form.js');
                initReservationForm();
            } catch (e) {
                console.error('Failed to load reservation form scripts:', e);
            }
            break;

        // === 予約状況確認 ===
        case 'page-customer-reservation-status':
            try {
                const { initReservationStatus } = await import('./customer/pages/reservation/status.js');
                initReservationStatus();
            } catch (e) {
                console.error('Failed to load reservation status scripts:', e);
            }
            break;

        default:
            // 不明なページID、またはJavaScript不要なページ
            console.log('No specific JavaScript required for this page:', bodyId);
            break;
    }
    
    // 全ページ共通の初期化処理
    initCommonFeatures();
});

/**
 * 全ページ共通の初期化処理
 * iframe環境とレスポンシブ対応を含む
 */
function initCommonFeatures() {
    // iframe環境の検出と高さ調整
    initIframeSupport();
    
    // レスポンシブ対応
    initResponsiveFeatures();
    
    // アラートメッセージの自動非表示
    initAutoHideAlerts();
    
    // フォーカス管理（アクセシビリティ対応）
    initFocusManagement();
}

/**
 * iframe環境サポートの初期化
 */
function initIframeSupport() {
    // iframe内かどうかを判定
    const isInIframe = window.self !== window.top;
    
    if (isInIframe) {
        // iframe用のCSSクラスを追加
        document.body.classList.add('iframe-mode');
        
        // 高さを動的に調整
        initIframeHeightAdjustment();
        
        console.log('Running in iframe mode');
    }
}

/**
 * iframe高さ調整の初期化
 */
function initIframeHeightAdjustment() {
    function adjustIframeHeight() {
        const height = Math.max(
            document.documentElement.scrollHeight,
            document.body.scrollHeight,
            400 // 最小高さ
        );
        
        // 親フレームに高さを通知
        if (window.parent && window.parent.postMessage) {
            window.parent.postMessage({
                type: 'iframe-height-update',
                height: height
            }, '*');
        }
    }
    
    // 初回実行
    adjustIframeHeight();
    
    // DOM変更時に再実行
    const observer = new MutationObserver(adjustIframeHeight);
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true
    });
    
    // リサイズ時に再実行
    window.addEventListener('resize', adjustIframeHeight);
}

/**
 * レスポンシブ機能の初期化
 */
function initResponsiveFeatures() {
    // タッチデバイスの判定
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
    }
    
    // モバイル判定（簡易）
    const isMobile = window.innerWidth <= 768;
    if (isMobile) {
        document.body.classList.add('mobile-device');
    }
}

/**
 * アラートメッセージの自動非表示
 */
function initAutoHideAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-hide]');
    
    alerts.forEach(alert => {
        const hideDelay = parseInt(alert.dataset.autoHide) || 5000;
        
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, hideDelay);
    });
}

/**
 * フォーカス管理の初期化（アクセシビリティ対応）
 */
function initFocusManagement() {
    // Skip linkの実装
    const skipLink = document.querySelector('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.focus();
                target.scrollIntoView();
            }
        });
    }
    
    // キーボードナビゲーションの改善
    document.addEventListener('keydown', function(e) {
        // Escキーでモーダルを閉じる
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const closeBtn = modal.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                }
            });
        }
    });
}