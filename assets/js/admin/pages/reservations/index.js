/**
 * 予約一覧ページ JavaScript
 * ファイル名: assets/js/admin/pages/reservations/index.js
 */

/**
 * 予約一覧ページの初期化
 */
export function initReservationsIndex() {
    console.log('Reservations Index page initialized.');
    
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
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    
    const form = document.getElementById('search-form');
    if (!form) return;
    
    switch (quickType) {
        case 'today':
            setDateInputValue(form, 'desired_date_from', formatDate(today));
            setDateInputValue(form, 'desired_date_to', formatDate(today));
            break;
        case 'tomorrow':
            setDateInputValue(form, 'desired_date_from', formatDate(tomorrow));
            setDateInputValue(form, 'desired_date_to', formatDate(tomorrow));
            break;
        case 'this_week':
            const startOfWeek = getStartOfWeek(today);
            const endOfWeek = getEndOfWeek(today);
            setDateInputValue(form, 'desired_date_from', formatDate(startOfWeek));
            setDateInputValue(form, 'desired_date_to', formatDate(endOfWeek));
            break;
    }
}

/**
 * 検索フォーム機能
 */
function initSearchForm() {
    const form = document.getElementById('search-form');
    if (!form) return;
    
    // ページ番号をリセットして検索
    form.addEventListener('submit', function() {
        const pageInput = form.querySelector('input[name="page"]');
        if (pageInput) {
            pageInput.value = '1';
        }
    });
    
    // 検索実行ボタン
    const searchBtn = document.getElementById('search-btn');
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
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
            // ページリロードまたは該当行の更新
            location.reload();
        } else {
            showNotification(data.message || '更新に失敗しました', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('通信エラーが発生しました', 'error');
    });
}

/**
 * レスポンシブ対応機能
 */
function initResponsiveFeatures() {
    // スマートフォン表示での横スクロール対応など
    handleTableResponsive();
    
    // 検索フォームの折りたたみ機能
    handleSearchFormCollapse();
}

// === ユーティリティ関数 ===

/**
 * 日付をYYYY-MM-DD形式でフォーマット
 */
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * 週の開始日を取得（月曜日）
 */
function getStartOfWeek(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1); // 日曜日の場合は-6、それ以外は1
    return new Date(d.setDate(diff));
}

/**
 * 週の終了日を取得（日曜日）
 */
function getEndOfWeek(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = d.getDate() + (7 - day);
    return new Date(d.setDate(diff));
}

/**
 * フォームの日付入力フィールドに値を設定
 */
function setDateInputValue(form, fieldName, value) {
    const input = form.querySelector(`input[name="${fieldName}"]`);
    if (input) {
        input.value = value;
    }
}

/**
 * クイック検索ボタンのフィードバック表示
 */
function showQuickSearchFeedback(button, quickType) {
    const originalText = button.textContent;
    button.textContent = '検索中...';
    button.disabled = true;
    
    setTimeout(() => {
        button.textContent = originalText;
        button.disabled = false;
    }, 1000);
}

/**
 * 通知メッセージ表示
 */
function showNotification(message, type = 'info') {
    // Bootstrap Alertまたはカスタム通知システムを使用
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // 通知エリアに追加（ページ上部など）
    const container = document.querySelector('.page-content') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // 自動削除
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

/**
 * テーブルのレスポンシブ対応
 */
function handleTableResponsive() {
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}

/**
 * 検索フォームの折りたたみ処理
 */
function handleSearchFormCollapse() {
    const collapseBtn = document.querySelector('[data-bs-toggle="collapse"]');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon) {
                // アイコンの切り替え
                setTimeout(() => {
                    const target = document.querySelector(this.getAttribute('data-bs-target'));
                    if (target && target.classList.contains('show')) {
                        icon.className = 'bi bi-chevron-up';
                    } else {
                        icon.className = 'bi bi-chevron-down';
                    }
                }, 300);
            }
        });
    }
}