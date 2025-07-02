//==========================================================================
// Customer Calendar Week Page JavaScript - Clear車検予約システム顧客向け
//==========================================================================

/**
 * 週表示カレンダーページの初期化
 * Ajax排除型 - 通常画面遷移のみ使用
 */
export function initCalendarWeek() {
    console.log('Calendar Week page initialized');
    
    // ボタンイベントリスナー設定
    setupButtonEvents();
    
    // 時間帯ボタンのクリック処理設定
    setupTimeSlotClicks();
}

/**
 * ボタンイベントリスナー設定
 */
function setupButtonEvents() {
    // 前週ボタン
    const prevWeekBtn = document.getElementById('prev-week-btn');
    if (prevWeekBtn) {
        prevWeekBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const week = this.dataset.week;
            const shopId = this.dataset.shopId;
            if (week && shopId) {
                window.location.href = `/customer/calendar/week?shop_id=${shopId}&week=${week}`;
            }
        });
    }
    
    // 次週ボタン
    const nextWeekBtn = document.getElementById('next-week-btn');
    if (nextWeekBtn) {
        nextWeekBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const week = this.dataset.week;
            const shopId = this.dataset.shopId;
            if (week && shopId) {
                window.location.href = `/customer/calendar/week?shop_id=${shopId}&week=${week}`;
            }
        });
    }
    
    // 月表示に戻るボタン
    const backToMonthBtn = document.getElementById('back-to-month-btn');
    if (backToMonthBtn) {
        backToMonthBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const shopId = this.dataset.shopId;
            if (shopId) {
                window.location.href = `/customer/calendar/month?shop_id=${shopId}`;
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
    // URLパラメータからshop_idを取得
    const urlParams = new URLSearchParams(window.location.search);
    const shopId = urlParams.get('shop_id');
    
    if (!shopId) {
        console.error('Shop ID not found in URL parameters');
        return;
    }
    
    console.log('Time slot clicked:', { dateStr, timeSlotId, timeSlotName, shopId });
    
    // 予約フォームページに遷移
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