/**
 * 定休日マスタ一括作成ページ JavaScript
 * ファイル名: assets/js/admin/pages/shop-closing-days/batch.js
 */

import { initCommonFeatures } from './common.js';

/**
 * 定休日マスタ一括作成ページの初期化
 */
export function initShopClosingDaysBatch() {
    console.log('Shop Closing Days Batch page initialized.');
    
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const repeatTypeSelect = document.getElementById('repeat_type');
    const previewInfo = document.getElementById('preview-info');
    const previewDescription = document.getElementById('preview-description');
    const submitBtn = document.getElementById('submit-btn');

    if (!startDateInput || !endDateInput || !repeatTypeSelect || !previewInfo || !previewDescription) {
        console.warn('Required batch form elements not found');
        return;
    }

    // プレビュー情報を更新
    function updatePreview() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const repeatType = parseInt(repeatTypeSelect.value);

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end >= start) {
                const days = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                const startFormatted = formatDateJapanese(start);
                const endFormatted = formatDateJapanese(end);
                
                let description = `${startFormatted} から ${endFormatted} までの ${days} 日間を登録します。`;
                
                if (repeatType === 1) {
                    description += '<br><small class="text-muted">※毎週同じ曜日に繰り返されます</small>';
                } else if (repeatType === 2) {
                    description += '<br><small class="text-muted">※毎年同じ月日に繰り返されます</small>';
                }
                
                previewDescription.innerHTML = description;
                previewInfo.style.display = 'block';
                
                // 登録件数をボタンに表示
                if (submitBtn) {
                    submitBtn.innerHTML = `<i class="bi bi-plus-circle-dotted"></i> ${days}件を一括登録する`;
                }
            } else {
                previewInfo.style.display = 'none';
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-plus-circle-dotted"></i> 一括登録する';
                }
            }
        } else {
            previewInfo.style.display = 'none';
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-dotted"></i> 一括登録する';
            }
        }
    }

    // 日付を日本語形式でフォーマット
    function formatDateJapanese(date) {
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate();
        const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        const weekday = weekdays[date.getDay()];
        
        return `${year}年${month}月${day}日(${weekday})`;
    }

    // イベントリスナー設定
    startDateInput.addEventListener('change', updatePreview);
    endDateInput.addEventListener('change', updatePreview);
    repeatTypeSelect.addEventListener('change', updatePreview);

    // 初期表示
    updatePreview();

    // 終了日の妥当性チェック
    function validateDateRange() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end < start) {
                endDateInput.setCustomValidity('終了日は開始日以降の日付を入力してください。');
            } else {
                endDateInput.setCustomValidity('');
            }
        } else {
            endDateInput.setCustomValidity('');
        }
    }

    startDateInput.addEventListener('change', validateDateRange);
    endDateInput.addEventListener('change', validateDateRange);

    // 期間の妥当性チェック
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function(event) {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const days = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                
                if (days > 365) {
                    if (!confirm(`${days}日間の登録を行います。データ量が多くなりますが、続行しますか？`)) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                }
            }
        });
    }
    
    // 共通機能の初期化
    initCommonFeatures();
}