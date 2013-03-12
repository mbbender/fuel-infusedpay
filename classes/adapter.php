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
    const NAME = 'abstract';
    const AUTH_CAPTURE = 'authorizeAndCapture';
    const AUTH_ONLY = 'authorizeOnly';
    const CAPTURE_ONLY = 'captureOnly';
    const PRIOR_AUTH_CAPTURE = 'priorAuthCapture';

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


    public function process($type, Model_Transaction $trans)
    {
        $func = '_'.$type;
        if(method_exists($this,$func)){
            $trans->processor = static::NAME;
            $response = $this->$func($trans);
            $this->_log_response($response);
            $trans->type = $type;
            $trans->save();
        }
        else throw new AdapterException('Unsupported transaction type: '.$type);
    }

    // Convenience method
    public function charge(Model_Transaction $transaction)
    {
       return $this->process(Processor::TYPE_CHARGE, $transaction);
    }

    // Convenience method for default (first) instance
    public function refund(Model_Transaction $transaction)
    {
        return $this->process(Processor::TYPE_REFUND, $transaction);
    }

    // Convenience method for default (first) instance
    public function void(Model_Transaction $transaction)
    {
        return $this->process(Processor::TYPE_VOID, $transaction);
    }

    public function get_credentials()
    {
        return $this->_credentials;
    }

    /**
     * @abstract
     * @param Model_Transaction $trans
     * @param $method Should be one of the consts of AUTH_CAPTURE, AUTH_ONLY, CAPTURE_ONLY, PRIOR_AUTH_CAPTURE
     * @return mixed Raw gateway response data
     * @throws FailedTransactionException if transaction did not process
     */
    protected abstract function _charge(Model_Transaction $trans, $method=null);
    protected abstract function _refund(Model_Transaction $trans);
    protected abstract function _void(Model_Transaction $trans);

    /**
     * Must log the response into the database of the liked named processor responses table.
     *
     * @param $response
     * @return mixed
     */
    protected abstract function _log_response($response);

}
