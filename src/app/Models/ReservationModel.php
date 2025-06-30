<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ReservationEntity;
use CodeIgniter\I18n\Time;
use App\Enums\ReserveStatusCode;

class ReservationModel extends Model
{
    protected $table            = 'reservations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = ReservationEntity::class;
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'reservation_no',
        'reservation_status_id',
        'reservation_guid',
        'work_type_id',
        'shop_id',
        'desired_date',
        'desired_time_slot_id',
        'reservation_start_time',
        'reservation_end_time',
        'customer_name',
        'customer_kana',
        'email',
        'line_display_name',
        'via_line',
        'phone_number1',
        'phone_number2',
        'postal_code',
        'address',
        'vehicle_license_region',
        'vehicle_license_class',
        'vehicle_license_kana',
        'vehicle_license_number',
        'vehicle_model_name',
        'first_registration_date',
        'shaken_expiration_date',
        'model_spec_number',
        'classification_number',
        'loaner_usage',
        'loaner_name',
        'customer_requests',
        'notes',
        'next_inspection_date',
        'send_inspection_notice',
        'next_work_type_id',
        'next_contact_date',
        'inspection_notice_sent',
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $validationRules = [
        'reservation_status_id'   => 'required|integer|is_not_unique[reserve_statuses.id]',
        'work_type_id'            => 'required|integer|is_not_unique[work_types.id]',
        'shop_id'                 => 'required|integer|is_not_unique[shops.id]',
        'desired_date'            => 'required|valid_date',
        'desired_time_slot_id'    => 'permit_empty|integer|is_not_unique[time_slots.id]',
        'reservation_start_time'  => 'permit_empty|regex_match[/^([01]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/]', // HH:MM or HH:MM:SS
        'reservation_end_time'    => 'permit_empty|regex_match[/^([01]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/]', // HH:MM or HH:MM:SS
        'customer_name'           => 'required|string|max_length[50]',
        'email'                   => 'required|valid_email|max_length[255]',
        'via_line'                => 'required|in_list[0,1]',
        'phone_number1'           => 'required|string|max_length[20]',
        'vehicle_license_number'  => 'required|string|max_length[5]',
        'send_inspection_notice'  => 'required|in_list[0,1]', // 次回点検案内を送信するか
        'inspection_notice_sent'  => 'required|in_list[0,1]', // 次回点検案内が送信済みか
        // 他のフィールドに対するルールも必要に応じて追加
    ];

    protected $validationMessages = [
        'email' => [
            'valid_email' => '有効なメールアドレスの形式で入力してください。',
            'required'    => 'メールアドレスは必須です。',
        ],
        'customer_name' => [
            'required' => 'お客様氏名は必須です。',
        ],
    ];
    
    protected $skipValidation     = false;
    protected $beforeInsert       = ['generateReservationNo'];

    /**
     * 予約番号 (YYMMNNNN) を自動生成します。
     * 同じ年月内で連番を生成する簡易的な例です。
     */
    protected function generateReservationNo(array $data): array
    {
        if (empty($data['data']['reservation_no'])) {
            $prefix = date('ym');
            
            $lastReservation = $this->selectMax('reservation_no', 'max_no')
                                    ->like('reservation_no', $prefix, 'after')
                                    ->first();
            
            $nextNumber = 1;
            if ($lastReservation && isset($lastReservation->max_no) && strpos($lastReservation->max_no, $prefix) === 0) {
                $lastNumberStr = substr($lastReservation->max_no, strlen($prefix));
                if (is_numeric($lastNumberStr)) {
                    $nextNumber = (int)$lastNumberStr + 1;
                }
            }
            
            $data['data']['reservation_no'] = $prefix . sprintf('%04d', $nextNumber);
        }
        return $data;
    }

