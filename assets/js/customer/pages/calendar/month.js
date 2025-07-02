//==========================================================================
// Customer Calendar Month Page JavaScript - Clear車検予約システム顧客向け
//==========================================================================

/**
 * 月表示カレンダーページの初期化
 * admin dashboard構造に準拠したシンプルな実装
 */
export function initCalendarMonth() {
    console.log('Customer Calendar Month page initialized');
    
    // DOM要素の取得
    const calendarData = document.getElementById('calendar-month-data');
    const prevMonthBtn = document.getElementById('prev-month-btn');
    const nextMonthBtn = document.getElementById('next-month-btn');
    const backToTodayBtn = document.getElementById('back-to-today-btn');
    
    if (!calendarData) {
        console.error('Calendar month data element not found');
        return;
    }
    
    // 初期データの取得
    let currentMonth = calendarData.dataset.currentMonth;
    const baseUrl = calendarData.dataset.baseUrl || '/customer/calendar/month';
    
    // イベントリスナーの設定
    setupEventListeners();
    
    // カレンダーセルのクリック処理設定
    setupCalendarCellClicks();
    
    /**
     * イベントリスナーの設定
     */
    function setupEventListeners() {
        // 前月ボタン
        if (prevMonthBtn) {
            prevMonthBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const date = new Date(currentMonth + '-01');
                date.setMonth(date.getMonth() - 1);
                currentMonth = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
                updateCalendar();
            });
        }
        
        // 次月ボタン
        if (nextMonthBtn) {
            nextMonthBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const date = new Date(currentMonth + '-01');
                date.setMonth(date.getMonth() + 1);
                currentMonth = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0');
                updateCalendar();
            });
        }
        
        // 今日に戻るボタン
        if (backToTodayBtn) {
            backToTodayBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const today = new Date();
                currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
                updateCalendar();
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
        
        // 選択した日付から週の開始日（月曜日）を計算
        const selectedDate = new Date(dateStr);
        const dayOfWeek = selectedDate.getDay(); // 0: 日曜日, 1: 月曜日, ...
        const mondayOffset = dayOfWeek === 0 ? -6 : -(dayOfWeek - 1); // 月曜日への日数差
        const mondayDate = new Date(selectedDate);
        mondayDate.setDate(selectedDate.getDate() + mondayOffset);
        
        // 週の開始日をYYYY-MM-DD形式で取得
        const weekStart = mondayDate.toISOString().split('T')[0];
        
        // 週表示カレンダーページに遷移（weekパラメータを使用）
        const weekUrl = `/customer/calendar/week?week=${weekStart}`;
        
        // iframe環境の場合は親ウィンドウに通知
        if (window.self !== window.top && window.parent.postMessage) {
            window.parent.postMessage({
                type: 'calendar-date-selected',
                date: dateStr,
                availability: availability,
                nextUrl: weekUrl
            }, '*');
        } else {
            // 通常環境では直接遷移
            window.location.href = weekUrl;
        }
    }
    
    /**
     * カレンダーの更新
     */
    function updateCalendar() {
        showLoading();
        
        const params = new URLSearchParams({
            month: currentMonth,
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
        // カレンダーテーブル本体の更新（.calendar-month内のテーブル）
        const calendarMonth = document.querySelector('.calendar-month');
        if (calendarMonth && data.html) {
            calendarMonth.innerHTML = data.html;
        }
        
        // ナビゲーション表示の更新（.calendar-title）
        const calendarTitle = document.getElementById('calendar-title');
        if (calendarTitle && data.month_display) {
            calendarTitle.textContent = data.month_display;
        }
        
        // カレンダーセルのクリック処理を再設定
        setupCalendarCellClicks();
        
        console.log('Calendar updated successfully');
    }
    
    /**
     * ローディング表示
     */
    function showLoading() {
        const calendarMonth = document.querySelector('.calendar-month');
        if (calendarMonth) {
            calendarMonth.innerHTML = `
                <div class="calendar-loading-overlay">
                    <div class="loading-content">
                        <div class="loading-spinner"></div>
                        <div class="loading-text">カレンダーを読み込み中...</div>
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
        const calendarMonth = document.querySelector('.calendar-month');
        if (calendarMonth) {
            calendarMonth.innerHTML = `
                <div class="calendar-empty-state">
                    <div class="empty-icon">⚠️</div>
                    <div class="empty-title">エラーが発生しました</div>
                    <div class="empty-description">${escapeHtml(message)}</div>
                </div>
            `;
        }
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