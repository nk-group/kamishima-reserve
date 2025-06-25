<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10, // INT UNSIGNED の一般的な制約
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'reservation_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'null'       => false,
            ],
            'reservation_status_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => false,
            ],
            'reservation_guid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'work_type_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => false,
            ],
            'shop_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => false,
            ],
            'desired_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'desired_time_slot_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true, // NULL許容
            ],
            'reservation_start_time' => [
                'type' => 'TIME',
                'null' => true, // NULL許容
            ],
            'reservation_end_time' => [
                'type' => 'TIME',
                'null' => true, // NULL許容
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'customer_kana' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'line_display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'via_line' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'phone_number1' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'phone_number2' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'null'       => true,
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'vehicle_license_region' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'vehicle_license_class' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'vehicle_license_kana' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'vehicle_license_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => false,
            ],
            'vehicle_type_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
            ],
            'vehicle_model_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'first_registration_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'shaken_expiration_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'model_spec_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'classification_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'loaner_usage' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'loaner_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'customer_requests' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'next_inspection_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'send_inspection_notice' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'next_work_type_id' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
            ],
            'next_contact_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'inspection_notice_sent' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('id', true); // 主キー
        $this->forge->addKey('reservation_no', false, true); // UNIQUEキー
        $this->forge->addKey('reservation_guid', false, true); // UNIQUEキー

        // INDEX設定
        $this->forge->addKey('desired_date');
        $this->forge->addKey('customer_name');
        $this->forge->addKey('customer_kana');
        $this->forge->addKey('phone_number1');
        $this->forge->addKey('vehicle_license_number');
        $this->forge->addKey('send_inspection_notice');
        $this->forge->addKey('next_contact_date');
        $this->forge->addKey('inspection_notice_sent');

        // FK用のINDEXも兼ねるため、個別にaddKeyする必要がない場合もあるが、明示的にFKカラムにもINDEXを貼る意図で追加
        $this->forge->addKey('reservation_status_id');
        $this->forge->addKey('work_type_id');
        $this->forge->addKey('shop_id');
        $this->forge->addKey('desired_time_slot_id');
        $this->forge->addKey('vehicle_type_id');
        $this->forge->addKey('next_work_type_id');

        // 外部キー制約
        $this->forge->addForeignKey('reservation_status_id', 'reserve_statuses', 'id', 'NO ACTION', 'NO ACTION');
        $this->forge->addForeignKey('work_type_id', 'work_types', 'id', 'NO ACTION', 'NO ACTION');
        $this->forge->addForeignKey('shop_id', 'shops', 'id', 'NO ACTION', 'NO ACTION');
        $this->forge->addForeignKey('desired_time_slot_id', 'time_slots', 'id', 'SET NULL', 'NO ACTION');
        $this->forge->addForeignKey('vehicle_type_id', 'vehicle_types', 'id', 'SET NULL', 'NO ACTION');
        $this->forge->addForeignKey('next_work_type_id', 'work_types', 'id', 'SET NULL', 'NO ACTION');

        $this->forge->createTable('reservations');
    }

    public function down()
    {
        $this->forge->dropTable('reservations');
    }
}