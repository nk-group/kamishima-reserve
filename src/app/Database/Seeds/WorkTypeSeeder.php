<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WorkTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'                => 1,
                'code'              => 'clear_shaken',
                'name'              => 'Clear車検',
                'active'            => 1,
                'is_clear_shaken'   => 1,
                'sort_order'        => 10,
            ],
            [
                'id'                => 2,
                'code'              => 'general_shaken',
                'name'              => '一般車検',
                'active'            => 1,
                'is_clear_shaken'   => 0,
                'sort_order'        => 20,
            ],
            [
                'id'                => 3,
                'code'              => 'periodic_inspection',
                'name'              => '定期点検',
                'active'            => 1,
                'is_clear_shaken'   => 0,
                'sort_order'        => 30,
            ],
            [
                'id'                => 4,
                'code'              => 'general_maintenance',
                'name'              => '一般整備',
                'active'            => 1,
                'is_clear_shaken'   => 0,
                'sort_order'        => 40,
            ],
            [
                'id'                => 8,
                'code'              => 'adjustment_clear',
                'name'              => '調整枠 (Clear車検)',
                'active'            => 1,
                'is_clear_shaken'   => 1,
                'sort_order'        => 80,
            ],
            [
                'id'                => 9,
                'code'              => 'other',
                'name'              => 'その他',
                'active'            => 1,
                'is_clear_shaken'   => 0,
                'sort_order'        => 90,
            ],
        ];

        $this->db->table('work_types')->insertBatch($data);
    }
}