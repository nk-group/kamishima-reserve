<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('ReserveStatusSeeder');
        $this->call('WorkTypeSeeder');
        $this->call('ShopSeeder');
        $this->call('VehicleTypeSeeder');
        $this->call('TimeSlotSeeder');
    }
}
