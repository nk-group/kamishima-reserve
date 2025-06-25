<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WorkTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'              => 1,
                'code'            => 'clear_shaken',
                'name'            => 'Clear車検',
                'active'          => 1,
                'is_clear_shaken' => 1,
                'tag_color'       => '#ff99cc',
                'count_category'  => 'clear_shaken',
                'sort_order'      => 10,
            ],
            [
                'id'              => 2,
                'code'            => 'periodic_inspection',
                'name'            => '定期点検',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#99ccff',
                'count_category'  => 'other',
                'sort_order'      => 20,
            ],
            [
                'id'              => 3,
                'code'            => 'general_shaken',
                'name'            => '一般車検',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#ff99cc',
                'count_category'  => 'general_shaken',
                'sort_order'      => 30,
            ],
            [
                'id'              => 4,
                'code'            => 'general_maintenance',
                'name'            => '一般整備',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#ffffff',
                'count_category'  => 'other',
                'sort_order'      => 40,
            ],
            [
                'id'              => 5,
                'code'            => 'adjustment_clear',
                'name'            => '調整枠 (Clear車検)',
                'active'          => 1,
                'is_clear_shaken' => 1,
                'tag_color'       => '#cccccc',
                'count_category'  => 'excluded',
                'sort_order'      => 50,
            ],
            [
                'id'              => 6,
                'code'            => 'lease_schedule',
                'name'            => 'リーススケジュール点検',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#99cc99',
                'count_category'  => 'other',
                'sort_order'      => 60,
            ],
            [
                'id'              => 7,
                'code'            => 'lease_statutory',
                'name'            => 'リース法定点検',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#99cc99',
                'count_category'  => 'other',
                'sort_order'      => 70,
            ],
            [
                'id'              => 8,
                'code'            => 'lease_shaken',
                'name'            => 'リース車検',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#99cc99',
                'count_category'  => 'general_shaken',
                'sort_order'      => 80,
            ],
            [
                'id'              => 9,
                'code'            => 'lease_maintenance',
                'name'            => 'リース整備',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#99cc99',
                'count_category'  => 'other',
                'sort_order'      => 90,
            ],
            [
                'id'              => 10,
                'code'            => 'bodywork',
                'name'            => '板金',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#ffcc66',
                'count_category'  => 'other',
                'sort_order'      => 100,
            ],
            [
                'id'              => 99,
                'code'            => 'other',
                'name'            => 'その他',
                'active'          => 1,
                'is_clear_shaken' => 0,
                'tag_color'       => '#ffffff',
                'count_category'  => 'other',
                'sort_order'      => 110,
            ],
        ];

        // 既存データがある場合は削除してから挿入
        $this->db->table('work_types')->truncate();
        
        // データを挿入
        $this->db->table('work_types')->insertBatch($data);
    }
}