<?php

namespace App\Enums;

/**
 * 作業種別コードを表すEnum。
 */
enum WorkTypeCode: string
{
    case CLEAR_SHAKEN        = 'clear_shaken';        // ID: 1
    case PERIODIC_INSPECTION = 'periodic_inspection'; // ID: 2
    case GENERAL_SHAKEN      = 'general_shaken';      // ID: 3
    case GENERAL_MAINTENANCE = 'general_maintenance'; // ID: 4
    case ADJUSTMENT_CLEAR    = 'adjustment_clear';    // ID: 5
    case LEASE_SCHEDULE      = 'lease_schedule';      // ID: 6
    case LEASE_STATUTORY     = 'lease_statutory';     // ID: 7
    case LEASE_SHAKEN        = 'lease_shaken';        // ID: 8
    case LEASE_MAINTENANCE   = 'lease_maintenance';   // ID: 9
    case BODYWORK            = 'bodywork';            // ID: 10
    case OTHER               = 'other';               // ID: 99

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
            self::PERIODIC_INSPECTION => '定期点検',
            self::GENERAL_SHAKEN      => '一般車検',
            self::GENERAL_MAINTENANCE => '一般整備',
            self::ADJUSTMENT_CLEAR    => '調整枠 (Clear車検)',
            self::LEASE_SCHEDULE      => 'リーススケジュール点検',
            self::LEASE_STATUTORY     => 'リース法定点検',
            self::LEASE_SHAKEN        => 'リース車検',
            self::LEASE_MAINTENANCE   => 'リース整備',
            self::BODYWORK            => '板金',
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
            self::PERIODIC_INSPECTION => 2,
            self::GENERAL_SHAKEN      => 3,
            self::GENERAL_MAINTENANCE => 4,
            self::ADJUSTMENT_CLEAR    => 5,
            self::LEASE_SCHEDULE      => 6,
            self::LEASE_STATUTORY     => 7,
            self::LEASE_SHAKEN        => 8,
            self::LEASE_MAINTENANCE   => 9,
            self::BODYWORK            => 10,
            self::OTHER               => 99,
        };
    }
}