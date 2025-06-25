/**
 * Shop Closing Days JavaScript
 * ファイル名: assets/js/admin/pages/shop-closing-days.js
 */

/**
 * 定休日マスタ一覧ページの初期化
 */
export function initShopClosingDaysIndex() {
    console.log('Shop Closing Days Index page initialized.');
    
    // 削除確認モーダル
    window.confirmDelete = function(id, name) {
        const targetElement = document.getElementById('deleteTargetName');
        const formElement = document.getElementById('deleteForm');
        
        if (targetElement && formElement) {
            targetElement.textContent = name;
            formElement.action = `/admin/shop-closing-days/delete/${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    };
    
    // 共通機能の初期化
    initCommonFeatures();
}

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
        return;
    }

    // 繰り返し種別の説明を更新
    function updateRepeatDescription() {
        const repeatType = parseInt(repeatTypeSelect.value);
        const closingDate = closingDateInput.value;
        
        if (repeatType === 0) {
            // 単発
            repeatInfo.style.display = 'none';
            if (repeatEndDateInput) repeatEndDateInput.disabled = true;
        } else {
            repeatInfo.style.display = 'block';
            if (repeatEndDateInput) repeatEndDateInput.disabled = false;
            
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

/**
 * 共通機能の初期化
 */
function initCommonFeatures() {
    // フォームバリデーション（既存のutils/form-validation.jsと重複を避けるため、
    // カスタムバリデーションのみここで処理）
    
    // フラッシュメッセージの自動非表示
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