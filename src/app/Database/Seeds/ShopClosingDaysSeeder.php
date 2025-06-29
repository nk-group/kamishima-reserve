<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShopClosingDaysSeeder extends Seeder
{
    public function run()
    {
        // 定休日データを削除（リセット）
        $this->db->table('shop_closing_days')->truncate();

        // 基準日を設定（2025年の最初の火曜日と水曜日）
        $firstTuesday = '2025-01-07';    // 2025年最初の火曜日
        $firstWednesday = '2025-01-08';  // 2025年最初の水曜日

        $data = [
            // Clear車検（店舗ID: 5）- 毎週火曜日が定休日
            [
                'shop_id'         => 5,
                'holiday_name'    => '定休日（毎週火曜日）',
                'closing_date'    => $firstTuesday,
                'repeat_type'     => 1, // 毎週
                'repeat_end_date' => null,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            
            // 本社工場（店舗ID: 1）- 毎週火曜日が定休日
            [
                'shop_id'         => 1,
                'holiday_name'    => '定休日（毎週火曜日）',
                'closing_date'    => $firstTuesday,
                'repeat_type'     => 1, // 毎週
                'repeat_end_date' => null,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            
            // モーターショップカミシマ（店舗ID: 2）- 毎週水曜日が定休日
            [
                'shop_id'         => 2,
                'holiday_name'    => '定休日（毎週水曜日）',
                'closing_date'    => $firstWednesday,
                'repeat_type'     => 1, // 毎週
                'repeat_end_date' => null,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        // 一括挿入
        $this->db->table('shop_closing_days')->insertBatch($data);

        //echo "定休日マスタのシードデータを登録しました。\n";
        //echo "- Clear車検: 毎週火曜日\n";
        //echo "- 本社工場: 毎週火曜日\n";
        //echo "- モーターショップカミシマ: 毎週水曜日\n";
    }
}