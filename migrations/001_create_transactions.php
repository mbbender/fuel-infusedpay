<?php

namespace Fuel\Migrations;

class Create_transactions
{

	public function up()
	{
		\DBUtil::create_table('transactions', array(
			'id' => array('type' => 'int unsigned', 'auto_increment' => true),
            'processor' => array('constraint'=>50, 'type'=>'varchar'),
			'third_party_transaction_id' => array('constraint'=>50, 'type'=>'varchar'),
            'type' => array('constraint'=>50, 'type'=>'varchar'),
            'ship_to_first' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_last' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_company' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_address1' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_address2' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_city' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_state' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'ship_to_zipcode' => array('constraint'=>15, 'type'=>'varchar', 'null'=>true),
            'ship_to_country' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_first' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_last' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_company' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_address1' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_address2' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_city' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_state' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_zipcode' => array('constraint'=>15, 'type'=>'varchar', 'null'=>true),
            'bill_to_country' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_phone' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'bill_to_email' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'currency' => array('constraint'=>15, 'type'=>'varchar', 'null'=>true),
            'amount' => array('constraint' => '10,2', 'type' => 'decimal'),
            'tax' => array('constraint' => '8,2', 'type' => 'decimal', 'null'=>true),
            'freight' => array('constraint' => '8,2', 'type' => 'decimal', 'null'=>true),
            'card_holder_first_name' => array('constraint'=>255, 'type'=>'varchar'),
            'card_holder_last_name' => array('constraint'=>255, 'type'=>'varchar'),
            'card_number' => array('constraint'=>25, 'type'=>'varchar'),
            'card_cvv' => array('constraint'=>10, 'type'=>'varchar', 'null'=>true),
            'card_expiration' => array('constraint'=>11, 'type'=>'int'),
            'third_party_metadata' => array('type'=>'text', 'null'=>true),
            'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int')
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('transactions');
	}
}