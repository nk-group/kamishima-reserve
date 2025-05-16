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
        'code'       => ReserveStatusCode::class, // codeプロパティをReserveStatusCode Enumにキャスト
    ];

    protected $dateFormat = 'datetime';


    // ヘルパーメソッド (Enumを使って判定)
    public function isPending(): bool
    {
        // $this->code はキャストにより ReserveStatusCode オブジェクトになっています
        return $this->attributes['code'] === ReserveStatusCode::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->attributes['code'] === ReserveStatusCode::CONFIRMED;
    }

    public function isCompleted(): bool
    {
        return $this->attributes['code'] === ReserveStatusCode::COMPLETED;
    }

    public function isCanceled(): bool
    {
        return $this->attributes['code'] === ReserveStatusCode::CANCELED;
    }

    /**
     * codeプロパティのゲッター (型ヒントのため明示的に定義する例)
     * 通常、Entityクラスが自動的に処理しますが、より厳密な型を意識する場合に。
     *
     * @return \App\Enums\ReserveStatusCode|null
     */
    public function getCode(): ?ReserveStatusCode
    {
        // $this->attributes['code'] は $casts により既にEnumオブジェクトになっているはずです。
        // もし文字列の場合に備えるなら、ここで ReserveStatusCode::tryFrom($this->attributes['code']) のような処理も可能です。
        return $this->attributes['code'];
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