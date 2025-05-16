<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ReservationEntity;
use CodeIgniter\I18n\Time;

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
        'work_type_id',
        'shop_id',
        'desired_date',
        'desired_time_slot_id',
        'reservation_start_time',
        'reservation_end_time',
        'customer_name',
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
        'vehicle_type_id',
        'vehicle_model_name',
        'shaken_expiration_date',
        'notes',
        'next_inspection_date',   // 次回推奨点検日
        'send_inspection_notice', // 次回点検案内を送信するか (フラグ)
        'next_contact_date',      // 次回顧客コンタクト予定日
        'inspection_notice_sent', // 次回点検案内が送信済みか (フラグ)
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
     * 指定された条件で予約を検索します。
     * @param array $params 検索条件
     * @return array<\App\Entities\ReservationEntity>
     */
    public function searchReservations(array $params = []): array
    {
        $builder = $this; 

        if (!empty($params['date_from'])) {
            $builder->where('desired_date >=', $params['date_from']);
        }
        if (!empty($params['date_to'])) {
            $builder->where('desired_date <=', $params['date_to']);
        }
        if (!empty($params['status_id'])) {
            $builder->where('reservation_status_id', $params['status_id']);
        }
        // ... 他の検索条件 ...

        return $builder->orderBy('desired_date', 'DESC')
                       ->orderBy('id', 'DESC')
                       ->findAll();
    }
}