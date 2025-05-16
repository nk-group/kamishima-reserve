<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShopsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'TINYINT',
                'constraint'     => 3,
                'unsigned'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'is_clear_ready' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 1,
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
        $this->forge->addKey('sort_order');
        $this->forge->createTable('shops');
    }

    public function down()
    {
        $this->forge->dropTable('shops');
    }
}