// assets/js/admin/pages/dashboard.js

import { Modal } from 'bootstrap';

/**
 * 管理者ダッシュボードページ固有のJavaScript処理
 */
export function initDashboard() {
    console.log('Admin Dashboard page specific script initialized.');
    
    // 入庫予定表印刷機能を初期化
    initArrivalSchedulePrint();
}

/**
 * 入庫予定表印刷ボタンの初期化
 */
function initArrivalSchedulePrint() {
    const printButton = document.querySelector('.btn-entry');
    
    if (printButton && printButton.textContent.includes('入庫予定表印刷')) {
        printButton.addEventListener('click', function(e) {
            e.preventDefault();
            showDateSelectionModal();
        });
    }
}

/**
 * 日付選択モーダルを表示
 */
function showDateSelectionModal() {
    const existingModal = document.getElementById('dateSelectionModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modalHtml = `
        <div class="modal fade" id="dateSelectionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-printer me-2"></i>
                            入庫予定表印刷
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="targetDate" class="form-label">印刷対象日</label>
                            <input type="date" class="form-control" id="targetDate" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="button" class="btn btn-primary" onclick="generateReport()">
                            <i class="bi bi-printer me-2"></i>
                            印刷
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    const modal = new Modal(document.getElementById('dateSelectionModal'));
    modal.show();
    
    document.getElementById('dateSelectionModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

/**
 * 帳票を生成して新しいタブで表示
 */
window.generateReport = function() {
    const targetDate = document.getElementById('targetDate').value;
    
    if (!targetDate) {
        alert('対象日を選択してください。');
        return;
    }
    
    const url = `/admin/reports/arrival-schedule?date=${targetDate}`;
    window.open(url, '_blank');
    
    const modal = Modal.getInstance(document.getElementById('dateSelectionModal'));
    modal.hide();
};