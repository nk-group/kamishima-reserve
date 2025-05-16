<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReserveStatusesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'TINYINT',
                'constraint'     => 3,
                'unsigned'       => true,
                // PKなのでAUTO_INCREMENTは不要 (初期データでID指定するため)
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true); // 主キー
        $this->forge->addKey('code', false, true); // UNIQUEキー
        $this->forge->addKey('sort_order'); // INDEX (任意)
        $this->forge->createTable('reserve_statuses');
    }

    public function down()
    {
        $this->forge->dropTable('reserve_statuses');
    }
}