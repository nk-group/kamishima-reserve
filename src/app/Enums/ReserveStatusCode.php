<?php

namespace App\Enums;

/**
 * 予約状況コードを表すEnum。
 *
 * @method string label() このステータスの日本語名ラベルを取得します。
 */
enum ReserveStatusCode: string
{
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELED  = 'canceled';

    /**
     * 各Enumケースに対応する日本語のラベル（表示名）を返します。
     * データベースの `reserve_statuses.name` カラムが正式な表示名ですが、
     * Enum自体にもラベルを持たせたい場合に便利です。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING   => '未確定',
            self::CONFIRMED => '予約確定',
            self::COMPLETED => '作業完了',
            self::CANCELED  => 'キャンセル',
        };
    }

    /**
     * データベースの `reserve_statuses.id` に対応する値を返します。
     * （初期データに基づきます）
     *
     * @return int
     */
    public function id(): int
    {
        return match ($this) {
            self::PENDING   => 1,
            self::CONFIRMED => 2,
            self::COMPLETED => 3,
            self::CANCELED  => 9,
        };
    }
}