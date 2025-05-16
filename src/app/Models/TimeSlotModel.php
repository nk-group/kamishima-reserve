<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\TimeSlotEntity; // 作成したエンティティクラスをインポート

class TimeSlotModel extends Model
{
    protected $table            = 'time_slots';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // シーダーでIDを指定するため

    protected $returnType       = TimeSlotEntity::class; // エンティティクラスを指定
    protected $useSoftDeletes   = false; // ソフトデリートは使用しない

    protected $allowedFields    = [
        'id',
        'shop_id',
        'name',
        'start_time',
        'end_time',
        'active',
        'sort_order'
    ];

    protected $useTimestamps    = false; // タイムスタンプは使用しない

    // バリデーションルール (このテーブルはメンテナンス画面がないため、定義は任意)
    // protected $validationRules = [
    //     'shop_id'    => 'required|integer|is_not_unique[shops.id]', // 存在する店舗IDか
    //     'name'       => 'required|string|max_length[30]',
    //     'start_time' => 'required|regex_match[/^([01]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/]', // HH:MM または HH:MM:SS
    //     'end_time'   => 'required|regex_match[/^([01]\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/]', // HH:MM または HH:MM:SS
    //     'active'     => 'required|in_list[0,1]',
    //     'sort_order' => 'required|integer',
    // ];
    // protected $validationMessages = [
    //     'shop_id' => [
    //         'is_not_unique' => '指定された店舗IDが存在しません。',
    //     ],
    //     'start_time' => [
    //         'regex_match' => '開始時刻は正しい時刻形式 (HH:MM または HH:MM:SS) で入力してください。',
    //     ],
    //     'end_time' => [
    //         'regex_match' => '終了時刻は正しい時刻形式 (HH:MM または HH:MM:SS) で入力してください。',
    //     ],
    // ];
    // protected $skipValidation = false;


    // --- カスタムメソッドの例 ---

    /**
     * 指定された店舗IDに紐づく、有効な予約時間帯を全て取得します。
     * 表示順でソートされます。
     *
     * @param int $shopId 店舗ID
     * @return array<TimeSlotEntity>
     */
    public function findActiveByShopId(int $shopId): array
    {
        return $this->where('shop_id', $shopId)
                    ->where('active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}