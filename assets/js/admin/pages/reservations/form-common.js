// assets/js/admin/pages/reservations/form-common.js

/**
 * 予約フォーム共通機能
 * 新規作成・編集ページ共通で使用される機能を提供
 */

export class ReservationFormManager {
    constructor(options = {}) {
        // DOM要素の取得
        this.workTypeSelect = document.getElementById('work_type_id');
        this.timeSlotGroup = document.querySelector('.time-slot-group');
        this.timeInputGroup = document.querySelector('.time-input-group');
        this.shopSelect = document.getElementById('shop_id');
        this.timeSlotSelect = document.getElementById('desired_time_slot_id');
        this.lineViaCheckbox = document.getElementById('via_line');
        this.lineDisplayNameInput = document.getElementById('line_display_name');
        
        // オプション
        this.timeSlots = options.timeSlots || [];
        this.workTypes = options.workTypes || [];
        this.currentReservation = options.currentReservation || null;
        
        // DOM要素の存在チェック
        if (!this.workTypeSelect) {
            console.error('work_type_id element not found');
            return;
        }
        if (!this.timeSlotGroup) {
            console.error('time-slot-group element not found');
            return;
        }
        if (!this.timeInputGroup) {
            console.error('time-input-group element not found');
            return;
        }
        
        console.log('ReservationFormManager initialized with:', {
            timeSlots: this.timeSlots.length,
            workTypes: this.workTypes.length,
            hasCurrentReservation: !!this.currentReservation
        });
        
        this.init();
    }
    
    /**
     * 初期化
     */
    init() {
        this.setupEventListeners();
        this.initializeFormState();
    }
    
    /**
     * イベントリスナー設定
     */
    setupEventListeners() {
        // 作業種別変更時の時間帯表示切り替え
        if (this.workTypeSelect) {
            this.workTypeSelect.addEventListener('change', () => {
                this.toggleTimeInputMethod();
            });
        }
        
        // 店舗変更時の時間帯オプション更新
        if (this.shopSelect) {
            this.shopSelect.addEventListener('change', () => {
                this.updateTimeSlotOptions();
            });
        }
        
        // LINE経由チェックボックスと入力欄の連動
        if (this.lineViaCheckbox && this.lineDisplayNameInput) {
            this.lineViaCheckbox.addEventListener('change', () => {
                this.toggleLineInput();
            });
        }
    }
    
    /**
     * フォーム初期状態設定
     */
    initializeFormState() {
        // データが正しく読み込まれているかチェック
        console.log('Initializing form state...');
        console.log('Work types:', this.workTypes);
        console.log('Time slots:', this.timeSlots);
        console.log('Current reservation:', this.currentReservation);
        
        // 初期状態で店舗が選択されている場合、時間帯を更新
        if (this.shopSelect && this.shopSelect.value) {
            this.updateTimeSlotOptions();
        }
        
        // 作業種別による表示切り替え
        this.toggleTimeInputMethod();
        
        // LINE入力の初期状態
        this.toggleLineInput();
    }
    
    /**
     * 作業種別による時間帯表示切り替え
     */
    toggleTimeInputMethod() {
        if (!this.workTypeSelect || !this.timeSlotGroup || !this.timeInputGroup) {
            return;
        }
        
        const selectedWorkType = this.workTypeSelect.value;
        console.log('Selected work type:', selectedWorkType); // デバッグ用
        
        // 作業種別データから該当するものを検索
        const selectedWorkTypeData = this.workTypes.find(wt => wt.id == selectedWorkType);
        console.log('Work type data:', selectedWorkTypeData); // デバッグ用
        
        if (selectedWorkTypeData && selectedWorkTypeData.code === 'clear_shaken') {
            // Clear車検の場合：時間帯選択
            console.log('Showing time slot selection'); // デバッグ用
            this.timeSlotGroup.style.display = 'block';
            this.timeInputGroup.style.display = 'none';
            this.updateTimeSlotOptions();
        } else {
            // その他の場合：直接時刻入力
            console.log('Showing direct time input'); // デバッグ用
            this.timeSlotGroup.style.display = 'none';
            this.timeInputGroup.style.display = 'block';
        }
    }
    
