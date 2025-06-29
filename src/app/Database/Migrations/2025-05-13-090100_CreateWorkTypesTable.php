<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'TINYINT',
                'constraint'     => 3,
                'unsigned'       => true,
                'auto_increment' => false, // シーダーでIDを直接指定するため
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
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
            'is_clear_shaken' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'tag_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => false,
                'default'    => '#ffffff',
            ],
            'count_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'other',
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
        //$this->forge->addKey('code'); // INDEX
        $this->forge->addKey('count_category'); // INDEX（集計クエリの高速化）
        
        $this->forge->createTable('work_types');
    }

    public function down()
    {
        $this->forge->dropTable('work_types');
    }
}