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

    const TYPE_CHARGE = 'charge';
    const TYPE_REFUND = 'refund';
    const TYPE_VOID = 'void';

    /**
     * @var  string  Adapter name
     */
    public $name;

    /**
     * @var mixed Adapter credentials
     */
    protected $_credentials;

    public function __construct($credentials)
    {
        $this->_credentials = $credentials;
    }


    public function charge(Model_Transaction $trans)
    {
        $this->_charge($trans);
        $trans->type = self::TYPE_CHARGE;
        $trans->save();
    }

    public function refund(Model_Transaction $trans)
    {
        $this->_refund($trans);
        $trans->type = self::TYPE_REFUND;
        $trans->save();
    }

    public function void(Model_Transaction $trans)
    {
        $this->_void($trans);
        $trans->type = self::TYPE_VOID;
        $trans->save();
    }

    public function get_credentials()
    {
        return $this->_credentials;
    }

    /**
     * @abstract
     * @param Model_Transaction $trans
     * @param $method Should be one of the consts of AUTH_CAPTURE, AUTH_ONLY, CAPTURE_ONLY, PRIOR_AUTH_CAPTURE
     * @return boolean TRUE if success
     * @throws FailedTransactionException if transaction did not process
     */
    protected abstract function _charge(Model_Transaction $trans, $method=null);
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
