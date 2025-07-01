<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * ユーザー個人設定テーブル作成マイグレーション
 * 
 * 個人設定機能実装のためのuser_preferencesテーブルを作成します。
 * ユーザー毎の設定情報をキー・バリュー形式で管理します。
 */
class CreateUserPreferencesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ユーザーID（usersテーブルへの外部キー）',
            ],
            'preference_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => '設定キー（例: default_shop_id, pagination_per_page）',
            ],
            'preference_value' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => '設定値（文字列、数値、JSON等）',
            ],
            'preference_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'general',
                'comment'    => '設定グループ（general, display, function等）',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => '作成日時',
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                'comment' => '更新日時',
            ],
        ]);

        // 主キー設定
        $this->forge->addKey('id', true);

        // 外部キー制約
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        // ユニークキー: 同一ユーザーで同一キーは重複不可
        $this->forge->addKey(['user_id', 'preference_key'], false, true);

        // インデックス
        $this->forge->addKey('user_id'); // user_idでの検索用
        $this->forge->addKey(['user_id', 'preference_key']); // 個人設定検索の最適化

        // テーブル作成
        $this->forge->createTable('user_preferences', true);

        // テーブルコメント追加
        $this->db->query("ALTER TABLE user_preferences COMMENT = 'ユーザー個人設定テーブル'");
    }

    public function down()
    {
        // 外部キー制約を先に削除
        $this->forge->dropForeignKey('user_preferences', 'user_preferences_user_id_foreign');
        
        // テーブル削除
        $this->forge->dropTable('user_preferences');
    }
}