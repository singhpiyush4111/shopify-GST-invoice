<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SettingMigrate extends Migration
{
    public function up()
    {
        $this->forge->addField([
			'id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'key'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255
			],
			'value'      => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
			'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
		]);

        $this->forge->addKey('id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('setting', TRUE);
    }

    public function down()
    {
        //
    }
}
