<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ReserveStatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'         => 1,
                'code'       => 'pending',
                'name'       => '未確定',
                'sort_order' => 10,
            ],
            [
                'id'         => 2,
                'code'       => 'confirmed',
                'name'       => '予約確定',
                'sort_order' => 20,
            ],
            [
                'id'         => 3,
                'code'       => 'completed',
                'name'       => '作業完了',
                'sort_order' => 30,
            ],
            [
                'id'         => 9,
                'code'       => 'canceled',
                'name'       => 'キャンセル',
                'sort_order' => 90,
            ],
        ];

        // Using Query Builder
        $this->db->table('reserve_statuses')->insertBatch($data);
    }
}