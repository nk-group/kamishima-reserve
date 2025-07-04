//==========================================================================
// Customer Reservation Form Page JavaScript - Clear車検予約システム顧客向け
//==========================================================================

/**
 * 予約入力フォームページの初期化
 * admin form構造に準拠したシンプルな実装
 */
export function initReservationForm() {
    console.log('Customer Reservation Form page initialized');
    
    // DOM要素の取得
    const formData = document.getElementById('reservation-form-data');
    const reservationForm = document.getElementById('reservation-form');
    
    if (!formData || !reservationForm) {
        console.error('Form data or form element not found');
        return;
    }
    
    // フォーム機能の初期化
    initFormValidation();
    initVehicleNumberInputs();
    initFormSubmission();
    initCalendarNavigationButtons();
    
    console.log('Reservation form initialized successfully');
    
    /**
     * カレンダー戻るボタンの初期化
     * 週表示ページの実装パターンに準拠
     */
    function initCalendarNavigationButtons() {
        const backToMonthBtn = document.getElementById('back-to-month-btn');
        const backToWeekBtn = document.getElementById('back-to-week-btn');
        
        if (backToMonthBtn) {
            backToMonthBtn.addEventListener('click', function(e) {
                e.preventDefault();
                handleCalendarNavigation('month', this);
            });
        }
        
        if (backToWeekBtn) {
            backToWeekBtn.addEventListener('click', function(e) {
                e.preventDefault();
                handleCalendarNavigation('week', this);
            });
        }
        
        console.log('Calendar navigation buttons initialized');
    }
    
    /**
     * カレンダー遷移処理
     * @param {string} viewType - 'month' または 'week'
     * @param {Element} button - クリックされたボタン要素
     */
    function handleCalendarNavigation(viewType, button) {
        const shopId = button.dataset.shopId;
        
        if (!shopId) {
            console.warn('Shop ID not found for calendar navigation');
            // shopIdが無い場合でも遷移を試行
        }
        
        // 遷移先URLを構築
        let targetUrl;
        if (viewType === 'month') {
            targetUrl = '/customer/calendar/month';
        } else if (viewType === 'week') {
            targetUrl = '/customer/calendar/week';
        } else {
            console.error('Invalid view type:', viewType);
            return;
        }
        
        // shop_idパラメータを追加
        if (shopId) {
            targetUrl += `?shop_id=${encodeURIComponent(shopId)}`;
        }
        
        console.log(`Navigating to ${viewType} view:`, targetUrl);
        
        // iframe環境の場合は親ウィンドウに通知
        if (window.self !== window.top && window.parent.postMessage) {
            window.parent.postMessage({
                type: 'calendar-navigation',
                viewType: viewType,
                shopId: shopId,
                targetUrl: targetUrl
            }, '*');
        } else {
            // 通常環境では直接遷移
            window.location.href = targetUrl;
        }
    }
    
    /**
     * フォームバリデーションの初期化
     * admin form-validation.jsに準拠
     */
    function initFormValidation() {
        // Bootstrap 5のバリデーション
        reservationForm.addEventListener('submit', function(event) {
            if (!reservationForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // 最初のエラーフィールドにフォーカス
                const firstInvalid = reservationForm.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            
            reservationForm.classList.add('was-validated');
        }, false);
        
        // リアルタイムバリデーション
        setupRealtimeValidation();
    }
    
    /**
     * リアルタイムバリデーションの設定
     */
    function setupRealtimeValidation() {
        // 必須フィールドのリアルタイムチェック
        const requiredFields = reservationForm.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            field.addEventListener('input', function() {
                // 入力中はエラー表示をクリア
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        });
        
        // メールアドレスの形式チェック
        const emailField = document.getElementById('email');
        if (emailField) {
            emailField.addEventListener('blur', function() {
                validateEmail(this);
            });
        }
        
        // 電話番号の形式チェック
        const phoneField = document.getElementById('phone_number1');
        if (phoneField) {
            phoneField.addEventListener('blur', function() {
                validatePhone(this);
            });
        }
    }
    
    /**
     * フィールドバリデーション
     * @param {Element} field 
     */
    function validateField(field) {
        const isValid = field.checkValidity();
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }
        
        return isValid;
    }
    
    /**
     * メールアドレスバリデーション
     * @param {Element} emailField 
     */
    function validateEmail(emailField) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailField.value === '' || emailPattern.test(emailField.value);
        
        if (isValid) {
            emailField.setCustomValidity('');
        } else {
            emailField.setCustomValidity('正しいメールアドレスを入力してください');
        }
        
        validateField(emailField);
    }
    
    /**
     * 電話番号バリデーション
     * @param {Element} phoneField 
     */
    function validatePhone(phoneField) {
        const phonePattern = /^[\d\-\(\)\s]+$/;
        const isValid = phoneField.value === '' || phonePattern.test(phoneField.value);
        
        if (isValid) {
            phoneField.setCustomValidity('');
        } else {
            phoneField.setCustomValidity('正しい電話番号を入力してください');
        }
        
        validateField(phoneField);
    }
    
    /**
     * 車両ナンバー入力フィールドの初期化
     * admin form構造に準拠
     */
    function initVehicleNumberInputs() {
        const vehicleInputs = {
            region: document.querySelector('input[name="vehicle_license_region"]'),
            class: document.querySelector('input[name="vehicle_license_class"]'),
            kana: document.querySelector('input[name="vehicle_license_kana"]'),
            number: document.querySelector('input[name="vehicle_license_number"]')
        };
        
        // 入力制限とフォーマット
        if (vehicleInputs.region) {
            vehicleInputs.region.addEventListener('input', function() {
                // ひらがな・カタカナ・漢字・英数字のみ
                this.value = this.value.replace(/[^\u3040-\u309F\u30A0-\u30FF\u4E00-\u9FAF\u3005-\u3006\u30FC\w]/g, '');
            });
        }
        
        if (vehicleInputs.class) {
            vehicleInputs.class.addEventListener('input', function() {
                // 数字のみ、3桁まで
                this.value = this.value.replace(/[^\d]/g, '').substring(0, 3);
            });
        }
        
        if (vehicleInputs.kana) {
            vehicleInputs.kana.addEventListener('input', function() {
                // ひらがな・カタカナのみ、1文字まで
                this.value = this.value.replace(/[^\u3040-\u309F\u30A0-\u30FF]/g, '').substring(0, 1);
            });
        }
        
        if (vehicleInputs.number) {
            vehicleInputs.number.addEventListener('input', function() {
                // 数字のみ、4桁まで
                this.value = this.value.replace(/[^\d]/g, '').substring(0, 4);
            });
        }
        
        // タブ移動の改善
        setupVehicleNumberTabbing(vehicleInputs);
    }
    
    /**
     * 車両ナンバータブ移動の設定
     * @param {Object} inputs 
     */
    function setupVehicleNumberTabbing(inputs) {
        const inputOrder = ['region', 'class', 'kana', 'number'];
        
        inputOrder.forEach((key, index) => {
            const input = inputs[key];
            if (!input) return;
            
            input.addEventListener('keydown', function(e) {
                // Enterキーで次のフィールドに移動
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const nextIndex = index + 1;
                    if (nextIndex < inputOrder.length) {
                        const nextInput = inputs[inputOrder[nextIndex]];
                        if (nextInput) {
                            nextInput.focus();
                        }
                    }
                }
            });
        });
    }
    
    /**
     * フォーム送信処理の初期化
     */
    function initFormSubmission() {
        reservationForm.addEventListener('submit', function(e) {
            // バリデーションが通った場合のみ送信処理
            if (reservationForm.checkValidity()) {
                handleFormSubmission(e);
            }
        });
    }
    
    /**
     * フォーム送信処理
     * @param {Event} e 
     */
    function handleFormSubmission(e) {
        console.log('Form submission initiated');
        
        // 送信ボタンを無効化
        const submitBtn = reservationForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>送信中...';
        }
        
        // ローディング表示
        showFormLoading();
        
        // iframe環境での送信処理
        if (window.self !== window.top && window.parent.postMessage) {
            // 親ウィンドウに送信開始を通知
            window.parent.postMessage({
                type: 'form-submission-started'
            }, '*');
        }
    }
    
    /**
     * フォームローディング表示
     */
    function showFormLoading() {
        const formContainer = document.querySelector('.reservation-form-container');
        if (formContainer) {
            formContainer.classList.add('form-loading-overlay');
            
            // ローディングスピナーを追加
            const loadingSpinner = document.createElement('div');
            loadingSpinner.className = 'loading-spinner';
            formContainer.appendChild(loadingSpinner);
        }
    }
}