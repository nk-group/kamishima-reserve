<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\ReservationModel;
use App\Models\ShopModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * 管理者向けダッシュボードコントローラー
 */
class DashboardController extends BaseController
{
    protected ReservationModel $reservationModel;
    protected ShopModel $shopModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->shopModel = new ShopModel();
    }

    /**
     * ダッシュボードメイン画面
     */
    public function index(): string
    {
        try {
            // リクエストパラメータ取得
            $currentMonth = $this->request->getGet('month') ?? date('Y-m');
            $selectedShopId = $this->request->getGet('shop_id');
            
            // 月の妥当性チェック
            if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
                $currentMonth = date('Y-m');
            }
            
            // 店舗IDの妥当性チェック
            if (!empty($selectedShopId) && !is_numeric($selectedShopId)) {
                $selectedShopId = null;
            } else {
                $selectedShopId = !empty($selectedShopId) ? (int)$selectedShopId : null;
            }
            
            // データ取得
            $todayReservations = $this->reservationModel->getTodayReservations($selectedShopId);
            $calendarData = $this->reservationModel->getCalendarData($currentMonth, $selectedShopId);
            $statistics = $this->reservationModel->getMonthlyStatistics($currentMonth, $selectedShopId);
            $closingDays = $this->reservationModel->getShopClosingDays($currentMonth, $selectedShopId);
            
            // ShopModelのメソッド確認
            try {
                $shops = method_exists($this->shopModel, 'findActiveShops') 
                    ? $this->shopModel->findActiveShops() 
                    : $this->shopModel->findAll();
            } catch (\Throwable $e) {
                log_message('error', '[DashboardController::index] Shop model error: ' . $e->getMessage());
                $shops = [];
            }
            
            // カレンダー表示用データの準備
            $calendarViewData = $this->prepareCalendarViewData($currentMonth, $calendarData, $closingDays);
            
            $data = [
                'page_title' => 'ダッシュボード | 車検予約管理システム',
                'h1_title' => 'ダッシュボード',
                'body_id' => 'page-admin-dashboard',
                
                // 本日の予約データ
                'today_reservations' => $todayReservations,
                'today_date' => date('Y年n月j日'),
                
                // カレンダーデータ
                'calendar_view_data' => $calendarViewData,
                'current_month' => $currentMonth,
                'current_month_display' => date('Y年n月', strtotime($currentMonth . '-01')),
                
                // 統計情報
                'statistics' => $statistics,
                
                // 選択肢データ
                'shops' => $shops,
                'selected_shop_id' => $selectedShopId,
                'selected_shop_name' => $this->getSelectedShopName($shops, $selectedShopId),
                
                // ナビゲーション用
                'prev_month' => date('Y-m', strtotime($currentMonth . '-01 -1 month')),
                'next_month' => date('Y-m', strtotime($currentMonth . '-01 +1 month')),
            ];
            
            return $this->render('Admin/Dashboard/index', $data);
            
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Dashboard display error: ' . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                throw $e;
            }
            
            // エラー時は基本的なダッシュボードを表示
            return $this->render('Admin/Dashboard/index', [
                'page_title' => 'ダッシュボード | 車検予約管理システム',
                'h1_title' => 'ダッシュボード',
                'body_id' => 'page-admin-dashboard',
                'error_message' => 'データの取得中にエラーが発生しました。しばらく経ってから再度お試しください。',
                'today_reservations' => [],
                'calendar_view_data' => [],
                'statistics' => ['total' => 0, 'clear_shaken' => 0, 'general_maintenance' => 0, 'other' => 0],
                'shops' => [],
                'current_month' => date('Y-m'),
                'current_month_display' => date('Y年n月'),
            ]);
        }
    }

    /**
     * Ajax用カレンダーデータ取得
     */
    public function calendarData(): ResponseInterface
    {
        try {
            $currentMonth = $this->request->getGet('month') ?? date('Y-m');
            $selectedShopId = $this->request->getGet('shop_id');
            
            // バリデーション
            if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
                return $this->response->setJSON(['error' => '無効な月形式です'], 400);
            }
            
            if (!empty($selectedShopId) && !is_numeric($selectedShopId)) {
                return $this->response->setJSON(['error' => '無効な店舗IDです'], 400);
            }
            
            $selectedShopId = !empty($selectedShopId) ? (int)$selectedShopId : null;
            
            // データ取得
            $calendarData = $this->reservationModel->getCalendarData($currentMonth, $selectedShopId);
            $statistics = $this->reservationModel->getMonthlyStatistics($currentMonth, $selectedShopId);
            $closingDays = $this->reservationModel->getShopClosingDays($currentMonth, $selectedShopId);
            
            // カレンダー表示用データの準備
            $calendarViewData = $this->prepareCalendarViewData($currentMonth, $calendarData, $closingDays);
            
            return $this->response->setJSON([
                'success' => true,
                'calendar_data' => $calendarViewData,
                'statistics' => $statistics,
                'current_month_display' => date('Y年n月', strtotime($currentMonth . '-01')),
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Calendar data ajax error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'データの取得中にエラーが発生しました。'
            ], 500);
        }
    }

    /**
     * Ajax用カレンダーテーブル取得（新規追加）
     */
    public function calendarTable(): ResponseInterface
    {
        try {
            $currentMonth = $this->request->getGet('month') ?? date('Y-m');
            $selectedShopId = $this->request->getGet('shop_id');
            
            // バリデーション
            if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
                return $this->response->setStatusCode(400)->setBody('無効な月形式です');
            }
            
            if (!empty($selectedShopId) && !is_numeric($selectedShopId)) {
                return $this->response->setStatusCode(400)->setBody('無効な店舗IDです');
            }
            
            $selectedShopId = !empty($selectedShopId) ? (int)$selectedShopId : null;
            
            // データ取得
            $calendarData = $this->reservationModel->getCalendarData($currentMonth, $selectedShopId);
            $closingDays = $this->reservationModel->getShopClosingDays($currentMonth, $selectedShopId);
            
            // カレンダー表示用データの準備
            $calendarViewData = $this->prepareCalendarViewData($currentMonth, $calendarData, $closingDays);
            
            // パーシャルビューをレンダリング
            $html = view('Admin/Dashboard/_calendar_table', [
                'calendar_view_data' => $calendarViewData,
                'selected_shop_id' => $selectedShopId
            ]);
            
            return $this->response->setBody($html);
            
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Calendar table ajax error: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setBody('
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    カレンダーテーブルの取得中にエラーが発生しました。
                </div>
            ');
        }
    }

    /**
     * 本日予約の「もっと見る」用Ajax
     */
    public function todayReservationsMore(): ResponseInterface
    {
        try {
            $selectedShopId = $this->request->getGet('shop_id');
            
            if (!empty($selectedShopId) && !is_numeric($selectedShopId)) {
                return $this->response->setJSON(['error' => '無効な店舗IDです'], 400);
            }
            
            $selectedShopId = !empty($selectedShopId) ? (int)$selectedShopId : null;
            
            $todayReservations = $this->reservationModel->getTodayReservations($selectedShopId);
            
            return $this->response->setJSON([
                'success' => true,
                'reservations' => $todayReservations,
                'total_count' => count($todayReservations)
            ]);
            
        } catch (\Throwable $e) {
            log_message('error', '[' . __CLASS__ . '::' . __FUNCTION__ . '] Today reservations more ajax error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'データの取得中にエラーが発生しました。'
            ], 500);
        }
    }

    /**
     * カレンダー表示用データを準備
     * 
     * @param string $month YYYY-MM形式
     * @param array $calendarData 予約データ
     * @param array $closingDays 休日データ
     * @return array
     */
    private function prepareCalendarViewData(string $month, array $calendarData, array $closingDays): array
    {
        try {
            $firstDay = new \DateTime($month . '-01');
            $lastDay = new \DateTime($month . '-' . $firstDay->format('t'));
            
            // デバッグ用ログ
            log_message('debug', '[DashboardController::prepareCalendarViewData] Processing month: ' . $month);
            log_message('debug', '[DashboardController::prepareCalendarViewData] Calendar data count: ' . count($calendarData));
            log_message('debug', '[DashboardController::prepareCalendarViewData] Closing days count: ' . count($closingDays));
            log_message('debug', '[DashboardController::prepareCalendarViewData] Closing days: ' . json_encode($closingDays));
            
            // カレンダーのグリッドを準備（6週間分）
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
                    $isCurrentMonth = $currentDate->format('Y-m') === $month;
                    $isToday = $dateStr === date('Y-m-d');
                    $isWeekend = in_array((int)$currentDate->format('w'), [0, 6]); // 日曜日、土曜日
                    $isHoliday = isset($closingDays[$dateStr]);
                    
                    // その日の予約データ（定休日でも予約データは表示）
                    $dayReservations = $calendarData[$dateStr] ?? [];
                    
                    // 作業種別別件数を集計
                    $workTypeCounts = [];
                    foreach ($dayReservations as $reservation) {
                        $workTypeCode = $reservation['work_type_code'] ?? 'other';
                        $workTypeCounts[$workTypeCode] = ($workTypeCounts[$workTypeCode] ?? 0) + 1;
                    }
                    
                    // デバッグ用ログ（該当日に予約または定休日がある場合のみ）
                    if (!empty($dayReservations) || $isHoliday) {
                        log_message('debug', "[DashboardController::prepareCalendarViewData] Date: {$dateStr}, Reservations: " . count($dayReservations) . ", Holiday: " . ($isHoliday ? 'Yes' : 'No'));
                    }
                    
                    $calendar[$week][$day] = [
                        'date' => $dateStr,
                        'day_num' => $dayNum,
                        'is_current_month' => $isCurrentMonth,
                        'is_today' => $isToday,
                        'is_weekend' => $isWeekend,
                        'is_holiday' => $isHoliday,
                        'holiday_name' => $closingDays[$dateStr] ?? null,
                        'reservations' => $dayReservations,
                        'reservation_count' => count($dayReservations),
                        'work_type_counts' => $workTypeCounts,
                    ];
                    
                    $currentDate->add(new \DateInterval('P1D'));
                }
            }
            
            log_message('debug', '[DashboardController::prepareCalendarViewData] Generated calendar weeks: ' . count($calendar));
            return $calendar;
            
        } catch (\Throwable $e) {
            log_message('error', '[DashboardController::prepareCalendarViewData] Error: ' . $e->getMessage());
            return []; // 空配列を返してエラーを防ぐ
        }
    }

    /**
     * 選択された店舗名を取得
     * 
     * @param array $shops 店舗リスト
     * @param int|null $selectedShopId 選択された店舗ID
     * @return string
     */
    private function getSelectedShopName(array $shops, ?int $selectedShopId): string
    {
        if (empty($selectedShopId)) {
            return '全店舗';
        }
        
        foreach ($shops as $shop) {
            if ($shop->id === $selectedShopId) {
                return $shop->name;
            }
        }
        
        return '不明な店舗';
    }
}