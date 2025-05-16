<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id'             => 1,
                'name'           => '本店',
                'is_clear_ready' => 0,
                'active'         => 1,
                'sort_order'     => 10,
            ],
            [
                'id'             => 2,
                'name'           => 'モーターショップカミシマ',
                'is_clear_ready' => 0,
                'active'         => 1,
                'sort_order'     => 20,
            ],
            [
                'id'             => 5,
                'name'           => 'Clear車検 (本店)',
                'is_clear_ready' => 1,
                'active'         => 1,
                'sort_order'     => 50,
            ],
        ];

        $this->db->table('shops')->insertBatch($data);
    }
}