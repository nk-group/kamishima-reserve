<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time; // Timeオブジェクトの利用のため

class TimeSlotEntity extends Entity
{
    protected $attributes = [
        'id'         => null,
        'shop_id'    => null,
        'name'       => null,
        'start_time' => null, // DBからは 'HH:MM:SS' 形式の文字列
        'end_time'   => null, // DBからは 'HH:MM:SS' 形式の文字列
        'active'     => null, // boolean (true/false)
        'sort_order' => null,
    ];

    protected $casts = [
        'id'         => 'integer', // TINYINT UNSIGNED は integer として扱えます
        'shop_id'    => 'integer', // TINYINT UNSIGNED は integer として扱えます
        'active'     => 'boolean', // TINYINT(1) を boolean にキャスト
        'sort_order' => 'integer',
        // TIME型は CodeIgniter\Model の $dateFormat の影響は受けず、
        // 通常は文字列として扱われます。
        // Timeオブジェクトとして扱いたい場合は、ゲッターメソッドで変換します。
    ];

    protected $dateFormat = 'datetime'; // このエンティティにはタイムスタンプはないが、ベースクラス用に設定

    // --- ヘルパーメソッド ---

    /**
     * この予約時間帯が有効かどうかを返します。
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->attributes['active'];
    }

    /**
     * 開始時刻を CodeIgniter\I18n\Time オブジェクトとして取得します。
     * @return \CodeIgniter\I18n\Time|null
     */
    public function getStartTimeAsObject(): ?Time
    {
        if (empty($this->attributes['start_time'])) {
            return null;
        }
        // タイムゾーンはプロジェクトの設定や要件に合わせてください
        // 通常は 'app/Config/App.php' の $appTimezone を参照します。
        return Time::parse($this->attributes['start_time'], config('App')->appTimezone);
    }

    /**
     * 終了時刻を CodeIgniter\I18n\Time オブジェクトとして取得します。
     * @return \CodeIgniter\I18n\Time|null
     */
    public function getEndTimeAsObject(): ?Time
    {
        if (empty($this->attributes['end_time'])) {
            return null;
        }
        return Time::parse($this->attributes['end_time'], config('App')->appTimezone);
    }

    /**
     * 開始時刻を指定されたフォーマットの文字列で取得します。
     * @param string $format (例: 'H:i', 'H時i分')
     * @return string|null
     */
    public function getFormattedStartTime(string $format = 'H:i'): ?string
    {
        $timeObject = $this->getStartTimeAsObject();
        return $timeObject ? $timeObject->format($format) : null;
    }

    /**
     * 終了時刻を指定されたフォーマットの文字列で取得します。
     * @param string $format (例: 'H:i', 'H時i分')
     * @return string|null
     */
    public function getFormattedEndTime(string $format = 'H:i'): ?string
    {
        $timeObject = $this->getEndTimeAsObject();
        return $timeObject ? $timeObject->format($format) : null;
    }

    /**
     * この予約時間帯が属する店舗エンティティを取得します。
     * ShopModel が必要です。
     *
     * @return \App\Entities\ShopEntity|null
     */
    public function getShop(): ?ShopEntity
    {
        if (empty($this->attributes['shop_id'])) {
            return null;
        }
        // ShopModelのインスタンスを取得 (model() ヘルパー関数が便利)
        $shopModel = model('App\Models\ShopModel');
        return $shopModel->find($this->attributes['shop_id']);
    }
}