<?php

namespace App\Enums;

/**
 * 作業種別コードを表すEnum。
 */
enum WorkTypeCode: string
{
    case CLEAR_SHAKEN        = 'clear_shaken';
    case GENERAL_SHAKEN      = 'general_shaken';
    case PERIODIC_INSPECTION = 'periodic_inspection';
    case GENERAL_MAINTENANCE = 'general_maintenance';
    case ADJUSTMENT_CLEAR    = 'adjustment_clear'; // ユーザー設定: 調整枠 (Clear車検)
    case OTHER               = 'other';

    /**
     * 各Enumケースに対応する日本語のラベル（表示名）を返します。
     * これは work_types.name カラムのデータと一致することを想定しています。
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::CLEAR_SHAKEN        => 'Clear車検',
            self::GENERAL_SHAKEN      => '一般車検',
            self::PERIODIC_INSPECTION => '定期点検',
            self::GENERAL_MAINTENANCE => '一般整備',
            self::ADJUSTMENT_CLEAR    => '調整枠 (Clear車検)',
            self::OTHER               => 'その他',
        };
    }

    /**
     * データベースの work_types.id に対応する値を返します。
     * （初期データに基づきます）
     *
     * @return int
     */
    public function id(): int
    {
        return match ($this) {
            self::CLEAR_SHAKEN        => 1,
            self::GENERAL_SHAKEN      => 2,
            self::PERIODIC_INSPECTION => 3,
            self::GENERAL_MAINTENANCE => 4,
            self::ADJUSTMENT_CLEAR    => 8,
            self::OTHER               => 9,
        };
    }
}