<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVehicleTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5, // INT UNSIGNEDの一般的な制約。必要なら調整
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => false,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false, // CIのタイムスタンプはNOT NULLを期待
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false, // CIのタイムスタンプはNOT NULLを期待
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null, // 明示的にDEFAULT NULL
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code', false, true);
        $this->forge->createTable('vehicle_types');
    }

    public function down()
    {
        $this->forge->dropTable('vehicle_types');
    }
}