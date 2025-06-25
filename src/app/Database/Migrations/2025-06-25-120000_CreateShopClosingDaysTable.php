<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShopClosingDaysTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'shop_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
            ],
            'holiday_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'closing_date' => [
                'type' => 'DATE',
            ],
            'repeat_type' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'comment'    => '0:繰り返しなし, 1:毎週, 2:毎年',
            ],
            'repeat_end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 1,
                'comment'    => '1:有効, 0:無効',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // 主キー設定
        $this->forge->addKey('id', true);

        // インデックス設定
        $this->forge->addKey('shop_id');
        $this->forge->addKey('closing_date');
        $this->forge->addKey(['shop_id', 'closing_date']); // 複合インデックス

        // 外部キー制約
        $this->forge->addForeignKey('shop_id', 'shops', 'id', 'CASCADE', 'RESTRICT');

        // テーブル作成
        $this->forge->createTable('shop_closing_days');
    }

    public function down()
    {
        $this->forge->dropTable('shop_closing_days');
    }
}