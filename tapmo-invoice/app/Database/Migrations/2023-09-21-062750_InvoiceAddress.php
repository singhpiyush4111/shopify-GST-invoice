<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InvoiceAddress extends Migration
{
    public function up()
    {
          $this->forge->addField([
			'id'              => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'order_id'        => [
				'type'           => 'VARCHAR',
				'constraint'     => 255
			],
            'is_draft_order'  => [
				'type' => 'TINYINT',
				'constraint'     => 1,
                'default'        => 0
			],
			'billing_address' => [
				'type'           => 'JSON',
				'null'           => true,
			],
            'shipping_address'=> [
				'type'           => 'JSON',
				'null'           => true,
			],
            'gst_details'     =>[
                "type"          => 'JSON',
                "null"          => true
            ],
			'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
		]);

        $this->forge->addKey('id', TRUE);

		// Membuat tabel news
		$this->forge->createTable('invoice_address', TRUE);
    }

    public function down()
    {
        //
    }
}
