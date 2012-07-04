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

/**
 * Will always save only the last 4 numbers of creditcard making it impossible to store an entire
 * credit card number in your database to avoid PCI compliance issues.
 */
class Observer_MaskCreditCard extends \Orm\Observer
{
    protected $_property = 'card_number';

    public function before_insert(Model_Transaction $obj)
    {
        $card = (string) $obj->{$this->_property};
        $obj->{$this->_property} = substr($card,strlen($card)-4,4);
    }
}

class Model_Transaction extends \Orm\Model
{
    protected static $_has_many = array('lineitems');

    protected static $_properties = array(
        'id',
        'third_party_transaction_id',
        'type',
        'ship_to_first',
        'ship_to_last',
        'ship_to_company',
        'ship_to_address',
        'ship_to_city',
        'ship_to_state',
        'ship_to_zipcode',
        'ship_to_country',
        'bill_to_first',
        'bill_to_last',
        'bill_to_company',
        'bill_to_address',
        'bill_to_city',
        'bill_to_state',
        'bill_to_zipcode',
        'bill_to_country',
        'bill_to_phone',
        'bill_to_email',
        'currency',
        'amount',
        'tax',
        'freight',
        'card_holder_first_name',
        'card_holder_last_name',
        'card_number',
        'card_cvv',
        'card_expiration',
        'third_party_metadata',
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
        'InfusedPay\Observer_MaskCreditCard' => array(
            'events' => array('before_insert')
        )
    );

    public function billing_info()
    {
        return array(
            'bill_to_first' => $this->bill_to_first,
            'bill_to_last' => $this->bill_to_last,
            'bill_to_company' => $this->bill_to_company,
            'bill_to_address' => $this->bill_to_address,
            'bill_to_city' => $this->bill_to_city,
            'bill_to_state' => $this->bill_to_state,
            'bill_to_zipcode' => $this->bill_to_zipcode,
            'bill_to_country' => $this->bill_to_country,
            'bill_to_phone' => $this->bill_to_phone,
            'bill_to_email' => $this->bill_to_email,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'tax' => $this->tax,
            'freight' => $this->freight
        );
    }

    public function shipping_info()
    {
        return array(
            'ship_to_first' => $this->ship_to_first,
            'ship_to_last' => $this->ship_to_last,
            'ship_to_company' => $this->ship_to_company,
            'ship_to_address' => $this->ship_to_address,
            'ship_to_city' => $this->ship_to_city,
            'ship_to_state' => $this->ship_to_state,
            'ship_to_zipcode' => $this->ship_to_zipcode,
            'ship_to_country' => $this->ship_to_country
        );
    }

    public function card_info()
    {
        return array(
            'card_holder_first_name' => $this->card_holder_first_name,
            'card_holder_last_name' => $this->card_holder_last_name,
            'card_number' => $this->card_number,
            'card_cvv' => $this->card_cvv,
            'card_expiration' => $this->card_expiration
        );
    }
}
