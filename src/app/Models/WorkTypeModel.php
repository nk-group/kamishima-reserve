<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\WorkTypeEntity; // 作成したエンティティクラスをインポート

class WorkTypeModel extends Model
{
    protected $table            = 'work_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // シーダーでIDを指定するため

    protected $returnType       = WorkTypeEntity::class; // エンティティクラスを指定
    protected $useSoftDeletes   = false; // ソフトデリートは使用しない

    protected $allowedFields    = [
        'id',
        'code',
        'name',
        'active',
        'is_clear_shaken',
        'sort_order'
    ];

    protected $useTimestamps    = false; // タイムスタンプは使用しない

    // バリデーションルール (このテーブルはメンテナンス画面がないため、ここでは定義は任意)
    // protected $validationRules = [
    //     'code'            => 'required|alpha_dash|is_unique[work_types.code,id,{id}]|max_length[30]',
    //     'name'            => 'required|string|max_length[30]',
    //     'active'          => 'required|in_list[0,1]',
    //     'is_clear_shaken' => 'required|in_list[0,1]',
    //     'sort_order'      => 'required|integer',
    // ];
    // protected $validationMessages = [];
    // protected $skipValidation = false;

    // --- カスタムメソッドの例 ---

    /**
     * 有効な作業種別のみを取得します。
     * @param bool $isClearShakenOptionally Clear車検の種別のみに絞り込むか (null:絞り込まない, true:Clear車検のみ, false:Clear車検以外)
     * @return array<WorkTypeEntity>
     */
    public function findActive(bool $isClearShakenOptionally = null): array
    {
        $builder = $this->where('active', 1);
        if ($isClearShakenOptionally !== null) {
            $builder->where('is_clear_shaken', $isClearShakenOptionally);
        }
        return $builder->orderBy('sort_order', 'ASC')->findAll();
    }

    /**
     * 指定されたコードから作業種別エンティティを取得します。
     * @param \App\Enums\WorkTypeCode $code
     * @return \App\Entities\WorkTypeEntity|null
     */
    public function findByCode(\App\Enums\WorkTypeCode $code): ?WorkTypeEntity
    {
        return $this->where('code', $code->value)->first();
    }
}