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
        
        // 横スクロールバー除去（縦スクロール維持）
        initScrollDisabling();
        
        // 高さを動的に調整
        initIframeHeightAdjustment();
        
        console.log('Running in iframe mode');
    }
}

/**
 * iframe内での横スクロールバー除去（縦スクロールは維持）
 */
function initScrollDisabling() {
    // HTML・Body要素の横スクロールのみ無効化
    const htmlElement = document.documentElement;
    const bodyElement = document.body;
    
    // 横スクロールバーのみ除去、縦は自然なスクロール維持
    htmlElement.style.overflowX = 'hidden';
    htmlElement.style.overflowY = 'auto';
    htmlElement.style.margin = '0';
    htmlElement.style.padding = '0';
    
    bodyElement.style.overflowX = 'hidden';
    bodyElement.style.overflowY = 'auto';
    bodyElement.style.margin = '0';
    bodyElement.style.padding = '0';
    bodyElement.style.minWidth = '100%';
    bodyElement.style.maxWidth = '100%';
    
    // main-contentのレスポンシブ調整
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.style.overflowX = 'hidden';
        mainContent.style.overflowY = 'visible';
        mainContent.style.width = '100%';
        mainContent.style.maxWidth = '100%';
        mainContent.style.boxSizing = 'border-box';
    }
}

/**
 * iframe高さ自動調整機能
 */
function initIframeHeightAdjustment() {
    let lastHeight = 0; // 前回の高さを記録
    let updateTimer = null; // デバウンス用タイマー
    let isUpdating = false; // 更新中フラグ
    
    // 高さ計算と通知関数
    function updateIframeHeight() {
        if (window.self !== window.top && !isUpdating) {
            isUpdating = true;
            
            // コンテンツの実際の高さを計算
            const height = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.scrollHeight,
                document.documentElement.offsetHeight
            );
            
            // 余白を追加（スクロールバー分など）
            const adjustedHeight = height + 20;
            
            // 前回と同じ高さなら更新しない（無限ループ防止）
            if (Math.abs(adjustedHeight - lastHeight) <= 10) {
                isUpdating = false;
                return;
            }
            
            lastHeight = adjustedHeight;
            
            // 親ウィンドウに高さを通知
            window.parent.postMessage({
                type: 'iframe-height-update',
                height: adjustedHeight,
                timestamp: Date.now()
            }, '*');
            
            console.log('iframe高さ更新通知:', adjustedHeight + 'px');
            
            // 短時間のロック
            setTimeout(() => {
                isUpdating = false;
            }, 200);
        }
    }
    
    // デバウンス機能付き更新関数
    function debouncedUpdate() {
        if (updateTimer) {
            clearTimeout(updateTimer);
        }
        updateTimer = setTimeout(updateIframeHeight, 300); // 300msに延長
    }
    
    // 初期高さ設定のみ（1回だけ）
    setTimeout(() => {
        updateIframeHeight();
    }, 500);
    
    // DOMContentLoadedとloadイベントのみ
    document.addEventListener('DOMContentLoaded', debouncedUpdate);
    window.addEventListener('load', debouncedUpdate);
    
    // resizeイベントは削除（iframe高さ変更が原因）
    // window.addEventListener('resize', debouncedUpdate);
    
    // 画像読み込み完了時のみ更新
    window.addEventListener('load', (event) => {
        if (event.target.tagName === 'IMG') {
            setTimeout(debouncedUpdate, 300);
        }
    }, true);
    
    // 最小限のDOM変更監視（新しい要素追加のみ）
    const observer = new MutationObserver((mutations) => {
        let hasNewElements = false;
        
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                for (let node of mutation.addedNodes) {
                    if (node.nodeType === Node.ELEMENT_NODE && node.offsetHeight > 0) {
                        hasNewElements = true;
                        break;
                    }
                }
            }
        });
        
        if (hasNewElements) {
            debouncedUpdate();
        }
    });
    
    // 監視対象を最小限に制限
    const calendarContainer = document.querySelector('.calendar-container') || 
                             document.querySelector('.main-content') || 
                             document.body;
    
    observer.observe(calendarContainer, {
        childList: true,
        subtree: false,
        attributes: false
    });
}

/**
 * レスポンシブ機能の初期化
 */
function initResponsiveFeatures() {
    // レスポンシブ対応のため、リサイズ時の処理
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            console.log('Window resized, updating responsive features');
        }, 250);
    });
}

/**
 * アラートメッセージの自動非表示
 */
function initAutoHideAlerts() {
    // 成功・エラーメッセージの自動非表示
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000); // 5秒後に非表示
    });
}

/**
 * フォーカス管理（アクセシビリティ対応）
 */
function initFocusManagement() {
    // キーボードナビゲーション向上
    document.addEventListener('keydown', (event) => {
        // ESCキーでモーダルクローズなど
        if (event.key === 'Escape') {
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                // モーダルクローズ処理
            }
        }
    });
}