<?php

namespace InfusedPay;


/**
 * @group InfusedPay
 */
class Test_Processor extends \TestCase
{
    protected function setUp()
    {
        \Package::load('infusedpay');

        $this->invalid_gateway = 'not_valid';

        $this->authnet_gateway = 'authorizenet';
        $this->authnet_test_credentials = array('transaction_key'=>'test','api_id'=>'test');
        $this->authnet_invalid_format_credentials = array('wrong_key'=>'','wrong_id'=>'');

        $this->paypal_gateway = 'paypal';
        $this->paypal_test_credentials = array('transaction_key'=>'test','api_id'=>'test');
        $this->paypal_invalid_format_credentials = array('wrong_key'=>'','wrong_id'=>'');

    }

    protected function tearDown()
    {
        Processor::remove('all');
        \Package::unload('infusedpay');
    }

    /**
     * @expectedException \InfusedPay\PaymentGatewayInvalidException
     */
    public function test_blank_gateway_forge_throws_PaymentGatewayInvalidException()
    {
        $config = array(
            'gateway' => '',
            'credentials' => ''
        );
        Processor::forge($config);
    }

    /**
     * @expectedException \InfusedPay\PaymentGatewayInvalidException
     */
    public function test_null_gateway_forge_throws_PaymentGatewayInvalidException()
    {
        $config = array(
            'gateway' => null,
            'credentials' => ''
        );
        Processor::forge($config);
    }
    public function test_blank_gateway_forge_exception_code()
    {
        $config = array(
            'gateway' => '',
            'credentials' => ''
        );
        try{
            Processor::forge($config);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->assertEquals($e->getCode(), 0);
        }
    }

    /**
     * @expectedException \InfusedPay\PaymentGatewayInvalidException
     */
    public function test_no_credentials_forge_exception()
    {
        $config = array(
            'gateway' => $this->invalid_gateway,
            'credentials' => ''
        );
        Processor::forge($config);
    }
    /**
     * @expectedException \InfusedPay\PaymentGatewayInvalidException
     */
    public function test_null_credentials_forge_exception()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => null
        );
        Processor::forge($config);
    }
    public function test_no_credentials_forge_exception_code()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => ''
        );
        try{
            Processor::forge($config);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->assertEquals($e->getCode(), 1);
        }
    }

    /**
     * @expectedException \InfusedPay\PaymentGatewayInvalidException
     */
    public function test_unsupported_gateway_forge_throws_PaymentGatewayInvalidException()
    {
        $config = array(
            'gateway' => $this->invalid_gateway,
            'credentials' => $this->authnet_test_credentials
        );
        Processor::forge($config);
    }
    public function test_unsupported_gateway_forge_exception_code()
    {
        $config = array(
            'gateway' => $this->invalid_gateway,
            'credentials' => $this->authnet_test_credentials
        );
        try{
            Processor::forge($config);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->assertEquals($e->getCode(), 3);
        }
    }

    public function test_forge_supported_gateway()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        try{
            Processor::forge($config);
            $this->assertTrue(true);
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }
    }

    public function test_forge_existing_gateway_throws_already_exists_exception()
    {
        $config = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        try{
            Processor::forge($config);
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        try{
            Processor::forge($config);
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Expected Processor Exception, not PaymentGatewayInvalid exception');
        }
        catch(ProcessorException $e)
        {
            $this->assertEquals($e->getCode(), 4);
        }

    }

    public function test_forge_multiple_gateways_succeeds()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        $config2 = array(
            'gateway' => $this->paypal_gateway,
            'credentials' => $this->paypal_test_credentials
        );

        try{
            Processor::forge($config1);
            Processor::forge($config2);
            $this->assertTrue(true);
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

    }


    public function test_instance_throws_processor_exception_when_no_gateway_or_credentials_defined()
    {
        try{
            // First unset any default settings from config files that may exist
            \Config::set('infusedpay.gateway',null);
            \Config::set('infusedpay.credentials',null);
            Processor::instance();
            $this->fail('Expected \InfusedPay\PaymentGatewayInvalidException 0');
        }

        catch(PaymentGatewayInvalidException $e)
        {
            $this->assertEquals($e->getCode(), 0);
        }
    }

    public function test_instance_throws_processor_exception_when_no_instances_set_with_gateway_id()
    {
        try{
            Processor::instance('authorizenet');
            $this->fail('Expected \InfusedPay\PaymentGatewayInvalidException 0');
        }

        catch(ProcessorException $e)
        {
            $this->assertEquals($e->getCode(), 5);
        }
    }

    public function test_instance_forges_new_gateway()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        try{
            $processor = Processor::instance($config1);
            $this->assertEquals('InfusedPay\Adapter_Authorizenet',get_class($processor));
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }
    }

    public function test_instance_forges_multiple_gateways_succeeds()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        $config2 = array(
            'gateway' => $this->paypal_gateway,
            'credentials' => $this->paypal_test_credentials
        );

        try{
            $p1 = Processor::instance($config1);
            $p2 = Processor::instance($config2);
            $this->assertEquals('InfusedPay\Adapter_Authorizenet',get_class($p1));
            $this->assertEquals('InfusedPay\Adapter_Paypal',get_class($p2));
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }
    }

    public function test_instance_throws_error_on_bad_gateway_id()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        $config2 = array(
            'gateway' => $this->paypal_gateway,
            'credentials' => $this->paypal_test_credentials
        );

        try{
            $p1 = Processor::instance($config1);
            $p2 = Processor::instance($config2);

            try{
                Processor::instance($this->invalid_gateway);
            }
            catch(ProcessorException $e)
            {
                $this->assertEquals($e->getCode(), 5);
            }
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }
    }

    public function test_instance_returns_named_instance()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        $config2 = array(
            'gateway' => $this->paypal_gateway,
            'credentials' => $this->paypal_test_credentials
        );

        try{
            Processor::instance($config1);
            Processor::instance($config2);

            $this->assertEquals('InfusedPay\Adapter_Authorizenet',get_class(Processor::instance($config1['gateway'])));
            $this->assertEquals('InfusedPay\Adapter_Paypal',get_class(Processor::instance($config2['gateway'])));
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

    }

    public function test_instance_defaults_to_first_instance()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        $config2 = array(
            'gateway' => $this->paypal_gateway,
            'credentials' => $this->paypal_test_credentials
        );

        try{
            Processor::instance($config1);
            Processor::instance($config2);

            $this->assertEquals(Processor::instance(),Processor::instance($config1['gateway']));
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }
    }

    public function test_remove_first_instance_defaults_next_instance()
    {
        $config1 = array(
            'gateway' => $this->authnet_gateway,
            'credentials' => $this->authnet_test_credentials
        );

        $config2 = array(
            'gateway' => $this->paypal_gateway,
            'credentials' => $this->paypal_test_credentials
        );

        try{
            Processor::instance($config1);
            Processor::instance($config2);

            Processor::remove($config1['gateway']);

            $this->assertEquals(Processor::instance(),Processor::instance($config2['gateway']));
        }
        catch(PaymentGatewayInvalidException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

        catch(ProcessorException $e)
        {
            $this->fail('Unexpected exception ('.$e->getCode().'): '.$e->getMessage());
        }

    }

    public function test_config_settings_are_taken_as_default()
    {
        \Config::set('infusedpay.gateway',$this->authnet_gateway);
        \Config::set('infusedpay.credentials',$this->authnet_test_credentials);

        Processor::instance();
    }
}
