<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1,  'shop_id' => 5, 'name' => '08:45～', 'start_time' => '08:45:00', 'end_time' => '09:29:00', 'active' => 1, 'sort_order' => 10],
            ['id' => 2,  'shop_id' => 5, 'name' => '09:30～', 'start_time' => '09:30:00', 'end_time' => '10:14:00', 'active' => 1, 'sort_order' => 20],
            ['id' => 3,  'shop_id' => 5, 'name' => '10:15～', 'start_time' => '10:15:00', 'end_time' => '10:59:00', 'active' => 1, 'sort_order' => 30],
            ['id' => 4,  'shop_id' => 5, 'name' => '11:00～', 'start_time' => '11:00:00', 'end_time' => '11:44:00', 'active' => 1, 'sort_order' => 40],
            ['id' => 5,  'shop_id' => 5, 'name' => '13:00～', 'start_time' => '13:00:00', 'end_time' => '13:44:00', 'active' => 1, 'sort_order' => 50],
            ['id' => 6,  'shop_id' => 5, 'name' => '13:45～', 'start_time' => '13:45:00', 'end_time' => '14:29:00', 'active' => 1, 'sort_order' => 60],
            ['id' => 7,  'shop_id' => 5, 'name' => '14:30～', 'start_time' => '14:30:00', 'end_time' => '15:14:00', 'active' => 1, 'sort_order' => 70],
            ['id' => 8,  'shop_id' => 5, 'name' => '15:15～', 'start_time' => '15:15:00', 'end_time' => '15:59:00', 'active' => 1, 'sort_order' => 80],
            ['id' => 9,  'shop_id' => 5, 'name' => '16:00～', 'start_time' => '16:00:00', 'end_time' => '16:44:00', 'active' => 1, 'sort_order' => 90],
            ['id' => 10, 'shop_id' => 5, 'name' => '16:45～', 'start_time' => '16:45:00', 'end_time' => '17:29:00', 'active' => 1, 'sort_order' => 100],
        ];

        $this->db->table('time_slots')->insertBatch($data);
    }
}