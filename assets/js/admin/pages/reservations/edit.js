// assets/js/admin/pages/reservations/edit.js

import { ReservationFormManager } from './form-common.js';

/**
 * 予約編集ページ固有のJavaScript処理
 */
export function initEditReservation() {
    console.log('Admin Reservations Edit page initialized.');
    
    // データが読み込まれるまで少し待つ
    setTimeout(() => {
        // PHP側から渡されたデータを取得
        const timeSlots = window.reservationData?.timeSlots || [];
        const workTypes = window.reservationData?.workTypes || [];
        const currentReservation = window.reservationData?.currentReservation || null;
        
        console.log('Retrieved data - timeSlots:', timeSlots.length, 'workTypes:', workTypes.length);
        
        // 共通フォーム機能を初期化
        const formManager = new ReservationFormManager({
            timeSlots: timeSlots,
            workTypes: workTypes,
            currentReservation: currentReservation
        });
        
        // 編集ページ固有の処理
        initEditPageFeatures(formManager);
    }, 100); // 100ms待機
}

/**
 * 編集ページ固有機能の初期化
 * @param {ReservationFormManager} formManager 
 */
function initEditPageFeatures(formManager) {
    // 削除確認機能
    setupDeleteConfirmation();
    
    // 月数ボタン機能
    setupMonthButtons(formManager);
    
    // 変更検知機能
    setupChangeDetection();
    
    // フォーム送信前のバリデーション
    setupFormValidation(formManager);
}

/**
 * 削除確認機能の設定
 */
function setupDeleteConfirmation() {
    // グローバル関数として削除確認を定義
    window.confirmDelete = function() {
        const reservationNo = document.querySelector('input[readonly]')?.value || '不明';
        
        const confirmMessage = `予約番号: ${reservationNo}\n\nこの予約を削除してもよろしいですか？\n削除した予約は復元できません。`;
        
        if (confirm(confirmMessage)) {
            // 削除処理実行
            const deleteForm = document.getElementById('delete-form');
            if (deleteForm) {
                // 削除中の表示
                showDeleteProgress();
                deleteForm.submit();
            }
        }
    };
}

/**
 * 月数ボタン機能の設定
 * @param {ReservationFormManager} formManager 
 */
function setupMonthButtons(formManager) {
    document.querySelectorAll('.btn-month').forEach(btn => {
        btn.addEventListener('click', function() {
            const months = parseInt(this.dataset.months);
            if (months) {
                formManager.setNextInspectionDate(months);
                
                // ボタンのフィードバック
                showButtonFeedback(this, `${months}ヶ月後の日付を設定しました`);
            }
        });
    });
}

/**
 * 変更検知機能の設定
 */
function setupChangeDetection() {
    let hasChanges = false;
    
    const form = document.getElementById('reservation-form');
    if (!form) return;
    
    // 初期値を保存
    const initialValues = new Map();
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        initialValues.set(input.name, getInputValue(input));
        
        input.addEventListener('change', () => {
            const currentValue = getInputValue(input);
            const initialValue = initialValues.get(input.name);
            
            if (currentValue !== initialValue) {
                hasChanges = true;
            }
            
            updateChangeIndicator();
        });
    });
    
    // ページ離脱時の警告
    window.addEventListener('beforeunload', function(event) {
        if (hasChanges) {
            event.preventDefault();
            event.returnValue = '変更が保存されていません。ページを離れてもよろしいですか？';
        }
    });
    
    // フォーム送信時は警告を無効化
    form.addEventListener('submit', () => {
        hasChanges = false;
    });
}

/**
 * フォームバリデーション設定
 * @param {ReservationFormManager} formManager 
 */
function setupFormValidation(formManager) {
    const form = document.getElementById('reservation-form');
    if (!form) return;
    
    form.addEventListener('submit', function(event) {
        if (!formManager.validateForm()) {
            event.preventDefault();
            showValidationError();
        }
    });
}

/**
 * 削除処理中の表示
 */
function showDeleteProgress() {
    const deleteButton = document.querySelector('.btn-outline-custom[onclick="confirmDelete()"]');
    if (deleteButton) {
        deleteButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>削除中...';
        deleteButton.disabled = true;
    }
}

/**
 * ボタンフィードバック表示
 * @param {HTMLElement} button 
 * @param {string} message 
 */
function showButtonFeedback(button, message) {
    const originalText = button.textContent;
    const originalClass = button.className;
    
    // 成功表示
    button.textContent = '✓ 設定完了';
    button.className = button.className.replace('btn-month', 'btn-success');
    
    // 1.5秒後に元に戻す
    setTimeout(() => {
        button.textContent = originalText;
        button.className = originalClass;
    }, 1500);
}

/**
 * バリデーションエラー表示
 */
function showValidationError() {
    const existingError = document.querySelector('.validation-error-alert');
    if (existingError) {
        existingError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger validation-error-alert';
    errorDiv.innerHTML = `
        <strong>入力エラー</strong><br>
        必須項目が入力されていません。赤く表示された項目を確認してください。
    `;
    
    const form = document.getElementById('reservation-form');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}

/**
 * 変更インジケーター更新
 */
function updateChangeIndicator() {
    // ページタイトルに変更マークを表示
    const pageTitle = document.querySelector('.page-title');
    if (pageTitle && !pageTitle.textContent.includes('*')) {
        pageTitle.textContent += ' *';
    }
}

/**
 * 入力要素の値を取得
 * @param {HTMLElement} input 
 * @returns {string}
 */
function getInputValue(input) {
    if (input.type === 'checkbox') {
        return input.checked ? '1' : '0';
    }
    return input.value || '';
}