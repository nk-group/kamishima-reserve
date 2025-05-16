<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time; // Timeオブジェクトを型ヒントで使う場合に備えて

class VehicleTypeEntity extends Entity
{
    protected $attributes = [
        'id'         => null,
        'code'       => null,
        'name'       => null,
        'active'     => null, // boolean (true/false)
        'created_at' => null, // Timeオブジェクトまたはnull
        'updated_at' => null, // Timeオブジェクトまたはnull
        'deleted_at' => null, // Timeオブジェクトまたはnull
    ];

    /**
     * $castsプロパティは、特定の属性がエンティティから取得される際に
     * どのようにキャストされるべきかを定義します。
     */
    protected $casts = [
        'id'         => 'integer',
        'active'     => 'boolean', // TINYINT(1) を boolean にキャスト
        // created_at, updated_at, deleted_at は $dates プロパティでTimeオブジェクトとして扱います
    ];

    /**
     * $dates プロパティは、指定されたカラムを自動的に CodeIgniter\I18n\Time オブジェクトとして
     * 扱うようにします。これにより、日付や時刻の操作が容易になります。
     * @var string[]
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    // --- ヘルパーメソッド ---

    /**
     * この車両種別が有効かどうかを返します。
     * @return bool
     */
    public function isActive(): bool
    {
        // $casts により $this->attributes['active'] は既に boolean になっています。
        return (bool) $this->attributes['active'];
    }

    /**
     * 作成日時を特定のフォーマットで取得する例
     * @param string $format
     * @return string|null
     */
    public function getFormattedCreatedAt(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->created_at instanceof Time ? $this->created_at->format($format) : null;
    }
}