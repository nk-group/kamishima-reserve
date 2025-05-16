<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;
// 関連エンティティやモデルをインポート
use App\Models\ReserveStatusModel;
use App\Models\WorkTypeModel;
use App\Models\ShopModel;
use App\Models\TimeSlotModel;
use App\Models\VehicleTypeModel;
use App\Enums\ReserveStatusCode;
use App\Enums\WorkTypeCode;

class ReservationEntity extends Entity
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'id'                       => null,
        'reservation_no'           => null,
        'reservation_status_id'    => null,
        'work_type_id'             => null,
        'shop_id'                  => null,
        'desired_date'             => null,
        'desired_time_slot_id'     => null,
        'reservation_start_time'   => null,
        'reservation_end_time'     => null,
        'customer_name'            => null,
        'email'                    => null,
        'line_display_name'        => null,
        'via_line'                 => null,
        'phone_number1'            => null,
        'phone_number2'            => null,
        'postal_code'              => null,
        'address'                  => null,
        'vehicle_license_region'   => null,
        'vehicle_license_class'    => null,
        'vehicle_license_kana'     => null,
        'vehicle_license_number'   => null,
        'vehicle_type_id'          => null,
        'vehicle_model_name'       => null,
        'shaken_expiration_date'   => null,
        'notes'                    => null,
        'next_inspection_date'     => null, // 次回推奨点検日
        'send_inspection_notice'   => null, // 次回点検案内の送信要否フラグ
        'next_contact_date'        => null, // 次回顧客コンタクト予定日
        'inspection_notice_sent'   => null, // 次回点検案内の送信済みフラグ
        'created_at'               => null,
        'updated_at'               => null,
        'deleted_at'               => null,
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'desired_date',
        'shaken_expiration_date',
        'next_inspection_date',
        'next_contact_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'                       => 'integer',
        'reservation_status_id'    => 'integer',
        'work_type_id'             => 'integer',
        'shop_id'                  => 'integer',
        'desired_time_slot_id'     => '?integer',
        'via_line'                 => 'boolean',
        'vehicle_type_id'          => '?integer',
        'send_inspection_notice'   => 'boolean',
        'inspection_notice_sent'   => 'boolean',
        'reservation_start_time'   => 'string',
        'reservation_end_time'     => 'string',
    ];

    protected $dateFormat = 'datetime';

    // --- 関連エンティティを取得するゲッターメソッド ---
    // (キャッシュ機能などを追加するとより効率的になります)
    protected $_reserveStatus;
    public function getReservationStatus(): ?ReserveStatusEntity
    {
        if ($this->_reserveStatus === null && !empty($this->attributes['reservation_status_id'])) {
            $model = model(ReserveStatusModel::class);
            $this->_reserveStatus = $model->find($this->attributes['reservation_status_id']);
        }
        return $this->_reserveStatus;
    }

    protected $_workType;
    public function getWorkType(): ?WorkTypeEntity
    {
        if ($this->_workType === null && !empty($this->attributes['work_type_id'])) {
            $model = model(WorkTypeModel::class);
            $this->_workType = $model->find($this->attributes['work_type_id']);
        }
        return $this->_workType;
    }

    protected $_shop;
    public function getShop(): ?ShopEntity
    {
        if ($this->_shop === null && !empty($this->attributes['shop_id'])) {
            $model = model(ShopModel::class);
            $this->_shop = $model->find($this->attributes['shop_id']);
        }
        return $this->_shop;
    }

    protected $_desiredTimeSlot;
    public function getDesiredTimeSlot(): ?TimeSlotEntity
    {
        if ($this->_desiredTimeSlot === null && !empty($this->attributes['desired_time_slot_id'])) {
            $model = model(TimeSlotModel::class);
            $this->_desiredTimeSlot = $model->find($this->attributes['desired_time_slot_id']);
        }
        return $this->_desiredTimeSlot;
    }

    protected $_vehicleType;
    public function getVehicleType(): ?VehicleTypeEntity
    {
        if ($this->_vehicleType === null && !empty($this->attributes['vehicle_type_id'])) {
            $model = model(VehicleTypeModel::class);
            $this->_vehicleType = $model->find($this->attributes['vehicle_type_id']);
        }
        return $this->_vehicleType;
    }

    // TIME型カラムをTimeオブジェクトとして扱うゲッター
    public function getReservationStartTimeAsObject(): ?Time
    {
        return $this->attributes['reservation_start_time'] ? Time::parse($this->attributes['reservation_start_time'], config('App')->appTimezone) : null;
    }

    public function getReservationEndTimeAsObject(): ?Time
    {
        return $this->attributes['reservation_end_time'] ? Time::parse($this->attributes['reservation_end_time'], config('App')->appTimezone) : null;
    }
    
    public function getFullLicensePlate(): string
    {
        return trim(
            ($this->attributes['vehicle_license_region'] ?? '') . ' ' .
            ($this->attributes['vehicle_license_class'] ?? '') . ' ' .
            ($this->attributes['vehicle_license_kana'] ?? '') . ' ' .
            ($this->attributes['vehicle_license_number'] ?? '')
        );
    }
}