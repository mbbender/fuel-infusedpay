<?php

namespace Fuel\Migrations;

class Create_response_authnets
{
	public function up()
	{
		\DBUtil::create_table('response_authnets', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'x_response_code' => array('constraint' => 11, 'type' => 'int'),
			'x_response_reason_code' => array('constraint' => 11, 'type' => 'int'),
			'x_response_reason_text' => array('constraint' => 255, 'type' => 'varchar'),
			'x_auth_code' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_avs_code' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_trans_id' => array('constraint' => 255, 'type' => 'varchar'),
			'x_invoice_num' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_description' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_amount' => array('constraint' => '7,2', 'type' => 'decimal'),
			'x_method' => array('constraint' => 255, 'type' => 'varchar'),
			'x_type' => array('constraint' => 255, 'type' => 'varchar'),
			'x_account_number' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_card_type' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_split_tender_id' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_prepaid_requested_amount' => array('constraint' => '7,2', 'type' => 'decimal', 'null'=>true),
			'x_prepaid_balance_on_card' => array('constraint' => '7,2', 'type' => 'decimal', 'null'=>true),
			'x_cust_id' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_first_name' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_last_name' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_company' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_address' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_city' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_state' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_zip' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_country' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_phone' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_email' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_first_name' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_last_name' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_company' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_address' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_city' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_state' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_zip' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_ship_to_country' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_tax' => array('constraint' => '7,2', 'type' => 'decimal', 'null'=>true),
			'x_duty' => array('constraint' => '7,2', 'type' => 'decimal', 'null'=>true),
			'x_freight' => array('constraint' => '7,2', 'type' => 'decimal', 'null'=>true),
			'x_tax_exempt' => array('type' => 'boolean', 'null'=>true),
			'x_po_num' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_MD5_Hash' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_cvv2_resp_code' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'x_cavv_response' => array('constraint' => 255, 'type' => 'varchar', 'null'=>true),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('response_authnets');
	}
}