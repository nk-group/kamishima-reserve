<?php

namespace App\Validation;

use App\Models\ShopModel;
use App\Models\ShopClosingDayModel;

/**
 * カスタムバリデーションルール
 * ファイル名: src/app/Validation/CustomRules.php
 */
class CustomRules
{
    /**
     * 店舗が存在するかをチェック
     *
     * @param string $value
     * @param string|null $error
     * @return bool
     */
    public function shop_exists($value, ?string &$error = null): bool
    {
        if (empty($value)) {
            return true; // 必須チェックは他のルールで行う
        }

        $shopModel = new ShopModel();
        $shop = $shopModel->where('id', $value)
                         ->where('active', 1)
                         ->first();

        if (!$shop) {
            $error = '指定された店舗が見つからないか、無効な店舗です。';
            return false;
        }

        return true;
    }

    /**
     * 店舗内での定休日名の重複チェック
     *
     * @param string $value
     * @param string $fields カンマ区切りのフィールド名（shop_id,id）
     * @param array $data
     * @param string|null $error
     * @return bool
     */
    public function unique_holiday_name_per_shop($value, string $fields, array $data, ?string &$error = null): bool
    {
        if (empty($value)) {
            return true; // 必須チェックは他のルールで行う
        }

        // パラメータを解析
        $params = explode(',', $fields);
        $shopIdField = $params[0] ?? 'shop_id';
        $idField = $params[1] ?? 'id';

        $shopId = $data[$shopIdField] ?? null;
        $currentId = $data[$idField] ?? null;

        if (empty($shopId)) {
            return true; // shop_idが無い場合はスキップ
        }

        $shopClosingDayModel = new ShopClosingDayModel();
        $builder = $shopClosingDayModel->where('shop_id', $shopId)
                                     ->where('holiday_name', $value);

        // 更新時は自分自身を除外
        if (!empty($currentId)) {
            $builder->where('id !=', $currentId);
        }

        $existing = $builder->first();

        if ($existing) {
            $error = 'この店舗では既に同じ名前の定休日が登録されています。';
            return false;
        }

        return true;
    }

    /**
     * 単発の定休日が未来の日付かをチェック
     *
     * @param string $value
     * @param string $fields カンマ区切りのフィールド名（repeat_type）
     * @param array $data
     * @param string|null $error
     * @return bool
     */
    public function future_date_for_single($value, string $fields, array $data, ?string &$error = null): bool
    {
        if (empty($value)) {
            return true; // 必須チェックは他のルールで行う
        }

        $params = explode(',', $fields);
        $repeatTypeField = $params[0] ?? 'repeat_type';
        $repeatType = $data[$repeatTypeField] ?? null;

        // 単発（repeat_type = 0）の場合のみチェック
        if ($repeatType == 0) {
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$date) {
                return true; // 日付形式チェックは他のルールで行う
            }

            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            $date->setTime(0, 0, 0);

            if ($date < $today) {
                $error = '単発の定休日は今日以降の日付を入力してください。';
                return false;
            }
        }

        return true;
    }

    /**
     * 終了日が開始日以降かをチェック
     *
     * @param string $value
     * @param string $fields カンマ区切りのフィールド名（start_field）
     * @param array $data
     * @param string|null $error
     * @return bool
     */
    public function check_end_date_after_start($value, string $fields, array $data, ?string &$error = null): bool
    {
        if (empty($value)) {
            return true; // 任意項目の場合はスキップ
        }

        $params = explode(',', $fields);
        $startField = $params[0] ?? 'closing_date';
        
        // 一括作成の場合は start_date を使用
        if (isset($data['start_date'])) {
            $startField = 'start_date';
        }

        $startDate = $data[$startField] ?? null;

        if (empty($startDate)) {
            return true; // 開始日が無い場合はスキップ
        }

        $start = \DateTime::createFromFormat('Y-m-d', $startDate);
        $end = \DateTime::createFromFormat('Y-m-d', $value);

        if (!$start || !$end) {
            return true; // 日付形式チェックは他のルールで行う
        }

        if ($end < $start) {
            if ($startField === 'start_date') {
                $error = '終了日は開始日以降の日付を入力してください。';
            } else {
                $error = '繰り返し終了日は休業日以降の日付を入力してください。';
            }
            return false;
        }

        return true;
    }
}