<?php

/**
 * 定休日マスタ関連のヘルパー関数
 * ファイル名: src/app/Helpers/shop_closing_day_helper.php
 */

if (!function_exists('is_closing_day')) {
    /**
     * 指定した日付が定休日かどうかを判定
     *
     * @param int $shopId 店舗ID
     * @param string $date 日付 (Y-m-d形式)
     * @return bool
     */
    function is_closing_day(int $shopId, string $date): bool
    {
        $shopClosingDayModel = model('App\Models\ShopClosingDayModel');
        return $shopClosingDayModel->isClosingDay($shopId, $date);
    }
}

if (!function_exists('get_closing_days_in_period')) {
    /**
     * 指定期間の定休日一覧を取得
     *
     * @param int $shopId 店舗ID
     * @param string $startDate 開始日 (Y-m-d形式)
     * @param string $endDate 終了日 (Y-m-d形式)
     * @return array
     */
    function get_closing_days_in_period(int $shopId, string $startDate, string $endDate): array
    {
        $shopClosingDayModel = model('App\Models\ShopClosingDayModel');
        return $shopClosingDayModel->getClosingDaysInPeriod($shopId, $startDate, $endDate);
    }
}

if (!function_exists('format_closing_date_japanese')) {
    /**
     * 日付を日本語形式でフォーマット
     *
     * @param string $date 日付 (Y-m-d形式)
     * @return string
     */
    function format_closing_date_japanese(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $dateObj = new DateTime($date);
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $weekdays[(int)$dateObj->format('w')];
        
        return $dateObj->format('Y年n月j日') . '(' . $weekday . ')';
    }
}

if (!function_exists('get_weekday_name')) {
    /**
     * 曜日名を取得
     *
     * @param string $date 日付 (Y-m-d形式)
     * @return string
     */
    function get_weekday_name(string $date): string
    {
        if (empty($date)) {
            return '';
        }

        $dateObj = new DateTime($date);
        $weekdays = ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'];
        
        return $weekdays[(int)$dateObj->format('w')];
    }
}

if (!function_exists('get_repeat_type_badge')) {
    /**
     * 繰り返し種別のバッジHTMLを生成
     *
     * @param int $repeatType 繰り返し種別
     * @return string
     */
    function get_repeat_type_badge(int $repeatType): string
    {
        switch ($repeatType) {
            case 0:
                return '<span class="badge bg-secondary">単発</span>';
            case 1:
                return '<span class="badge bg-primary">毎週</span>';
            case 2:
                return '<span class="badge bg-success">毎年</span>';
            default:
                return '<span class="badge bg-light text-dark">不明</span>';
        }
    }
}

if (!function_exists('get_active_status_badge')) {
    /**
     * 有効/無効ステータスのバッジHTMLを生成
     *
     * @param bool $isActive 有効フラグ
     * @return string
     */
    function get_active_status_badge(bool $isActive): string
    {
        if ($isActive) {
            return '<span class="badge bg-success">有効</span>';
        } else {
            return '<span class="badge bg-danger">無効</span>';
        }
    }
}

if (!function_exists('validate_closing_day_date')) {
    /**
     * 定休日の日付妥当性をチェック
     *
     * @param string $date 日付 (Y-m-d形式)
     * @param int $repeatType 繰り返し種別
     * @return array ['valid' => bool, 'message' => string]
     */
    function validate_closing_day_date(string $date, int $repeatType): array
    {
        if (empty($date)) {
            return ['valid' => false, 'message' => '日付が入力されていません。'];
        }

        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            return ['valid' => false, 'message' => '正しい日付形式で入力してください。'];
        }

        // 単発の場合は未来の日付のみ許可
        if ($repeatType === 0) {
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            $dateObj->setTime(0, 0, 0);
            
            if ($dateObj < $today) {
                return ['valid' => false, 'message' => '単発の定休日は今日以降の日付を入力してください。'];
            }
        }

        return ['valid' => true, 'message' => ''];
    }
}

if (!function_exists('get_shop_list_for_select')) {
    /**
     * セレクトボックス用の店舗一覧を取得
     *
     * @return array [店舗ID => 店舗名]
     */
    function get_shop_list_for_select(): array
    {
        $shopModel = model('App\Models\ShopModel');
        $shops = $shopModel->where('active', 1)
                          ->orderBy('sort_order', 'ASC')
                          ->findAll();
        
        $options = [];
        foreach ($shops as $shop) {
            $options[$shop->id] = $shop->name;
        }
        
        return $options;
    }
}

if (!function_exists('get_closing_days_calendar_data')) {
    /**
     * カレンダー表示用の定休日データを取得
     *
     * @param int $shopId 店舗ID
     * @param int $year 年
     * @param int $month 月
     * @return array
     */
    function get_closing_days_calendar_data(int $shopId, int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate)); // 月末日
        
        $shopClosingDayModel = model('App\Models\ShopClosingDayModel');
        $closingDays = $shopClosingDayModel->getClosingDaysInPeriod($shopId, $startDate, $endDate);
        
        $calendarData = [];
        foreach ($closingDays as $closingDay) {
            $day = (int)date('j', strtotime($closingDay['date']));
            $calendarData[$day] = [
                'name' => $closingDay['name'],
                'repeat_type' => $closingDay['repeat_type']
            ];
        }
        
        return $calendarData;
    }
}

if (!function_exists('get_next_business_day')) {
    /**
     * 指定日以降の次の営業日を取得
     *
     * @param int $shopId 店舗ID
     * @param string $date 基準日 (Y-m-d形式)
     * @param int $maxDays 最大検索日数（デフォルト: 30日）
     * @return string|null 次の営業日 (Y-m-d形式) または null
     */
    function get_next_business_day(int $shopId, string $date, int $maxDays = 30): ?string
    {
        $currentDate = new DateTime($date);
        $shopClosingDayModel = model('App\Models\ShopClosingDayModel');
        
        for ($i = 0; $i < $maxDays; $i++) {
            $checkDate = $currentDate->format('Y-m-d');
            
            if (!$shopClosingDayModel->isClosingDay($shopId, $checkDate)) {
                return $checkDate;
            }
            
            $currentDate->add(new DateInterval('P1D'));
        }
        
        return null;
    }
}