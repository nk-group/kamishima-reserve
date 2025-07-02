<?php

namespace App\Controllers\Customer;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * 顧客向けカレンダーコントローラー
 * Clear車検予約システムの顧客向けカレンダー機能を提供します。
 */
class CalendarController extends BaseController
{
    protected $reservationModel;
    protected $shopModel;
    protected $timeSlotModel;
    protected $shopClosingDayModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        // モデルを初期化
        $this->reservationModel = model('App\Models\ReservationModel');
        $this->shopModel = model('App\Models\ShopModel');
        $this->timeSlotModel = model('App\Models\TimeSlotModel');
        $this->shopClosingDayModel = model('App\Models\ShopClosingDayModel');
    }

    /**
     * 月表示カレンダーページ
     */
    public function month()
    {
        try {
            $currentMonth = $this->request->getGet('month') ?? date('Y-m');
            
            // バリデーション
            if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
                return $this->showError('無効な月形式です。', 400);
            }

            // Ajax リクエストの場合
            if ($this->request->getGet('ajax') === '1') {
                return $this->getMonthCalendarData($currentMonth);
            }

            // カレンダーデータ取得（Clear車検のみ）
            $calendarData = $this->getCustomerCalendarData($currentMonth);

            $data = [
                'page_title' => 'Clear車検予約カレンダー | 上嶋自動車',
                'body_id' => 'page-customer-calendar-month',
                'current_month' => $currentMonth,
                'current_month_display' => date('Y年n月', strtotime($currentMonth . '-01')),
                'calendar_data' => $calendarData,
            ];

            return $this->render('Customer/Calendar/month', $data);

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Month calendar error: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->showError('カレンダーの取得に失敗しました。再度お試しください。', 500);
        }
    }

    /**
     * 週表示カレンダーページ
     */
    public function week()
    {
        try {
            $currentWeekStart = $this->request->getGet('week') ?? date('Y-m-d', strtotime('monday this week'));
            
            // バリデーション
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $currentWeekStart)) {
                return $this->showError('無効な日付形式です。', 400);
            }

            // Ajax リクエストの場合
            if ($this->request->getGet('ajax') === '1') {
                return $this->getWeekCalendarData($currentWeekStart);
            }

            // 週表示データ取得
            $weekData = $this->getCustomerWeekData($currentWeekStart);

            $data = [
                'page_title' => 'Clear車検予約 週表示 | 上嶋自動車',
                'body_id' => 'page-customer-calendar-week',
                'current_week_start' => $currentWeekStart,
                'current_week_display' => $this->formatWeekDisplay($currentWeekStart),
                'week_dates' => $weekData['week_dates'],
                'time_slots' => $weekData['time_slots'],
            ];

            return $this->render('Customer/Calendar/week', $data);

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Week calendar error: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[' . __CLASS__ . '::' . __FUNCTION__ . '] File: ' . $e->getFile() . ', Line: ' . $e->getLine());
            }
            
            return $this->showError('カレンダーの取得に失敗しました。再度お試しください。', 500);
        }
    }

    /**
     * 月表示カレンダーデータをAjax用に取得
     */
    private function getMonthCalendarData(string $currentMonth): ResponseInterface
    {
        try {
            $calendarData = $this->getCustomerCalendarData($currentMonth);

            return $this->jsonResponse([
                'success' => true,
                'calendar_data' => $calendarData,
                'current_month_display' => date('Y年n月', strtotime($currentMonth . '-01')),
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Month calendar Ajax error: ' . $e->getMessage());
            
            return $this->jsonResponse([
                'success' => false,
                'error' => 'カレンダーデータの取得に失敗しました。'
            ], 500);
        }
    }

    /**
     * 週表示カレンダーデータをAjax用に取得
     */
    private function getWeekCalendarData(string $currentWeekStart): ResponseInterface
    {
        try {
            $weekData = $this->getCustomerWeekData($currentWeekStart);

            return $this->jsonResponse([
                'success' => true,
                'week_dates' => $weekData['week_dates'],
                'time_slots' => $weekData['time_slots'],
                'current_week_display' => $this->formatWeekDisplay($currentWeekStart),
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Week calendar Ajax error: ' . $e->getMessage());
            
            return $this->jsonResponse([
                'success' => false,
                'error' => 'カレンダーデータの取得に失敗しました。'
            ], 500);
        }
    }

    /**
     * 顧客向け月カレンダーデータを取得（Clear車検のみ）
     */
    private function getCustomerCalendarData(string $currentMonth): array
    {
        // Clear車検のみ対象とする（work_type_idで制限）
        $clearShakenWorkTypeId = 1; // Clear車検のID（実際の値に調整が必要）

        // 月の最初と最後の日を取得
        $firstDay = new \DateTime($currentMonth . '-01');
        $lastDay = new \DateTime($currentMonth . '-' . $firstDay->format('t'));

        // カレンダーグリッド用に6週間分の日付を生成
        $calendar = [];
        $currentDate = clone $firstDay;
        
        // 月初の曜日まで戻る（日曜日 = 0）
        $dayOfWeek = (int)$currentDate->format('w');
        if ($dayOfWeek > 0) {
            $currentDate->sub(new \DateInterval('P' . $dayOfWeek . 'D'));
        }

        // 6週間分のカレンダーを生成
        for ($week = 0; $week < 6; $week++) {
            $calendar[$week] = [];
            for ($day = 0; $day < 7; $day++) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayNum = (int)$currentDate->format('d');
                $isCurrentMonth = $currentDate->format('Y-m') === $currentMonth;
                $isToday = $dateStr === date('Y-m-d');
                $dayOfWeek = (int)$currentDate->format('w');

                // 定休日チェック
                $isHoliday = $this->shopClosingDayModel->isClosingDay(1, $dateStr); // 店舗ID=1固定（調整が必要）

                // 予約状況を判定
                $availabilityStatus = 'closed';
                $cssClasses = ['calendar-cell'];

                if ($isCurrentMonth && !$isHoliday && $dateStr >= date('Y-m-d')) {
                    // Clear車検の予約可能状況をチェック
                    $availabilityStatus = $this->getDateAvailabilityStatus($dateStr, $clearShakenWorkTypeId);
                    $cssClasses[] = 'clickable';
                }

                if ($isToday) {
                    $cssClasses[] = 'today';
                }

                if ($isHoliday) {
                    $cssClasses[] = 'holiday';
                }

                if (!$isCurrentMonth) {
                    $cssClasses[] = 'other-month';
                }

                $calendar[$week][] = [
                    'date' => $dateStr,
                    'day' => $dayNum,
                    'is_current_month' => $isCurrentMonth,
                    'is_today' => $isToday,
                    'day_of_week' => $dayOfWeek,
                    'availability_status' => $availabilityStatus,
                    'css_classes' => $cssClasses,
                ];

                $currentDate->add(new \DateInterval('P1D'));
            }
        }

        return $calendar;
    }

    /**
     * 顧客向け週表示データを取得
     */
    private function getCustomerWeekData(string $weekStart): array
    {
        // Clear車検のみ対象
        $clearShakenWorkTypeId = 1;

        // 週の日付を生成（月曜日開始）
        $weekDates = [];
        $currentDate = new \DateTime($weekStart);
        
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = (int)$currentDate->format('w');
            $isToday = $dateStr === date('Y-m-d');

            $weekDates[] = [
                'date' => $dateStr,
                'day' => (int)$currentDate->format('d'),
                'day_label' => ['日', '月', '火', '水', '木', '金', '土'][$dayOfWeek],
                'day_of_week' => $dayOfWeek,
                'is_today' => $isToday,
            ];

            $currentDate->add(new \DateInterval('P1D'));
        }

        // 時間帯データを取得
        $timeSlots = $this->timeSlotModel->where('is_active', 1)
                                         ->where('work_type_id', $clearShakenWorkTypeId)
                                         ->orderBy('start_time', 'ASC')
                                         ->findAll();

        $timeSlotsData = [];
        foreach ($timeSlots as $timeSlot) {
            $slots = [];
            
            // 各日付の予約状況をチェック
            foreach ($weekDates as $date) {
                $dateStr = $date['date'];
                $isHoliday = $this->shopClosingDayModel->isClosingDay(1, $dateStr);
                
                if ($isHoliday || $dateStr < date('Y-m-d')) {
                    $status = 'closed';
                } else {
                    $status = $this->getTimeSlotAvailabilityStatus($dateStr, $timeSlot->id);
                }

                $slots[$dateStr] = [
                    'status' => $status,
                    'time_slot_id' => $timeSlot->id,
                    'css_classes' => [$status],
                ];
            }

            $timeSlotsData[] = [
                'time_slot_id' => $timeSlot->id,
                'start_time_display' => $timeSlot->start_time,
                'duration_display' => '約' . $timeSlot->duration_minutes . '分',
                'slots' => $slots,
            ];
        }

        return [
            'week_dates' => $weekDates,
            'time_slots' => $timeSlotsData,
        ];
    }

    /**
     * 指定日の予約可能状況を判定
     */
    private function getDateAvailabilityStatus(string $date, int $workTypeId): string
    {
        // 仮実装：実際のロジックは予約データを元に判定
        // available: 余裕あり, limited: 残りわずか, full: 満席
        return 'available';
    }

    /**
     * 指定時間帯の予約可能状況を判定
     */
    private function getTimeSlotAvailabilityStatus(string $date, int $timeSlotId): string
    {
        // 仮実装：実際のロジックは予約データを元に判定
        return 'available';
    }

    /**
     * 週表示用の表示文字列を生成
     */
    private function formatWeekDisplay(string $weekStart): string
    {
        $startDate = new \DateTime($weekStart);
        $endDate = clone $startDate;
        $endDate->add(new \DateInterval('P6D'));

        return $startDate->format('Y年n月j日') . ' - ' . $endDate->format('Y年n月j日');
    }
}