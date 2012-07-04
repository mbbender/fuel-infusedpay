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
 * @group InfusedPay
 */
class Test_Adapter_Authorizenet extends \TestCase
{


    public function setUp()
    {
        \Package::load('infusedpay');

        $this->invalid_gateway = 'not_valid';

        $this->authnet_gateway = 'authorizenet';
        $this->authnet_test_credentials = array('transaction_key'=>'','api_id'=>'');
        $this->authnet_invalid_format_credentials = array('wrong_key'=>'','wrong_id'=>'');

        $this->charge_transaction = Model_Transaction::forge(array(
            'amount' => 1,
            'card_number' => '4111111111111111',
            'card_expiration' => strtotime("+1 year")
        ));

        $li = Model_Lineitem::forge(array(
            'sku' => 'testsku',
            'name' => 'testname',
            'description' => 'testdescription',
            'quantity' => 3,
            'unit_price' => 4.99,
            'taxable' => true,
            'type' => null
        ));

        $this->charge_transaction->lineitems[0] = $li;

    }

    protected function tearDown()
    {
        Processor::remove('all');
        \Package::unload('infusedpay');
    }

    public function test_invalid_credential_trans_id_throw_exceptions()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => array(
                'wrong_transaction_key' => '',
                'api_id' => 'test'
            )
        );

        try{
            Processor::forge($config);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->assertEquals(4, $e->getCode());
        }
    }

    public function test_invalid_credential_api_id_throw_exceptions()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => array(
                'transaction_key' => 'test',
                'api_id_wrong' => ''
            )
        );

        try{
            Processor::forge($config);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->assertEquals(4, $e->getCode());
        }
    }

    public function test_auth_capture_transaction()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => array(
                'transaction_key' => 'will_be_autoset_to_test_config_settings_because debug mode will be set which auto
                                      pulls credentials from authnet config file',
                'api_id' => 'will_be_autoset_to_test_config_settings'
            )
        );

        try{
            Processor::forge($config);
            Processor::charge($this->charge_transaction);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

    }

    /**
     * @expectedException \InfusedPay\FailedTransactionException
     */
    public function test_failed_transaction_exception_bad_cc()
    {
        $bad_cc_transaction = Model_Transaction::forge(array(
            'amount' => 1,
            'card_number' => '4111111111',
            'card_expiration' => strtotime("+1 year")
        ));

        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => array(
                'transaction_key' => 'will_be_autoset_to_test_config_settings_because debug mode will be set which auto
                                      pulls credentials from authnet config file',
                'api_id' => 'will_be_autoset_to_test_config_settings'
            )
        );

        Processor::forge($config);
        Processor::charge($bad_cc_transaction);
    }

    public function test_void()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => array(
                'transaction_key' => 'will_be_autoset_to_test_config_settings_because debug mode will be set which auto
                                      pulls credentials from authnet config file',
                'api_id' => 'will_be_autoset_to_test_config_settings'
            )
        );

        Processor::forge($config);
        Processor::charge($this->charge_transaction);
        Processor::void($this->charge_transaction);


    }

}
