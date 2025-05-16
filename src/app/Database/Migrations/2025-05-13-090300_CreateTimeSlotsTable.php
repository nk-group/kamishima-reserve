<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimeSlotsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'TINYINT',
                'constraint'     => 3,
                'unsigned'       => true,
            ],
            'shop_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => false,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => false,
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
        $this->forge->addKey('shop_id'); // FK用インデックス
        $this->forge->addKey('sort_order');
        $this->forge->addForeignKey('shop_id', 'shops', 'id', 'CASCADE', 'RESTRICT'); // FK制約
        // 注意: shopsテーブルが先に作成されている必要があります。
        // ON UPDATEのデフォルトはRESTRICT、ON DELETEのデフォルトはRESTRICT。
        // ここではON DELETEはCASCADE（店舗が削除されたら関連する時間帯も削除）、ON UPDATEはRESTRICTとしています。
        // もし店舗削除時に関連時間帯を削除したくない場合は 'NO ACTION' や 'SET NULL' (shop_idがNULL許容の場合) を検討。
        // 今回はCASCADEが無難かもしれません。
        $this->forge->createTable('time_slots');
    }

    public function down()
    {
        $this->forge->dropTable('time_slots');
    }
}