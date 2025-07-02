//==========================================================================
// Customer Calendar Month Page JavaScript - Clear車検予約システム顧客向け
//==========================================================================

/**
 * 月表示カレンダーページの初期化
 * Ajax排除型 - 通常画面遷移のみ使用
 */
export function initCalendarMonth() {
    console.log('Calendar Month page initialized');
    
    // ボタンイベントリスナー設定
    setupButtonEvents();
    
    // カレンダーセルのクリック処理設定
    setupCalendarCellClicks();
}

/**
 * ボタンイベントリスナー設定
 */
function setupButtonEvents() {
    // 前月ボタン
    const prevMonthBtn = document.getElementById('prev-month-btn');
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const month = this.dataset.month;
            const shopId = this.dataset.shopId;
            if (month && shopId) {
                window.location.href = `/customer/calendar/month?shop_id=${shopId}&month=${month}`;
            }
        });
    }
    
    // 次月ボタン
    const nextMonthBtn = document.getElementById('next-month-btn');
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const month = this.dataset.month;
            const shopId = this.dataset.shopId;
            if (month && shopId) {
                window.location.href = `/customer/calendar/month?shop_id=${shopId}&month=${month}`;
            }
        });
    }
}

/**
 * カレンダーセルのクリック処理設定
 */
function setupCalendarCellClicks() {
    document.addEventListener('click', function(e) {
        const calendarCell = e.target.closest('.calendar-cell');
        if (!calendarCell) return;
        
        // clickableクラスを持つセルのみクリック可能
        if (!calendarCell.classList.contains('clickable')) {
            return;
        }
        
        // 日付データを取得
        const dateStr = calendarCell.dataset.date;
        if (!dateStr) return;
        
        // 空き状況をチェック
        const availabilityMark = calendarCell.querySelector('.availability-mark');
        if (!availabilityMark) return;
        
        const availability = getAvailabilityStatus(availabilityMark);
        
        // 週表示カレンダーまたは予約フォームに遷移
        if (availability === 'available' || availability === 'limited') {
            handleDateClick(dateStr, availability);
        }
    });
}

/**
 * 空き状況の取得
 * @param {Element} markElement 
 * @returns {string}
 */
function getAvailabilityStatus(markElement) {
    if (markElement.classList.contains('available')) return 'available';
    if (markElement.classList.contains('limited')) return 'limited';
    if (markElement.classList.contains('full')) return 'full';
    if (markElement.classList.contains('closed')) return 'closed';
    return 'unknown';
}

/**
 * 日付クリック処理
 * @param {string} dateStr 
 * @param {string} availability 
 */
function handleDateClick(dateStr, availability) {
    console.log('Date clicked:', dateStr, 'Availability:', availability);
    
    // URLパラメータからshop_idを取得
    const urlParams = new URLSearchParams(window.location.search);
    const shopId = urlParams.get('shop_id');
    
    if (!shopId) {
        console.error('Shop ID not found in URL parameters');
        return;
    }
    
    // 選択した日付から週の開始日（日曜日）を計算
    const selectedDate = new Date(dateStr);
    const dayOfWeek = selectedDate.getDay(); // 0: 日曜日, 1: 月曜日, ...
    const sundayOffset = -dayOfWeek; // 日曜日への日数差
    const sundayDate = new Date(selectedDate);
    sundayDate.setDate(selectedDate.getDate() + sundayOffset);
    
    // 週の開始日をYYYY-MM-DD形式で取得
    const weekStart = sundayDate.toISOString().split('T')[0];
    
    // 週表示カレンダーページに遷移
    const weekUrl = `/customer/calendar/week?shop_id=${shopId}&week=${weekStart}`;
    
    // iframe環境の場合は親ウィンドウに通知
    if (window.self !== window.top && window.parent.postMessage) {
        window.parent.postMessage({
            type: 'calendar-date-selected',
            date: dateStr,
            availability: availability,
            shopId: shopId,
            nextUrl: weekUrl
        }, '*');
    } else {
        // 通常環境では直接遷移
        window.location.href = weekUrl;
    }
}