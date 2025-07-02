//==========================================================================
// Customer Reservation Status Page JavaScript - Clear車検予約システム顧客向け
//==========================================================================

/**
 * 予約状況確認ページの初期化
 * admin構造に準拠したシンプルな実装
 */
export function initReservationStatus() {
    console.log('Customer Reservation Status page initialized');
    
    // DOM要素の取得
    const statusData = document.getElementById('reservation-status-data');
    const cancelBtn = document.getElementById('cancel-reservation-btn');
    const refreshBtn = document.getElementById('refresh-status-btn');
    
    if (!statusData) {
        console.error('Reservation status data element not found');
        return;
    }
    
    // 初期データの取得
    const reservationGuid = statusData.dataset.reservationGuid;
    const baseUrl = statusData.dataset.baseUrl || '/customer/reservation/status';
    
    // 機能の初期化
    initCancelReservation();
    initStatusRefresh();
    
    console.log('Reservation status initialized successfully');
    
    /**
     * 予約キャンセル機能の初期化
     */
    function initCancelReservation() {
        if (!cancelBtn) return;
        
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // キャンセル確認ダイアログ
            const confirmMessage = `予約をキャンセルしてもよろしいですか？\n\nキャンセル後は同じ条件での予約を保証できません。\nこの操作は取り消せません。`;
            
            if (confirm(confirmMessage)) {
                handleCancelReservation();
            }
        });
    }
    
    /**
     * 予約キャンセル処理
     */
    function handleCancelReservation() {
        console.log('Reservation cancellation initiated');
        
        // キャンセルボタンを無効化
        if (cancelBtn) {
            cancelBtn.disabled = true;
            cancelBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>キャンセル中...';
        }
        
        // ローディング表示
        showStatusLoading('予約をキャンセルしています...');
        
        const formData = new FormData();
        formData.append('action', 'cancel');
        formData.append('reservation_guid', reservationGuid);
        
        fetch(`${baseUrl}/${reservationGuid}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                handleCancelSuccess(data);
            } else {
                throw new Error(data.message || 'キャンセル処理に失敗しました');
            }
        })
        .catch(error => {
            console.error('Cancel reservation error:', error);
            handleCancelError(error.message);
        });
    }
    
    /**
     * キャンセル成功処理
     * @param {Object} data 
     */
    function handleCancelSuccess(data) {
        console.log('Reservation cancelled successfully');
        
        // 成功メッセージを表示
        showSuccessMessage('予約をキャンセルしました。');
        
        // ページ内容を更新
        if (data.html) {
            const statusContainer = document.querySelector('.reservation-status-container');
            if (statusContainer) {
                statusContainer.innerHTML = data.html;
            }
        }
        
        // iframe環境では親ウィンドウに通知
        if (window.self !== window.top && window.parent.postMessage) {
            window.parent.postMessage({
                type: 'reservation-cancelled',
                reservationGuid: reservationGuid
            }, '*');
        }
    }
    
    /**
     * キャンセルエラー処理
     * @param {string} message 
     */
    function handleCancelError(message) {
        console.error('Cancel reservation failed:', message);
        
        // エラーメッセージを表示
        showErrorMessage(`キャンセルに失敗しました: ${message}`);
        
        // キャンセルボタンを復元
        if (cancelBtn) {
            cancelBtn.disabled = false;
            cancelBtn.innerHTML = '予約をキャンセル';
        }
        
        hideStatusLoading();
    }
    
    /**
     * 状況更新機能の初期化
     */
    function initStatusRefresh() {
        if (!refreshBtn) return;
        
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleStatusRefresh();
        });
    }
    
    /**
     * 状況更新処理
     */
    function handleStatusRefresh() {
        console.log('Status refresh initiated');
        
        // 更新ボタンを無効化
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>更新中...';
        }
        
        // ローディング表示
        showStatusLoading('最新の状況を確認しています...');
        
        const params = new URLSearchParams({
            ajax: '1'
        });
        
        fetch(`${baseUrl}/${reservationGuid}?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.html) {
                    updateStatusContent(data);
                } else {
                    throw new Error(data.message || '状況の更新に失敗しました');
                }
            })
            .catch(error => {
                console.error('Status refresh error:', error);
                showErrorMessage('状況の更新中にエラーが発生しました。再度お試しください。');
            })
            .finally(() => {
                // 更新ボタンを復元
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '最新状況を確認';
                }
                hideStatusLoading();
            });
    }
    
    /**
     * 状況コンテンツの更新
     * @param {Object} data 
     */
    function updateStatusContent(data) {
        const statusContainer = document.querySelector('.reservation-status-container');
        if (statusContainer && data.html) {
            statusContainer.innerHTML = data.html;
            
            // 機能を再初期化
            initCancelReservation();
            initStatusRefresh();
        }
        
        console.log('Status content updated successfully');
    }
    
    /**
     * ローディング表示
     * @param {string} message 
     */
    function showStatusLoading(message = '読み込み中...') {
        const statusContainer = document.querySelector('.reservation-status-container');
        if (statusContainer) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'status-loading-overlay';
            loadingOverlay.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">${escapeHtml(message)}</div>
                </div>
            `;
            statusContainer.appendChild(loadingOverlay);
        }
    }
    
    /**
     * ローディング非表示
     */
    function hideStatusLoading() {
        const loadingOverlay = document.querySelector('.status-loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }
    
    /**
     * 成功メッセージ表示
     * @param {string} message 
     */
    function showSuccessMessage(message) {
        showAlertMessage('success', message);
    }
    
    /**
     * エラーメッセージ表示
     * @param {string} message 
     */
    function showErrorMessage(message) {
        showAlertMessage('error', message);
    }
    
    /**
     * アラートメッセージ表示
     * @param {string} type 
     * @param {string} message 
     */
    function showAlertMessage(type, message) {
        const alertContainer = document.querySelector('.alert-container') || 
                              document.querySelector('.reservation-status-container');
        
        if (!alertContainer) return;
        
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        
        const alertElement = document.createElement('div');
        alertElement.className = `alert ${alertClass} alert-dismissible fade show`;
        alertElement.innerHTML = `
            <div class="alert-icon">
                <i class="bi ${iconClass}"></i>
            </div>
            <div class="alert-content">
                ${escapeHtml(message)}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="閉じる"></button>
        `;
        
        // 既存のアラートを削除
        const existingAlerts = alertContainer.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // 新しいアラートを追加
        alertContainer.insertBefore(alertElement, alertContainer.firstChild);
        
        // 5秒後に自動非表示
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.classList.remove('show');
                setTimeout(() => alertElement.remove(), 150);
            }
        }, 5000);
    }
    
    /**
     * HTMLエスケープ
     * @param {string} text 
     * @returns {string}
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}