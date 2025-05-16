<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFullNameToUsers extends Migration
{
    public function up()
    {
        // usersテーブルに full_name カラムを追加します。
        // Shieldのusersテーブルには既に id, username, active, last_active, created_at, updated_at, deleted_at があります。
        // Shieldのauth_identitiesテーブルには user_id, type, name, secret, secret2, expires, extra, force_reset があります。
        // 一般的に氏名は users テーブルに持たせることが多いです。
        $fields = [
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, // ご指定の varchar(20)
                'null'       => true, // 必須ではない場合は true、必須なら false (または default 値を指定)
                'after'      => 'username', // username カラムの後に追加 (任意)
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // ロールバック時に full_name カラムを削除します。
        $this->forge->dropColumn('users', 'full_name');
    }
}