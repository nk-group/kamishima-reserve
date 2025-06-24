// assets/js/admin/pages/reservations/new.js

import { ReservationFormManager } from './form-common.js';

/**
 * 新規予約作成ページ固有のJavaScript処理
 */
export function initNewReservation() {
    console.log('Admin Reservations New page initialized.');
    
    // データが読み込まれるまで少し待つ
    setTimeout(() => {
        // PHP側から渡されたデータを取得
        const timeSlots = window.reservationData?.timeSlots || [];
        const workTypes = window.reservationData?.workTypes || [];
        
        console.log('Retrieved data - timeSlots:', timeSlots.length, 'workTypes:', workTypes.length);
        
        // 共通フォーム機能を初期化
        const formManager = new ReservationFormManager({
            timeSlots: timeSlots,
            workTypes: workTypes,
            currentReservation: null // 新規なのでnull
        });
        
        // 新規作成ページ固有の処理
        initNewPageFeatures(formManager);
    }, 100); // 100ms待機
}

/**
 * 新規作成ページ固有機能の初期化
 * @param {ReservationFormManager} formManager 
 */
function initNewPageFeatures(formManager) {
    // フォーム送信前のバリデーション
    const form = document.getElementById('reservation-form');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!formManager.validateForm()) {
                event.preventDefault();
                showValidationError();
            }
        });
    }
    
    // リセットボタンの処理
    const resetButton = form?.querySelector('button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function(event) {
            if (!confirm('入力内容をクリアしてもよろしいですか？')) {
                event.preventDefault();
            } else {
                // フォームリセット後に初期状態を復元
                setTimeout(() => {
                    formManager.initializeFormState();
                }, 100);
            }
        });
    }
    
    // 初期フォーカス設定
    setInitialFocus();
    
    // 自動保存機能（オプション）
    // setupAutoSave(formManager);
}

/**
 * バリデーションエラー表示
 */
function showValidationError() {
    // 既存のエラーメッセージを削除
    const existingError = document.querySelector('.validation-error-alert');
    if (existingError) {
        existingError.remove();
    }
    
    // 新しいエラーメッセージを作成
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger validation-error-alert';
    errorDiv.innerHTML = `
        <strong>入力エラー</strong><br>
        必須項目が入力されていません。赤く表示された項目を確認してください。
    `;
    
    // フォームの上に挿入
    const form = document.getElementById('reservation-form');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
        
        // 3秒後に自動で消去
        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }
}

/**
 * 初期フォーカス設定
 */
function setInitialFocus() {
    // 最初の必須フィールドにフォーカス
    const firstRequiredField = document.querySelector('#reservation-form [required]');
    if (firstRequiredField) {
        setTimeout(() => {
            firstRequiredField.focus();
        }, 300);
    }
}

/**
 * 自動保存機能（オプション）
 * @param {ReservationFormManager} formManager 
 */
function setupAutoSave(formManager) {
    let autoSaveTimer;
    const AUTOSAVE_INTERVAL = 30000; // 30秒
    
    const form = document.getElementById('reservation-form');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveFormDraft(formManager.getFormData());
            }, AUTOSAVE_INTERVAL);
        });
    });
}

/**
 * フォームの下書き保存
 * @param {Object} formData 
 */
function saveFormDraft(formData) {
    // ローカルストレージに保存（実装例）
    try {
        localStorage.setItem('reservation-draft', JSON.stringify(formData));
        console.log('Draft saved automatically');
    } catch (error) {
        console.warn('Failed to save draft:', error);
    }
}