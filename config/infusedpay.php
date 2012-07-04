<?php
/**
 * InfusedPay is an abstracted payment processor
 * @package    InfusedPay
 * @version    1.0
 * @author     Michael Bneder
 * @license    Commercial License
 * @copyright  2012 Infused Industries, Inc.
 * @link       http://sociablegroup.com
 */
 
return array(

    /**
     * Always set this to true in production. It is recommended that you leave this set to true in development
     * also.
     *
     * //todo: Implement ssl enforcement
     */
    'require_ssl' => true,

    /**
     * Log transactions (requires transaction_log table to be created)
     *
     * //todo: Implement logging
     */
    'log_transactions' => true,

    /**
     * Used for default gateway info. Useful if you are not changing and instantiating more than one
     * gateway.
     *
     * Options are: authorizenet, paypal
     */
    'gateway' => null,

    /**
     * Default gateway credentials
     *
     * An array of login/api credentials for the set payment gateway above. This will vary based on gateway type
     * You can also pass this in as a configuration when creating processors to override these settings
     *
     * authorizenet
     * array('transaction_key'=>'','api_id'=>'')
     *
     * paypal
     * tbd
     */
    'credentials' => null

);