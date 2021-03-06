<?php

namespace Fuel\Migrations;

class Create_lineitems
{

	public function up()
	{
		\DBUtil::create_table('lineitems', array(
			'id' => array('type' => 'int unsigned', 'auto_increment' => true),
			'transaction_id' => array('type'=>'int unsigned'),
            'sku' => array('constraint'=>150, 'type'=>'varchar'),
            'name' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'description' => array('constraint'=>255, 'type'=>'varchar', 'null'=>true),
            'quantity' => array('constraint'=>10, 'type'=>'int'),
            'unit_price' => array('constraint'=>'12,2', 'type'=>'decimal'),
            'taxable' => array('constraint'=>1, 'type'=>'tinyint', 'null'=>true),
            'type' => array('constraint'=>50, 'type'=>'varchar', 'null'=>true),
            'metadata' => array('type'=>'text', 'null'=>true),
            'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int')
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('lineitems');
	}
}