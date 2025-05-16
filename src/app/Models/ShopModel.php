<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ShopEntity; // 作成したエンティティクラスをインポート

class ShopModel extends Model
{
    protected $table            = 'shops';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // シーダーでIDを指定するため

    protected $returnType       = ShopEntity::class; // エンティティクラスを指定
    protected $useSoftDeletes   = false; // ソフトデリートは使用しない

    protected $allowedFields    = [
        'id',
        'name',
        'is_clear_ready',
        'active',
        'sort_order'
    ];

    protected $useTimestamps    = false; // タイムスタンプは使用しない

    // バリデーションルール (このテーブルはメンテナンス画面がないため、定義は任意)
    // protected $validationRules = [
    //     'name'             => 'required|string|max_length[50]',
    //     'is_clear_ready'   => 'required|in_list[0,1]',
    //     'active'           => 'required|in_list[0,1]',
    //     'sort_order'       => 'required|integer',
    // ];
    // protected $validationMessages = [];
    // protected $skipValidation = false;

    // --- カスタムメソッドの例 ---

    /**
     * 有効な店舗を取得します。
     * オプションでClear車検対応状況によって絞り込みも可能です。
     *
     * @param bool|null $isClearReadyOptionally nullの場合は絞り込まない、trueの場合はClear車検対応店舗のみ、falseの場合はClear車検非対応店舗のみ
     * @return array<ShopEntity>
     */
    public function findActiveShops(bool $isClearReadyOptionally = null): array
    {
        $builder = $this->where('active', 1);

        if ($isClearReadyOptionally !== null) {
            $builder->where('is_clear_ready', $isClearReadyOptionally ? 1 : 0);
        }

        return $builder->orderBy('sort_order', 'ASC')->findAll();
    }

    /**
     * 指定された店舗IDに紐づく予約時間帯を取得します。
     * TimeSlotModel が必要です。
     *
     * @param int  $shopId 店舗ID
     * @param bool $activeOnly 有効なもののみ取得するか (デフォルト: true)
     * @return array<\App\Entities\TimeSlotEntity> An array of TimeSlotEntity objects or an empty array.
     */
    public function getTimeSlotsForShop(int $shopId, bool $activeOnly = true): array
    {
        $timeSlotModel = model('App\Models\TimeSlotModel'); // TimeSlotModel のフルパスを指定
        // TimeSlotModel がまだ作成されていない場合は、この行でエラーになります。
        // TimeSlotModel 作成後にこのメソッドは機能します。

        $builder = $timeSlotModel->where('shop_id', $shopId);

        if ($activeOnly) {
            $builder->where('active', 1);
        }

        return $builder->orderBy('sort_order', 'ASC')->findAll();
    }
}