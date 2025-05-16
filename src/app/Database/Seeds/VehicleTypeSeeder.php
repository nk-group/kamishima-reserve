<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time; // Timeクラスをインポート

class VehicleTypeSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now()->toDateTimeString(); // 現在日時を取得

        $data = [
            [
                'id'         => 1, // AUTO_INCREMENT ですが、初期データでIDを指定
                'code'       => '9999',
                'name'       => 'その他',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 2,
                'code'       => '0001',
                'name'       => '乗用車',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 3,
                'code'       => '0002',
                'name'       => '商用車',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 4,
                'code'       => '0003',
                'name'       => '貨物車',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 5,
                'code'       => '0004',
                'name'       => '特殊用途車',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // AUTO_INCREMENT カラムに値を指定してINSERTする場合、
        // DBによっては挙動が異なることがありますが、MySQLでは通常問題ありません。
        // もしIDをDBに自動採番させたい場合は、データから 'id' のキーを削除してください。
        $this->db->table('vehicle_types')->insertBatch($data);
    }
}