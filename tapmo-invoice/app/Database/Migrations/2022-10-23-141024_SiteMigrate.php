<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class SiteMigrate extends Migration
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
			'site_name'       => [
				'type'           => 'VARCHAR',
				'constraint'     => 255
			],
			'access_Token'      => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
			'shop_url' => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
			],
			'status'      => [
				'type'           => 'ENUM',
				'constraint'     => ['published', 'draft'],
				'default'        => 'draft',
			],
			// 'created_at'      => [
			// 	'type'           => 'TIMESTAMP',
			// 	'default'        => new RawSql('CURRENT_TIMESTAMP'),
			// ],
			// 'updated_at'      => [
			// 	'type'           => 'TIMESTAMP',
			// 	'NULL'           =>  true,
			// 	'default'        => new RawSql('CURRENT_TIMESTAMP'),
			// 	'on update'      => new RawSql('CURRENT_TIMESTAMP'),
				
			// ]
			'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
		]);

		// Membuat primary key
		$this->forge->addKey('id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('sites', TRUE);
    } 

    public function down()
    {
        $this->forge->dropTable('sites');
        //
    }
}
