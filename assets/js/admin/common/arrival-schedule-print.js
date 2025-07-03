// assets/js/admin/common/arrival-schedule-print.js
import * as bootstrap from 'bootstrap';

/**
 * 入庫予定表印刷機能の初期化
 */
export function initArrivalSchedulePrint() {
    const printBtn = document.getElementById('arrivalSchedulePrintBtn');
    const modal = document.getElementById('arrivalSchedulePrintModal');
    const executePrintBtn = document.getElementById('executeArrivalSchedulePrintBtn');
    const form = document.getElementById('arrivalSchedulePrintForm');

    if (!printBtn || !modal) {
        return; // 必要な要素がない場合は処理を終了
    }

    // ヘッダメニューのクリック時の処理
    printBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openArrivalSchedulePrintModal();
    });

    // 印刷実行ボタンクリック時の処理
    if (executePrintBtn) {
        executePrintBtn.addEventListener('click', (e) => {
            e.preventDefault();
            executeArrivalSchedulePrint();
        });
    }

    // モーダル表示時の処理
    modal.addEventListener('show.bs.modal', () => {
        initializePrintForm();
    });
}

/**
 * 入庫予定表印刷モーダルを開く
 */
function openArrivalSchedulePrintModal() {
    const modal = new bootstrap.Modal(document.getElementById('arrivalSchedulePrintModal'));
    modal.show();
}

/**
 * 印刷フォームの初期化
 */
function initializePrintForm() {
    const printDateInput = document.getElementById('printDate');
    
    // デフォルトで今日の日付を設定
    if (printDateInput && !printDateInput.value) {
        const today = new Date();
        const todayString = today.getFullYear() + '-' + 
                          String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                          String(today.getDate()).padStart(2, '0');
        printDateInput.value = todayString;
    }
    
    // メッセージエリアをクリア
    hideArrivalSchedulePrintMessage();
}

/**
 * 入庫予定表印刷を実行
 */
async function executeArrivalSchedulePrint() {
    const printDate = document.getElementById('printDate').value;
    
    if (!printDate) {
        showArrivalSchedulePrintMessage('印刷対象日を選択してください。', 'danger');
        return;
    }

    try {
        showArrivalSchedulePrintLoading(true);
        
        // arrival-scheduleエンドポイントにリクエストを送信
        const url = `/admin/reports/arrival-schedule?date=${encodeURIComponent(printDate)}`;
        
        // 新しいウィンドウでPDFを開く
        window.open(url, '_blank');
        
        // モーダルを閉じる
        const modal = bootstrap.Modal.getInstance(document.getElementById('arrivalSchedulePrintModal'));
        modal.hide();
        
        showArrivalSchedulePrintMessage('入庫予定表を印刷用に開きました。', 'success');
        
    } catch (error) {
        console.error('入庫予定表印刷エラー:', error);
        showArrivalSchedulePrintMessage('印刷処理中にエラーが発生しました。', 'danger');
    } finally {
        showArrivalSchedulePrintLoading(false);
    }
}

/**
 * ローディング状態の表示/非表示
 */
function showArrivalSchedulePrintLoading(show) {
    const executeBtn = document.getElementById('executeArrivalSchedulePrintBtn');
    
    if (executeBtn) {
        if (show) {
            executeBtn.disabled = true;
            executeBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>印刷準備中...';
        } else {
            executeBtn.disabled = false;
            executeBtn.innerHTML = '<i class="bi bi-printer-fill me-2"></i>印刷';
        }
    }
}

/**
 * メッセージの表示
 */
function showArrivalSchedulePrintMessage(message, type = 'info') {
    const messageDiv = document.getElementById('arrivalSchedulePrintMessage');
    const messageText = document.getElementById('arrivalSchedulePrintMessageText');
    
    if (messageDiv && messageText) {
        messageText.textContent = message;
        messageDiv.className = `alert alert-${type} alert-dismissible fade show`;
        messageDiv.style.display = 'block';
        
        // 3秒後に自動的に非表示
        setTimeout(() => {
            hideArrivalSchedulePrintMessage();
        }, 3000);
    }
}

/**
 * メッセージの非表示
 */
function hideArrivalSchedulePrintMessage() {
    const messageDiv = document.getElementById('arrivalSchedulePrintMessage');
    
    if (messageDiv) {
        messageDiv.className = 'alert alert-dismissible fade';
        messageDiv.style.display = 'none';
    }
}

// グローバル関数として公開（HTMLから呼び出せるように）
window.hideArrivalSchedulePrintMessage = hideArrivalSchedulePrintMessage;