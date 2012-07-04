<?php
/**
 * InfusedAuth is an add on to SimpleAuth
 * @package    InfusedAuth
 * @version    1.0
 * @author     Michael Bneder
 * @license    Commercial License
 * @copyright  2012 Infused Industries, Inc.
 * @link       http://sociablegroup.com
 */

namespace InfusedPay;

class Model_Lineitem extends \Orm\Model
{
    protected static $_belongs_to = array('transaction');

    protected static $_properties = array(
        'id',
        'transaction_id',
        'sku',
        'name',
        'description',
        'quantity',
        'unit_price',
        'taxable',
        'type', // digital / tangible
        'updated_at',
        'created_at'
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => false,
        ),
    );
}
