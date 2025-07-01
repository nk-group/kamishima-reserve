/**
 * ダッシュボード用JavaScript
 * ファイル: assets/js/admin/pages/dashboard/index.js
 */

/**
 * ダッシュボードの初期化
 */
export function initDashboard() {
    console.log('Dashboard initialized.');
    
    // DOM要素の取得
    const dashboardData = document.getElementById('dashboard-data');
    const shopSelect = document.getElementById('shopSelect');
    const prevMonthBtn = document.getElementById('prev-month-btn');
    const nextMonthBtn = document.getElementById('next-month-btn');
    const refreshTodayBtn = document.getElementById('refresh-today-btn');
    const refreshCalendarBtn = document.getElementById('refresh-calendar-btn');
    const toggleMoreBtn = document.getElementById('toggle-more-reservations');
    
    if (!dashboardData) {
        console.error('Dashboard data element not found');
        return;
    }
    
    // 初期データの取得
    let currentMonth = dashboardData.dataset.currentMonth;
    let selectedShopId = dashboardData.dataset.selectedShopId || null;
    const prevMonth = dashboardData.dataset.prevMonth;
    const nextMonth = dashboardData.dataset.nextMonth;
    
    // イベントリスナーの設定
    setupEventListeners();
    
    /**
     * イベントリスナーの設定
     */
    function setupEventListeners() {
        // 店舗選択の変更
        if (shopSelect) {
            shopSelect.addEventListener('change', function() {
                selectedShopId = this.value || null;
                updateCalendar();
                updateTodayReservations();
            });
        }
        
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
        
        // 今日の予約更新ボタン
        if (refreshTodayBtn) {
            refreshTodayBtn.addEventListener('click', function() {
                updateTodayReservations();
            });
        }
        
        // カレンダー更新ボタン
        if (refreshCalendarBtn) {
            refreshCalendarBtn.addEventListener('click', function() {
                updateCalendar();
            });
        }
        
        // もっと見るボタン
        if (toggleMoreBtn) {
            toggleMoreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                toggleMoreReservations();
            });
        }
    }
    
    /**
     * カレンダーの更新
     */
    function updateCalendar() {
        const calendarContainer = document.getElementById('calendar-container');
        const calendarStats = document.getElementById('calendar-stats');
        const calendarMonth = document.getElementById('calendar-month');
        
        if (!calendarContainer) return;
        
        // ローディング表示
        showCalendarLoading();
        
        // Ajax リクエスト
        const params = new URLSearchParams({
            month: currentMonth
        });
        
        if (selectedShopId) {
            params.append('shop_id', selectedShopId);
        }
        
        fetch(`/admin/dashboard/calendar-data?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // カレンダーテーブルの更新
                    updateCalendarTable(data.calendar_data);
                    
                    // 統計情報の更新
                    if (calendarStats) {
                        calendarStats.innerHTML = `
                            合計件数　${data.statistics.total}件／
                            Clear車検　${data.statistics.clear_shaken}件／
                            一般整備　${data.statistics.general_maintenance}件／
                            その他　${data.statistics.other}件
                        `;
                    }
                    
                    // 月表示の更新
                    if (calendarMonth) {
                        calendarMonth.textContent = data.current_month_display;
                    }
                } else {
                    showCalendarError(data.error || 'データの取得に失敗しました。');
                }
            })
            .catch(error => {
                console.error('Calendar update error:', error);
                showCalendarError('ネットワークエラーが発生しました。');
            });
    }
    
    /**
     * 本日の予約の更新
     */
    function updateTodayReservations() {
        const scheduleGrid = document.getElementById('schedule-grid');
        
        if (!scheduleGrid) return;
        
        // ローディング表示
        showTodayLoading();
        
        const params = new URLSearchParams();
        if (selectedShopId) {
            params.append('shop_id', selectedShopId);
        }
        
        fetch(`/admin/dashboard/today-reservations-more?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTodayReservationsDisplay(data.reservations);
                } else {
                    showTodayError(data.error || 'データの取得に失敗しました。');
                }
            })
            .catch(error => {
                console.error('Today reservations update error:', error);
                showTodayError('ネットワークエラーが発生しました。');
            });
    }
    
    /**
     * もっと見るボタンの切り替え
     */
    function toggleMoreReservations() {
        const hiddenCards = document.querySelectorAll('.schedule-card-hidden');
        const toggleBtn = document.getElementById('toggle-more-reservations');
        
        if (!toggleBtn) return;
        
        const isExpanded = toggleBtn.dataset.expanded === 'true';
        
        hiddenCards.forEach(card => {
            if (isExpanded) {
                card.style.display = 'none';
            } else {
                card.style.display = 'block';
            }
        });
        
        // ボタンテキストの更新
        const showText = toggleBtn.dataset.showText || 'もっと見る';
        const hideText = toggleBtn.dataset.hideText || '表示を少なくする';
        const icon = toggleBtn.querySelector('i');
        
        if (isExpanded) {
            toggleBtn.innerHTML = `<i class="bi bi-arrow-right me-2"></i>${showText}`;
            toggleBtn.dataset.expanded = 'false';
        } else {
            toggleBtn.innerHTML = `<i class="bi bi-arrow-up me-2"></i>${hideText}`;
            toggleBtn.dataset.expanded = 'true';
        }
    }
    
    /**
     * カレンダーローディング表示
     */
    function showCalendarLoading() {
        const container = document.getElementById('calendar-container');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">読み込み中...</span>
                    </div>
                    <div class="mt-2">カレンダーを更新中...</div>
                </div>
            `;
        }
    }
    
    /**
     * 本日予約ローディング表示
     */
    function showTodayLoading() {
        const container = document.getElementById('schedule-grid');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">読み込み中...</span>
                    </div>
                    <div class="mt-2">予定を更新中...</div>
                </div>
            `;
        }
    }
    
    /**
     * カレンダーエラー表示
     */
    function showCalendarError(message) {
        const container = document.getElementById('calendar-container');
        if (container) {
            container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    ${escapeHtml(message)}
                </div>
            `;
        }
    }
    
    /**
     * 本日予約エラー表示
     */
    function showTodayError(message) {
        const container = document.getElementById('schedule-grid');
        if (container) {
            container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    ${escapeHtml(message)}
                </div>
            `;
        }
    }
    
    /**
     * カレンダーテーブルの更新
     */
    function updateCalendarTable(calendarData) {
        // サーバーサイドでレンダリングされたパーシャルビューを取得
        const params = new URLSearchParams({
            month: currentMonth
        });
        
        if (selectedShopId) {
            params.append('shop_id', selectedShopId);
        }
        
        // カレンダーテーブルパーシャル用の新しいエンドポイントを呼び出し
        fetch(`/admin/dashboard/calendar-table?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('calendar-container');
                if (container) {
                    container.innerHTML = html;
                }
            })
            .catch(error => {
                console.error('Calendar table update error:', error);
                // フォールバック: 動的生成を試行
                generateCalendarTableDynamic(calendarData);
            });
    }
    
    /**
     * カレンダーテーブルの動的生成（フォールバック）
     */
    function generateCalendarTableDynamic(calendarData) {
        const container = document.getElementById('calendar-container');
        if (!container || !calendarData) return;
        
        const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        
        let html = `
            <table class="calendar-table">
                <thead>
                    <tr>
                        ${weekdays.map(day => `<th>${day}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
        `;
        
        // カレンダーデータが配列形式であることを前提
        if (Array.isArray(calendarData)) {
            calendarData.forEach(week => {
                html += '<tr>';
                week.forEach(day => {
                    const classes = [];
                    if (!day.is_current_month) classes.push('other-month');
                    if (day.is_today) classes.push('today');
                    if (day.is_weekend) classes.push('weekend');
                    if (day.is_holiday) classes.push('holiday');
                    
                    html += `
                        <td class="${classes.join(' ')}" data-date="${day.date}">
                            <div class="date-number">${day.day_num}</div>
                            ${day.is_holiday ? `<div class="holiday-name">${escapeHtml(day.holiday_name || '')}</div>` : ''}
                            <div class="reservation-count">
                                ${day.reservation_count > 0 ? `${day.reservation_count}件` : ''}
                            </div>
                        </td>
                    `;
                });
                html += '</tr>';
            });
        }
        
        html += `
                </tbody>
            </table>
        `;
        
        container.innerHTML = html;
    }
    
    /**
     * 本日予約表示の更新
     */
    function updateTodayReservationsDisplay(reservations) {
        const container = document.getElementById('schedule-grid');
        const moreLink = document.querySelector('.more-link');
        
        if (!container) return;
        
        if (!reservations || reservations.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    本日の予約はありません。
                </div>
            `;
            if (moreLink) moreLink.style.display = 'none';
            return;
        }
        
        // 予約カードの生成
        let html = '';
        reservations.forEach((reservation, index) => {
            const isHidden = index >= 6;
            html += generateReservationCard(reservation, isHidden);
        });
        
        container.innerHTML = html;
        
        // もっと見るリンクの表示制御
        if (moreLink) {
            if (reservations.length > 6) {
                moreLink.style.display = 'block';
                const link = moreLink.querySelector('a');
                if (link) {
                    link.innerHTML = `<i class="bi bi-arrow-right me-2"></i>もっと見る（残り${reservations.length - 6}件）`;
                    link.dataset.expanded = 'false';
                }
            } else {
                moreLink.style.display = 'none';
            }
        }
    }
    
    /**
     * 予約カードのHTML生成
     */
    function generateReservationCard(reservation, isHidden = false) {
        const time = reservation.reservation_start_time 
            ? new Date('1970-01-01T' + reservation.reservation_start_time).toLocaleTimeString('ja-JP', {hour: '2-digit', minute: '2-digit'})
            : '--:--';
        
        const vehicleInfo = [
            reservation.vehicle_model_name || '',
            reservation.vehicle_license_region || '',
            reservation.vehicle_license_class || '',
            reservation.vehicle_license_kana || '',
            reservation.vehicle_license_number || ''
        ].filter(v => v).join(' ');
        
        const shakenDate = reservation.shaken_expiration_date 
            ? new Date(reservation.shaken_expiration_date).toLocaleDateString('ja-JP', {year: 'numeric', month: 'long', day: 'numeric'})
            : '';
        
        return `
            <div class="schedule-card${isHidden ? ' schedule-card-hidden' : ''}" 
                 style="${isHidden ? 'display: none;' : ''}">
                <div class="schedule-time">
                    <i class="bi bi-clock"></i>
                    ${time}
                </div>
                <div class="customer-name">
                    <i class="bi bi-person-circle"></i>
                    ${escapeHtml(reservation.customer_name)}様
                </div>
                <div class="work-type-badge badge-${reservation.work_type_code || 'other'}">
                    ${escapeHtml(reservation.work_type_name || '')}
                    ${reservation.shop_name ? `（${escapeHtml(reservation.shop_name)}）` : ''}
                </div>
                <div class="schedule-details">
                    <div><strong>連絡先：</strong>${escapeHtml(reservation.phone_number1 || '')}</div>
                    <div><strong>車種／車番：</strong>${escapeHtml(vehicleInfo)}</div>
                    ${shakenDate ? `<div><strong>車検満了：</strong>${shakenDate}</div>` : ''}
                </div>
            </div>
        `;
    }
    
    /**
     * HTMLエスケープ
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

/**
 * 新規予約作成
 */
export function createNewReservation(date, shopId) {
    const params = new URLSearchParams({
        date: date
    });
    
    if (shopId) {
        params.append('shop_id', shopId);
    }
    
    window.location.href = `/admin/reservations/new?${params.toString()}`;
}