    /**
     * 検索条件をクエリビルダーに適用します。
     * @param \CodeIgniter\Model $model
     * @param array $params
     */
    public function buildSearchConditions($model, array $params): void
    {
        // 名前検索（部分一致）
        if (!empty($params['customer_name'])) {
            $model->like('customer_name', $params['customer_name']);
        }

        // 車番検索（部分一致、どの車番フィールドにも対応）
        if (!empty($params['vehicle_number'])) {
            $model->groupStart()
                  ->like('vehicle_license_region', $params['vehicle_number'])
                  ->orLike('vehicle_license_class', $params['vehicle_number'])
                  ->orLike('vehicle_license_kana', $params['vehicle_number'])
                  ->orLike('vehicle_license_number', $params['vehicle_number'])
                  ->groupEnd();
        }

        // LINE識別名検索（部分一致）
        if (!empty($params['line_display_name'])) {
            $model->like('line_display_name', $params['line_display_name']);
        }

        // 日付範囲検索
        if (!empty($params['date_from'])) {
            $model->where('desired_date >=', $params['date_from']);
        }
        if (!empty($params['date_to'])) {
            $model->where('desired_date <=', $params['date_to']);
        }

        // 作業種別複数選択
        if (!empty($params['work_type_ids']) && is_array($params['work_type_ids'])) {
            $model->whereIn('work_type_id', $params['work_type_ids']);
        }

        // 店舗絞り込み
        if (!empty($params['shop_id'])) {
            $model->where('shop_id', $params['shop_id']);
        }

        // 予約状況絞り込み
        if (!empty($params['status_id'])) {
            $model->where('reservation_status_id', $params['status_id']);
        }

        // 特別な検索条件（クイック検索用）
        $this->applyQuickSearchConditions($model, $params);
    }

    /**
     * クイック検索条件を適用します。
     * @param \CodeIgniter\Model $model
     * @param array $params
     */
    private function applyQuickSearchConditions($model, array $params): void
    {
        $quickSearch = $params['quick_search'] ?? null;

        switch ($quickSearch) {
            case 'today':
                // 本日の作業
                $today = date('Y-m-d');
                $model->where('desired_date', $today);
                break;

            case 'incomplete':
                // 未完了（未確定 + 予約確定）
                $model->whereIn('reservation_status_id', [
                    ReserveStatusCode::PENDING->id(), 
                    ReserveStatusCode::CONFIRMED->id()
                ]);
                break;

            case 'this_month_completed':
                // 今月整備完了予定
                $firstDay = date('Y-m-01');
                $lastDay = date('Y-m-t');
                $model->where('desired_date >=', $firstDay);
                $model->where('desired_date <=', $lastDay);
                $model->where('reservation_status_id', ReserveStatusCode::COMPLETED->id());
                break;

            case 'main_shop':
                // 本社作業（shop_id = 2 の想定）
                $model->where('shop_id', 2);
                break;

            case 'clear_shop':
                // Clear車検店作業（shop_id = 1 の想定）
                $model->where('shop_id', 1);
                break;
        }
    }

    /**
     * CSVエクスポート用のデータを取得します。
     * @param array $params 検索条件
     * @return array
     */
    public function getExportData(array $params = []): array
    {
        $builder = $this->builder();
        $this->buildSearchConditions($builder, $params);
        
        // JOINして関連データも含める
        $builder->select('
            reservations.*,
            reserve_statuses.name as status_name,
            work_types.name as work_type_name,
            shops.name as shop_name,
            vehicle_types.name as vehicle_type_name
        ')
        ->join('reserve_statuses', 'reserve_statuses.id = reservations.reservation_status_id', 'left')
        ->join('work_types', 'work_types.id = reservations.work_type_id', 'left')
        ->join('shops', 'shops.id = reservations.shop_id', 'left')
        ->join('vehicle_types', 'vehicle_types.id = reservations.vehicle_type_id', 'left');

        $builder->orderBy('desired_date', 'DESC')
               ->orderBy('id', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * 統計情報を取得します。
     * @param array $params 検索条件
     * @return array
     */
    public function getStatistics(array $params = []): array
    {
        // 基本統計（コピーを作成）
        $tempModel = clone $this;
        $tempModel->buildSearchConditions($tempModel, $params);
        $total = $tempModel->countAllResults();

        // ステータス別集計
        $tempModel2 = clone $this;
        $tempModel2->buildSearchConditions($tempModel2, $params);
        $statusCounts = $tempModel2->select('reservation_status_id, COUNT(*) as count')
            ->groupBy('reservation_status_id')
            ->findAll();

        // 作業種別別集計
        $tempModel3 = clone $this;
        $tempModel3->buildSearchConditions($tempModel3, $params);
        $workTypeCounts = $tempModel3->select('work_type_id, COUNT(*) as count')
            ->groupBy('work_type_id')
            ->findAll();

        // 結果を配列に変換
        $statusArray = [];
        foreach ($statusCounts as $status) {
            if (is_object($status)) {
                $statusArray[] = $status->toArray();
            } else {
                $statusArray[] = $status;
            }
        }

        $workTypeArray = [];
        foreach ($workTypeCounts as $workType) {
            if (is_object($workType)) {
                $workTypeArray[] = $workType->toArray();
            } else {
                $workTypeArray[] = $workType;
            }
        }

        return [
            'total' => $total,
            'by_status' => $statusArray,
            'by_work_type' => $workTypeArray,
        ];
    }
}