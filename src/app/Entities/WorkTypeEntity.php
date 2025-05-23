<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Enums\WorkTypeCode; // 作成したEnumをインポート

class WorkTypeEntity extends Entity
{
    protected $attributes = [
        'id'                => null,
        'code'              => null, // WorkTypeCode Enum インスタンスまたはその backed value (string)
        'name'              => null,
        'active'            => null, // boolean (true/false)
        'is_clear_shaken'   => null, // boolean (true/false)
        'sort_order'        => null,
    ];

    protected $casts = [
        'id'                => 'integer',
        'active'            => 'boolean', // TINYINT(1) を boolean にキャスト
        'is_clear_shaken'   => 'boolean', // TINYINT(1) を boolean にキャスト
        'sort_order'        => 'integer',
        'code'              => WorkTypeCode::class, // codeプロパティをWorkTypeCode Enumにキャスト
    ];

    protected $dateFormat = 'datetime'; // このエンティティにはタイムスタンプはないが、ベースクラス用に設定

    // --- ヘルパーメソッド ---

    /**
     * この作業種別が有効かどうかを返します。
     * @return bool
     */
    public function isActive(): bool
    {
        // $casts により $this->attributes['active'] は既に boolean になっています。
        return (bool) $this->attributes['active'];
    }

    /**
     * この作業種別がClear車検サービスであるか（顧客向けClear車検か、調整枠としてのClear車検か）を返します。
     * @return bool
     */
    public function isClearShakenServiceType(): bool
    {
        // $casts により $this->attributes['is_clear_shaken'] は既に boolean になっています。
        return (bool) $this->attributes['is_clear_shaken'];
    }

    /**
     * codeプロパティのゲッター (型ヒントのため明示的に定義する例)
     * @return \App\Enums\WorkTypeCode|null
     */
    public function getCode(): ?WorkTypeCode
    {
        return $this->attributes['code'];
    }

    // name, sort_order 等のゲッター/セッターは必要に応じて追加
}