/**
 * 定休日マスタ共通機能 JavaScript
 * ファイル名: assets/js/admin/pages/shop-closing-days/common.js
 */

/**
 * 共通機能の初期化
 * 定休日マスタの全ページで使用される共通機能
 */
export function initCommonFeatures() {
    console.log('Shop Closing Days common features initialized.');
    
    // フラッシュメッセージの自動非表示
    initAutoHideAlerts();
    
    // ツールチップの初期化
    initTooltips();
}

/**
 * フラッシュメッセージの自動非表示
 */
function initAutoHideAlerts() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {
                // Bootstrap Alert が既に破棄されている場合のエラーを無視
                console.log('Alert already disposed:', e);
            }
        }, 5000);
    });
}

/**
 * ツールチップの初期化
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}