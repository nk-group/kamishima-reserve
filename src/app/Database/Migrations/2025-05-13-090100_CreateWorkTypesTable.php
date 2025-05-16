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
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code', false, true);
        $this->forge->addKey('sort_order');
        $this->forge->createTable('work_types');
    }

    public function down()
    {
        $this->forge->dropTable('work_types');
    }
}