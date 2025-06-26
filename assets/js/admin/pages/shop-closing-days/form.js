/**
 * 定休日マスタフォームページ（新規・編集）JavaScript
 * ファイル名: assets/js/admin/pages/shop-closing-days/form.js
 */

import { initCommonFeatures } from './common.js';

/**
 * 定休日マスタフォームページ（新規・編集）の初期化
 */
export function initShopClosingDaysForm() {
    console.log('Shop Closing Days Form page initialized.');
    
    const repeatTypeSelect = document.getElementById('repeat_type');
    const repeatInfo = document.getElementById('repeat-info');
    const repeatDescription = document.getElementById('repeat-description');
    const closingDateInput = document.getElementById('closing_date');
    const repeatEndDateInput = document.getElementById('repeat_end_date');

    if (!repeatTypeSelect || !repeatInfo || !repeatDescription || !closingDateInput) {
        console.warn('Required form elements not found');
        return;
    }

    // 繰り返し種別の説明を更新
    function updateRepeatDescription() {
        const repeatType = parseInt(repeatTypeSelect.value);
        const closingDate = closingDateInput.value;
        
        if (repeatType === 0) {
            // 単発
            repeatInfo.style.display = 'none';
            if (repeatEndDateInput) {
                repeatEndDateInput.disabled = true;
                repeatEndDateInput.value = ''; // 値もクリア
            }
        } else {
            repeatInfo.style.display = 'block';
            if (repeatEndDateInput) {
                repeatEndDateInput.disabled = false;
            }
            
            let description = '';
            if (closingDate) {
                const date = new Date(closingDate);
                const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                const weekday = weekdays[date.getDay()];
                const month = date.getMonth() + 1;
                const day = date.getDate();
                
                if (repeatType === 1) {
                    description = `毎週${weekday}曜日が休業日となります。`;
                } else if (repeatType === 2) {
                    description = `毎年${month}月${day}日が休業日となります。`;
                }
            } else {
                if (repeatType === 1) {
                    description = '休業日で指定した曜日が毎週休業日となります。';
                } else if (repeatType === 2) {
                    description = '休業日で指定した月日が毎年休業日となります。';
                }
            }
            
            repeatDescription.textContent = description;
        }
    }

    // イベントリスナー設定
    repeatTypeSelect.addEventListener('change', updateRepeatDescription);
    closingDateInput.addEventListener('change', updateRepeatDescription);

    // 初期表示
    updateRepeatDescription();

    // 単発の場合の日付チェック
    function validateSingleDate() {
        const repeatType = parseInt(repeatTypeSelect.value);
        const closingDate = closingDateInput.value;
        
        if (repeatType === 0 && closingDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(closingDate);
            
            if (selectedDate < today) {
                closingDateInput.setCustomValidity('単発の定休日は今日以降の日付を入力してください。');
            } else {
                closingDateInput.setCustomValidity('');
            }
        } else {
            closingDateInput.setCustomValidity('');
        }
    }

    repeatTypeSelect.addEventListener('change', validateSingleDate);
    closingDateInput.addEventListener('change', validateSingleDate);

    // 終了日の妥当性チェック
    if (repeatEndDateInput) {
        function validateEndDate() {
            const closingDate = closingDateInput.value;
            const endDate = repeatEndDateInput.value;
            
            if (closingDate && endDate) {
                const start = new Date(closingDate);
                const end = new Date(endDate);
                
                if (end < start) {
                    repeatEndDateInput.setCustomValidity('繰り返し終了日は休業日以降の日付を入力してください。');
                } else {
                    repeatEndDateInput.setCustomValidity('');
                }
            } else {
                repeatEndDateInput.setCustomValidity('');
            }
        }

        closingDateInput.addEventListener('change', validateEndDate);
        repeatEndDateInput.addEventListener('change', validateEndDate);
    }
    
    // 共通機能の初期化
    initCommonFeatures();
}