<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
class CreateCiSessionsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => false],
            'timestamp' => ['type' => 'TIMESTAMP', 'default' => null, 'null' => false],
            'data' => ['type' => 'BLOB', 'null' => false],
        ]);
        if ($this->db->DBDriver === 'MySQLi') {
             $this->forge->addField("`timestamp` timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL");
        }
        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('ci_sessions', true); // テーブル名確認
    }
    public function down() { $this->forge->dropTable('ci_sessions', true); }
}