    /**
     * 店舗選択による時間帯オプション更新
     */
    updateTimeSlotOptions() {
        if (!this.shopSelect || !this.timeSlotSelect) {
            console.log('Shop select or time slot select not found'); // デバッグ用
            return;
        }
        
        const selectedShopId = this.shopSelect.value;
        console.log('Selected shop ID:', selectedShopId); // デバッグ用
        console.log('Available time slots:', this.timeSlots); // デバッグ用
        
        this.timeSlotSelect.innerHTML = '<option value="">選択してください</option>';
        
        if (selectedShopId) {
            const shopTimeSlots = this.timeSlots.filter(ts => 
                ts.shop_id == selectedShopId && ts.active
            );
            console.log('Filtered time slots for shop:', shopTimeSlots); // デバッグ用
            
            shopTimeSlots.sort((a, b) => a.sort_order - b.sort_order);
            
            shopTimeSlots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.id;
                option.textContent = slot.name;
                
                // 既存の予約データで選択状態を復元
                if (this.currentReservation && 
                    this.currentReservation.desired_time_slot_id == slot.id) {
                    option.selected = true;
                    console.log('Selected existing time slot:', slot.name); // デバッグ用
                }
                
                this.timeSlotSelect.appendChild(option);
            });
            
            console.log('Time slot options updated, count:', shopTimeSlots.length); // デバッグ用
        } else {
            // 店舗が選択されていない場合でも、Clear車検ならデフォルト店舗の時間帯を表示
            console.log('No shop selected, showing all time slots for debugging');
            
            // デバッグ用：全時間帯を表示（店舗ID別に）
            const timeSlotsByShop = {};
            this.timeSlots.forEach(ts => {
                if (!timeSlotsByShop[ts.shop_id]) {
                    timeSlotsByShop[ts.shop_id] = [];
                }
                timeSlotsByShop[ts.shop_id].push(ts);
            });
            
            console.log('Time slots grouped by shop:', timeSlotsByShop);
            
            // Clear車検対応店舗の時間帯があれば表示
            const clearShakenShopIds = Object.keys(timeSlotsByShop);
            if (clearShakenShopIds.length > 0) {
                const defaultShopId = clearShakenShopIds[0];
                console.log('Using default shop ID for Clear車検:', defaultShopId);
                
                const defaultTimeSlots = timeSlotsByShop[defaultShopId];
                defaultTimeSlots.sort((a, b) => a.sort_order - b.sort_order);
                
                defaultTimeSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.id;
                    option.textContent = slot.name;
                    this.timeSlotSelect.appendChild(option);
                });
            }
        }
    }
    
    /**
     * LINE経由チェックボックスと入力欄の連動
     */
    toggleLineInput() {
        if (!this.lineViaCheckbox || !this.lineDisplayNameInput) {
            return;
        }
        
        this.lineDisplayNameInput.disabled = !this.lineViaCheckbox.checked;
        
        if (!this.lineViaCheckbox.checked) {
            this.lineDisplayNameInput.value = '';
        }
    }
    
    /**
     * 次回点検日を自動設定
     * @param {number} months 追加する月数
     */
    setNextInspectionDate(months) {
        const desiredDateInput = document.getElementById('desired_date');
        const nextInspectionInput = document.getElementById('next_inspection_date');
        const sendNoticeCheckbox = document.getElementById('send_inspection_notice');
        
        if (!desiredDateInput || !nextInspectionInput) {
            return;
        }
        
        const desiredDate = desiredDateInput.value;
        if (desiredDate) {
            const date = new Date(desiredDate);
            date.setMonth(date.getMonth() + months);
            
            nextInspectionInput.value = date.toISOString().split('T')[0];
            
            // Flatpickrインスタンスがあれば更新
            if (nextInspectionInput._flatpickr) {
                nextInspectionInput._flatpickr.setDate(date);
            }
            
            // 次回点検案内チェックも自動でON
            if (sendNoticeCheckbox) {
                sendNoticeCheckbox.checked = true;
            }
        }
    }
    
    /**
     * フォームバリデーション
     * @returns {boolean} バリデーション結果
     */
    validateForm() {
        const form = document.getElementById('reservation-form');
        if (!form) {
            return true;
        }
        
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    }
    
    /**
     * フォームデータを取得
     * @returns {Object} フォームデータ
     */
    getFormData() {
        const form = document.getElementById('reservation-form');
        if (!form) {
            return {};
        }
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        return data;
    }
}