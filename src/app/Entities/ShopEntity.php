<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
// use App\Models\TimeSlotModel; // TimeSlotModelへのリレーションを定義する場合に備えて

class ShopEntity extends Entity
{
    protected $attributes = [
        'id'                => null,
        'name'              => null,
        'is_clear_ready'    => null, // boolean (true/false)
        'active'            => null, // boolean (true/false)
        'sort_order'        => null,
    ];

    protected $casts = [
        'id'                => 'integer', // TINYINT UNSIGNED は integer として扱えます
        'is_clear_ready'    => 'boolean', // TINYINT(1) を boolean にキャスト
        'active'            => 'boolean', // TINYINT(1) を boolean にキャスト
        'sort_order'        => 'integer',
    ];

    protected $dateFormat = 'datetime'; // このエンティティにはタイムスタンプはないが、ベースクラス用に設定

    // --- ヘルパーメソッド ---

    /**
     * この店舗が有効かどうかを返します。
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->attributes['active'];
    }

    /**
     * この店舗がClear車検に対応しているかどうかを返します。
     * @return bool
     */
    public function isClearReady(): bool
    {
        return (bool) $this->attributes['is_clear_ready'];
    }

    /**
     * この店舗に紐づく予約時間帯を取得するメソッドの例です。
     * TimeSlotModel が作成されている必要があります。
     *
     * @param bool $activeOnly 有効な時間帯のみを取得するかどうか (デフォルト: true)
     * @return array<\App\Entities\TimeSlotEntity>
     */
    // public function getTimeSlots(bool $activeOnly = true): array
    // {
    //     if (empty($this->attributes['id'])) {
    //         return [];
    //     }
    //     $timeSlotModel = model('App\Models\TimeSlotModel'); // TimeSlotModel のフルパスを指定
    //     $builder = $timeSlotModel->where('shop_id', $this->attributes['id']);
    //
    //     if ($activeOnly) {
    //         $builder->where('active', 1);
    //     }
    //
    //     return $builder->orderBy('sort_order', 'ASC')->findAll();
    // }
}