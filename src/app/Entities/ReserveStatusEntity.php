<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Enums\ReserveStatusCode; // 作成したEnumをインポート

class ReserveStatusEntity extends Entity
{
    protected $attributes = [
        'id'         => null,
        'code'       => null, // ここには ReserveStatusCode Enum インスタンス、またはその backed value (string) が入ります
        'name'       => null,
        'sort_order' => null,
    ];

    protected $casts = [
        'id'         => 'integer',
        'sort_order' => 'integer',
        // 'code'       => ReserveStatusCode::class, // ★この行を削除（Enumキャストが原因）
    ];

    protected $dateFormat = 'datetime';

    /**
     * codeプロパティをReserveStatusCode Enumとして取得します。
     * @return \App\Enums\ReserveStatusCode|null
     */
    public function getCodeEnum(): ?ReserveStatusCode
    {
        if (empty($this->attributes['code'])) {
            return null;
        }
        
        return ReserveStatusCode::tryFrom($this->attributes['code']);
    }

    /**
     * codeプロパティを文字列として取得します。
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->attributes['code'];
    }

    // ヘルパーメソッド (Enumを使って判定)
    public function isPending(): bool
    {
        return $this->getCodeEnum() === ReserveStatusCode::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->getCodeEnum() === ReserveStatusCode::CONFIRMED;
    }

    public function isCompleted(): bool
    {
        return $this->getCodeEnum() === ReserveStatusCode::COMPLETED;
    }

    public function isCanceled(): bool
    {
        return $this->getCodeEnum() === ReserveStatusCode::CANCELED;
    }

    // 必要に応じて他のゲッターやセッターを追加
    // public function getId(): ?int
    // {
    //     return $this->attributes['id'];
    // }

    // public function getName(): ?string
    // {
    //     return $this->attributes['name'];
    // }

    // public function getSortOrder(): ?int
    // {
    //     return $this->attributes['sort_order'];
    // }
}