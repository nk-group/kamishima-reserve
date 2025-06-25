// assets/js/admin/pages/reservations-list.js

/**
 * 予約一覧ページのJavaScript機能
 */

// ページロード時に実行
document.addEventListener('DOMContentLoaded', function() {
    initReservationList();
});

/**
 * 予約一覧ページの初期化
 */
function initReservationList() {
    console.log('Reservations list page initialized');
    
    // データ取得
    const listData = window.reservationListData || {};
    console.log('List data:', listData);
    
    // 各機能を初期化
    initQuickSearch();
    initSearchForm();
    initClearButton();
    initClipboardCopy();
    initCompleteButtons();
    initResponsiveFeatures();
}

/**
 * クイック検索機能
 */
function initQuickSearch() {
    const quickButtons = document.querySelectorAll('.btn-quick-small');
    
    quickButtons.forEach(button => {
        button.addEventListener('click', function() {
            const quickType = this.dataset.quick;
            console.log('Quick search:', quickType);
            
            // 検索フォームをクリア
            clearSearchForm();
            
            // クイック検索パラメータを設定
            const form = document.getElementById('search-form');
            if (!form) return;
            
            // 隠しフィールドでクイック検索タイプを送信
            let quickInput = form.querySelector('input[name="quick_search"]');
            if (!quickInput) {
                quickInput = document.createElement('input');
                quickInput.type = 'hidden';
                quickInput.name = 'quick_search';
                form.appendChild(quickInput);
            }
            quickInput.value = quickType;
            
            // 日付条件を設定（必要に応じて）
            setQuickSearchConditions(quickType);
            
            // ボタンのフィードバック
            showQuickSearchFeedback(this, quickType);
            
            // フォーム送信
            setTimeout(() => {
                form.submit();
            }, 300);
        });
    });
}

/**
 * クイック検索条件設定
 */
function setQuickSearchConditions(quickType) {
    const today = new Date();
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');
    
    switch (quickType) {
        case 'today':
            // 本日
            const todayStr = today.toISOString().split('T')[0];
            if (dateFromInput) dateFromInput.value = todayStr;
            if (dateToInput) dateToInput.value = todayStr;
            break;
            
        case 'this_month_completed':
            // 今月
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            if (dateFromInput) dateFromInput.value = firstDay.toISOString().split('T')[0];
            if (dateToInput) dateToInput.value = lastDay.toISOString().split('T')[0];
            break;
    }
}

/**
 * 検索フォーム機能
 */
function initSearchForm() {
    const form = document.getElementById('search-form');
    if (!form) return;
    
    // Enterキーでの送信を有効化
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            // 検索ボタンがあればそれをクリック、なければフォーム送信
            const searchBtn = form.querySelector('.btn-search');
            if (searchBtn) {
                e.preventDefault();
                searchBtn.click();
            }
        }
    });
    
    // 検索ボタンのクリック処理
    const searchBtn = form.querySelector('.btn-search');
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // クイック検索パラメータをクリア
            const quickInput = form.querySelector('input[name="quick_search"]');
            if (quickInput) {
                quickInput.remove();
            }
            
            // ページ番号をリセット
            const pageInput = form.querySelector('input[name="page"]');
            if (pageInput) {
                pageInput.value = '1';
            }
            
            form.submit();
        });
    }
}

/**
 * 条件クリア機能
 */
function initClearButton() {
    const clearBtn = document.getElementById('clear-search');
    if (!clearBtn) return;
    
    clearBtn.addEventListener('click', function() {
        if (confirm('検索条件をクリアしてもよろしいですか？')) {
            clearSearchForm();
            
            // フォーム送信（条件なしで検索）
            const form = document.getElementById('search-form');
            if (form) {
                form.submit();
            }
        }
    });
}

/**
 * 検索フォームクリア
 */
function clearSearchForm() {
    const form = document.getElementById('search-form');
    if (!form) return;
    
    // テキスト入力をクリア
    form.querySelectorAll('input[type="text"]').forEach(input => {
        input.value = '';
    });
    
    // セレクトボックスをリセット
    form.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });
    
    // チェックボックスをクリア
    form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // 隠しフィールドを削除
    form.querySelectorAll('input[type="hidden"]').forEach(hidden => {
        if (hidden.name !== '_token') { // CSRFトークンは残す
            hidden.remove();
        }
    });
}

/**
 * クリップボードコピー機能
 */
function initClipboardCopy() {
    const copyBtn = document.getElementById('copy-to-clipboard');
    if (!copyBtn) return;
    
    copyBtn.addEventListener('click', function() {
        const table = document.querySelector('.table tbody');
        if (!table) {
            showNotification('コピーするデータがありません', 'warning');
            return;
        }
        
        let csvText = '予約番号,予約状況,予約希望日,お名前,車種,車番,作業種別,作業店舗\n';
        
        table.querySelectorAll('tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 8) {
                const rowData = [
                    cells[0].textContent.trim(), // 予約番号
                    cells[1].textContent.trim(), // 予約状況
                    cells[2].textContent.trim(), // 予約希望日
                    cells[3].textContent.trim(), // お名前
                    cells[4].textContent.trim(), // 車種
                    cells[5].textContent.trim(), // 車番
                    cells[6].textContent.trim(), // 作業種別
                    cells[7].textContent.trim()  // 作業店舗
                ];
                csvText += rowData.join(',') + '\n';
            }
        });
        
        // クリップボードにコピー
        navigator.clipboard.writeText(csvText).then(() => {
            showNotification('データをクリップボードにコピーしました', 'success');
        }).catch(err => {
            console.error('クリップボードコピー失敗:', err);
            showNotification('クリップボードコピーに失敗しました', 'error');
        });
    });
}

/**
 * 完了ボタン機能
 */
function initCompleteButtons() {
    document.querySelectorAll('.btn-complete').forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.onclick.toString().match(/\d+/)[0];
            markAsComplete(reservationId);
        });
    });
}

/**
 * 予約を完了状態にする
 */
function markAsComplete(reservationId) {
    if (!confirm('この予約を完了状態にしてもよろしいですか？')) {
        return;
    }
    
    // Ajax request to update status
    fetch(`/admin/reservations/update/${reservationId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            'reservation_status_id': '3', // 完了状態
            '_token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('予約を完了状態に更新しました', 'success');
            // ページをリロード
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification('更新に失敗しました', 'error');
        }
    })
    .catch(error => {
        console.error('更新エラー:', error);
        showNotification('更新に失敗しました', 'error');
    });
}

/**
 * レスポンシブ機能
 */
function initResponsiveFeatures() {
    // モバイルでのテーブル横スクロール表示
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        // タッチスクロールのヒント表示
        if (window.innerWidth <= 768) {
            const scrollHint = document.createElement('div');
            scrollHint.className = 'alert alert-info d-md-none';
            scrollHint.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>表は左右にスクロールできます';
            tableContainer.parentNode.insertBefore(scrollHint, tableContainer);
        }
    }
}

/**
 * クイック検索フィードバック表示
 */
function showQuickSearchFeedback(button, quickType) {
    const originalText = button.textContent;
    const originalClass = button.className;
    
    button.textContent = '検索中...';
    button.disabled = true;
    
    setTimeout(() => {
        button.textContent = originalText;
        button.className = originalClass;
        button.disabled = false;
    }, 1000);
}

/**
 * 通知表示
 */
function showNotification(message, type = 'info') {
    // 既存の通知があれば削除
    const existingAlert = document.querySelector('.notification-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // 通知要素を作成
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show notification-alert`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // 3秒後に自動削除
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}