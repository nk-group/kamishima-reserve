//==========================================================================
// Customer Calendar Week Page JavaScript - Clear車検予約システム顧客向け
//==========================================================================

/**
 * 週表示カレンダーページの初期化
 * admin dashboard構造に準拠したシンプルな実装
 */
export function initCalendarWeek() {
    console.log('Customer Calendar Week page initialized');
    
    // DOM要素の取得
    const calendarData = document.getElementById('calendar-week-data');
    const prevWeekBtn = document.getElementById('prev-week-btn');
    const nextWeekBtn = document.getElementById('next-week-btn');
    const backToMonthBtn = document.getElementById('back-to-month-btn');
    
    if (!calendarData) {
        console.error('Calendar week data element not found');
        return;
    }
    
    // 初期データの取得
    let currentWeekStart = calendarData.dataset.currentWeekStart;
    let shopId = calendarData.dataset.shopId; // shop_id取得
    const baseUrl = calendarData.dataset.baseUrl || '/customer/calendar/week';
    
    // イベントリスナーの設定
    setupEventListeners();
    
    // 時間帯ボタンのクリック処理設定
    setupTimeSlotClicks();
    
    /**
     * イベントリスナーの設定
     */
    function setupEventListeners() {
        // 前週ボタン
        if (prevWeekBtn) {
            prevWeekBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const date = new Date(currentWeekStart);
                date.setDate(date.getDate() - 7);
                currentWeekStart = formatDate(date);
                updateCalendar();
            });
        }
        
        // 次週ボタン
        if (nextWeekBtn) {
            nextWeekBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const date = new Date(currentWeekStart);
                date.setDate(date.getDate() + 7);
                currentWeekStart = formatDate(date);
                updateCalendar();
            });
        }
        
        // 月表示に戻るボタン
        if (backToMonthBtn) {
            backToMonthBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // 月表示URLにshop_idを含める
                const monthUrl = `/customer/calendar/month?shop_id=${shopId}`;
                
                // iframe環境の場合は親ウィンドウに通知
                if (window.self !== window.top && window.parent.postMessage) {
                    window.parent.postMessage({
                        type: 'calendar-navigation',
                        shopId: shopId,
                        nextUrl: monthUrl
                    }, '*');
                } else {
                    window.location.href = monthUrl;
                }
            });
        }
    }
    
    /**
     * 時間帯ボタンのクリック処理設定
     */
    function setupTimeSlotClicks() {
        document.addEventListener('click', function(e) {
            const timeSlotButton = e.target.closest('.time-slot-button');
            if (!timeSlotButton) return;
            
            e.preventDefault();
            
            // 時間帯データを取得
            const timeSlotCell = timeSlotButton.closest('.time-slot-cell');
            if (!timeSlotCell) return;
            
            const dateStr = timeSlotCell.dataset.date;
            const timeSlotId = timeSlotButton.dataset.timeSlotId;
            const timeSlotName = timeSlotButton.textContent.trim();
            
            if (!dateStr || !timeSlotId) {
                console.error('Missing date or time slot data');
                return;
            }
            
            // 予約可能かチェック
            if (timeSlotCell.classList.contains('available') || 
                timeSlotCell.classList.contains('limited')) {
                handleTimeSlotClick(dateStr, timeSlotId, timeSlotName);
            }
        });
    }
    
    /**
     * 時間帯クリック処理
     * @param {string} dateStr 
     * @param {string} timeSlotId 
     * @param {string} timeSlotName 
     */
    function handleTimeSlotClick(dateStr, timeSlotId, timeSlotName) {
        console.log('Time slot clicked:', { dateStr, timeSlotId, timeSlotName, shopId });
        
        // 予約フォームページに遷移（shop_idパラメータを追加）
        const formUrl = `/customer/reservation/form?shop_id=${shopId}&date=${dateStr}&time_slot_id=${timeSlotId}`;
        
        // iframe環境の場合は親ウィンドウに通知
        if (window.self !== window.top && window.parent.postMessage) {
            window.parent.postMessage({
                type: 'time-slot-selected',
                date: dateStr,
                timeSlotId: timeSlotId,
                timeSlotName: timeSlotName,
                shopId: shopId,
                nextUrl: formUrl
            }, '*');
        } else {
            // 通常環境では直接遷移
            window.location.href = formUrl;
        }
    }
    
    /**
     * カレンダーの更新
     */
    function updateCalendar() {
        showLoading();
        
        const params = new URLSearchParams({
            shop_id: shopId,
            week: currentWeekStart,
            ajax: '1'
        });
        
        fetch(`${baseUrl}?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.html) {
                    updateCalendarContent(data);
                } else {
                    throw new Error(data.message || 'カレンダーの更新に失敗しました');
                }
            })
            .catch(error => {
                console.error('Calendar update error:', error);
                showError('カレンダーの更新中にエラーが発生しました。再度お試しください。');
            });
    }
    
    /**
     * カレンダーコンテンツの更新
     * @param {Object} data 
     */
    function updateCalendarContent(data) {
        // カレンダー本体の更新
        const calendarWrapper = document.querySelector('.week-calendar-wrapper');
        if (calendarWrapper && data.html) {
            calendarWrapper.innerHTML = data.html;
        }
        
        // ナビゲーション表示の更新
        const currentWeekDisplay = document.getElementById('current-week-display');
        if (currentWeekDisplay && data.current_week_display) {
            currentWeekDisplay.textContent = data.current_week_display;
        }
        
        // 時間帯ボタンのクリック処理を再設定
        setupTimeSlotClicks();
        
        console.log('Week calendar updated successfully');
    }
    
    /**
     * ローディング表示
     */
    function showLoading() {
        const calendarWrapper = document.querySelector('.week-calendar-wrapper');
        if (calendarWrapper) {
            calendarWrapper.innerHTML = `
                <div class="week-calendar-loading">
                    <div class="loading-content">
                        <div class="loading-spinner"></div>
                        <div class="loading-text">週カレンダーを読み込み中...</div>
                    </div>
                </div>
            `;
        }
    }
    
    /**
     * エラー表示
     * @param {string} message 
     */
    function showError(message) {
        const calendarWrapper = document.querySelector('.week-calendar-wrapper');
        if (calendarWrapper) {
            calendarWrapper.innerHTML = `
                <div class="week-calendar-empty">
                    <div class="empty-icon">⚠️</div>
                    <div class="empty-title">エラーが発生しました</div>
                    <div class="empty-description">${escapeHtml(message)}</div>
                </div>
            `;
        }
    }
    
    /**
     * 日付フォーマット（YYYY-MM-DD）
     * @param {Date} date 
     * @returns {string}
     */
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    /**
     * HTMLエスケープ
     * @param {string} text 
     * @returns {string}
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}