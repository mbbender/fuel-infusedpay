<?php
/**
 * InfusedPay is an abstracted payment processor
 *
 * Initialize
 * Processor::instance() or Processor::forge() // uses cofigurations form infusedpay config
 * Processor::instance($config) or Processor::forge($config)  // adds a processor instance
 *
 * Static calls use the default instance (first instance set with either instance() or forge())
 * Processor::charge(Transaction)
 * Processor::refund(Transaction)
 * Processor::void(Transaction)
 *
 * To call a specific gateway use the instance
 * Processor::instance('gateway_id')->charge(Transaction)
 * Processor::instance('gateway_id')->refund(Transaction)
 * Processor::instance('gateway_id')->void(Transaction)
 *
 * Notes:
 *   - Do not save a Model_Transaction before it has been processed. InfusedPay automatically saves only the last 4
 *     digits of the credit card number, so if you save the transaction model before processing it, the processor will
 *     only see the last 4 digits. You probably shouldn't be saving the transaction until it has been processed anyways.
 *
 *
 *
 * Exception Error Codes:
 * PaymentGatewayInvalidException
 * 0 => 'No payment gateway was specified, unable to instantiate driver.',
 * 1 => 'No payment gateway credentials were specified, unable to instantiate driver.',
 * 2 => NONE
 * 3 => 'An invalid gateway ['.$config['gateway'].'] was provided. This driver does not exist! ['.$adapter.']',
 * 4 => 'Invalid gateway credentials were provided' // Adapter specific
 *
 *
 * ProcessorException
 * 0 => 'Must initialize a processor before calling instance().',
 * 1 => 'Must initialize a processor before calling charge().',
 * 2 => 'Must initialize a processor before calling refund().',
 * 3 => 'Must initialize a processor before calling void().',
 * 4 =>  'Payment gateway '.$config['gateway'].' is already set.',
 * 5 => 'No gateway with id '.$gateway_id.' set. You must initialize this gateway first.'
 *
 * @package    InfusedPay
 * @version    1.0
 * @author     Michael Bneder
 * @license    Commercial License
 * @copyright  2012 Infused Industries, Inc.
 * @link       http://sociablegroup.com
 */

namespace InfusedPay;

use Lang;
use Arr;
use Config;

class PaymentGatewayInvalidException extends \FuelException{}
class ProcessorException extends \FuelException{}
class FailedTransactionException extends \FuelException{}

class Processor
{
    /**
     * An array of created processor instances.
     * @var null
     */
    protected static $instance = null;

    /**
     * Disable the ability to construct the object
     */
    final private function __construct() {}

    /**
     * Disable the ability to clone the object
     */
    final public function __clone() {}

    public static function _init()
    {
        Config::load('infusedpay', true);
    }

    /**
     * @static
     * @param array $config If set overrides config file settings
     * @return mixed
     * @throws ProcessorException
     * @throws PaymentGatewayInvalidException
     */
    public static function forge(array $config = array())
    {
        //todo:add test to make sure config file does not override passed in data [ I think it does -mbb ]
        $config = Arr::merge(array('gateway' => Config::get('infusedpay.gateway',null),
            'credentials' => Config::get('infusedpay.credentials',null)), $config);

        if(!isset($config['gateway']) or empty($config['gateway']))
        {
            throw new PaymentGatewayInvalidException('No payment gateway was specified, unable to instantiate driver.',0);
        }

        if(!isset($config['credentials']) or empty($config['credentials']))
        {
            throw new PaymentGatewayInvalidException('No payment gateway credentials were specified, unable to instantiate driver.',1);
        }

        if(isset(static::$instance) AND isset(static::$instance[$config['gateway']])) throw new ProcessorException('Payment gateway '.$config['gateway'].' is already set.',4);

        $adapter = 'InfusedPay\Adapter_'.ucfirst($config['gateway']);

        if(!class_exists($adapter))
        {
            throw new PaymentGatewayInvalidException('An invalid gateway ['.$config['gateway'].'] was provided. This driver does not exist! ['.$adapter.']',3);
        }

        if(static::$instance == null) static::$instance = array();
        static::$instance[$config['gateway']] = new $adapter($config['credentials']);

        return static::$instance[$config['gateway']];
    }

    /**
     * Config parameter
     * 'gateway'=> 'authorizenet [paypal]'
     * 'credentials' => array([processor_specific])
     *       'authorizenet' =   'transaction_key','api_id'
     *       'paypal' = TBD
     *
     * @static
     * @param array $config or string instance name i.e. authorize.net
     * @return mixed
     */
    public static function instance(/*mixed*/ $config = null)
    {
        // Try to create instance from config file
        if(empty($config) and empty(static::$instance)) static::forge();

        if(empty($config) and !empty(static::$instance))
        {
            // Return default instance
            reset(static::$instance);
            return current(static::$instance);
        }

        // Try to create the config if a configuration array is passed in
        if(is_array($config))static::forge($config);

        // Try to return the existing instance named by config
        $gateway_id = is_string($config) ? $config : $config['gateway'];
        if(isset(static::$instance[$gateway_id])) return static::$instance[$gateway_id];
        else throw new ProcessorException('No gateway with id '.$gateway_id.' set. You must initialize this gateway first.',5);
    }

    // Convenience method for default (first) instance
    public static function charge(Model_Transaction $transaction)
    {
        if(empty(static::$instance)) throw new ProcessorException('Must initialize a processor before calling charge().',1);
        reset(static::$instance);
        current(static::$instance)->charge($transaction);
    }

    // Convenience method for default (first) instance
    public static function refund(Model_Transaction $transaction)
    {
        if(empty(static::$instance)) throw new ProcessorException('Must initialize a processor before calling refund().',2);
        reset(static::$instance);
        current(static::$instance)->refund($transaction);
    }

    // Convenience method for default (first) instance
    public static function void(Model_Transaction $transaction)
    {
        if(empty(static::$instance)) throw new ProcessorException('Must initialize a processor before calling void().',3);
        reset(static::$instance);
        current(static::$instance)->void($transaction);
    }

    /**
     * Remove a gateway instance or set to all to remove all instances
     * @param $gateway_id
     */
    public static function remove($gateway_id)
    {
        if(strtolower($gateway_id) === 'all' AND isset(static::$instance)) static::$instance = null;
        if(isset(static::$instance)) unset(static::$instance[$gateway_id]);
    }

}
