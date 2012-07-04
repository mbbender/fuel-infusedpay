<?php
/**
 * InfusedPay is an abstracted payment adapter that allows you to integrate payments into your site
 * for things like Paypal and Authorize.net
 *
 * @package    InfusedAuth
 * @version    1.0
 * @author     Michael Bneder
 * @license    Commercial License
 * @copyright  2012 Infused Industries, Inc.
 * @link       http://sociablegroup.com
 */
 

namespace InfusedPay;

/**
 * 0 => Invalid or unsupported API
 */
class AdapterException extends \FuelException{}

abstract class Adapter
{
    const AUTH_CAPTURE = 'authorizeAndCapture';
    const AUTH_ONLY = 'authorizeOnly';
    const CAPTURE_ONLY = 'captureOnly';
    const PRIOR_AUTH_CAPTURE = 'priorAuthCapture';

    /**
     * @var  string  Adapter name
     */
    public $name;


    public static function forge($adapter)
    {
        $class = 'InfusedPay\\Adapter_'.ucfirst($adapter);

        return new $class;
    }

    public function charge(Model_Transaction $trans)
    {
        $this->_charge($trans);
        $trans->type = 'charge';
        $trans->save();
    }

    public function refund(Model_Transaction $trans)
    {
        $this->_refund($trans);
        $trans->type = 'refund';
        $trans->save();
    }

    public function void(Model_Transaction $trans)
    {
        $this->_void($trans);
        $trans->type = 'void';
        $trans->save();
    }

    protected abstract function _charge(Model_Transaction $trans);
    protected abstract function _refund(Model_Transaction $trans);
    protected abstract function _void(Model_Transaction $trans);


    /**
     * This function should be used to maniuplate the Model_Transaction object into a format that the implementing
     * gateway can understand.
     *
     * @abstract
     * @param Model_Transaction $trans
     * @return mixed
     */
    protected abstract function _format_transaction(Model_Transaction $trans);

    /**
     * This function should process the gateway response and return true if the action went through successfully.
     *
     * It should throw a FailedTransactionException with failure details if the transaction did not succeed.
     *
     * @abstract
     * @param $gateway_response
     * @return true on success
     * @throws FailedTransactionException If transaction is in error or declined states
     */
    protected abstract function _process_response($gateway_response);
}
