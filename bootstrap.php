<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael Bender
 * Date: 6/11/12
 * Time: 6:46 AM
 *
 * Requires Auth to be enabled
 */


Autoloader::add_namespace('InfusedPay',__DIR__.'/classes/');

Autoloader::add_classes(array(
    'InfusedPay\\Processor'                        => __DIR__.'/classes/processor.php',
    'InfusedPay\\PaymentGatewayInvalidException'   => __DIR__.'/classes/processor.php',
    'InfusedPay\\ProcessorException'               => __DIR__.'/classes/processor.php',
    'InfusedPay\\FailedTransactionException'               => __DIR__.'/classes/processor.php',

    'InfusedPay\\Model_Transaction'                      =>__DIR__.'/classes/model/transaction.php',
    'InfusedPay\\Observer_MaskCreditCard'               =>__DIR__.'/classes/model/transaction.php',
    'InfusedPay\\Model_Lineitem'                      =>__DIR__.'/classes/model/lineitem.php',

    'InfusedPay\\Adapter'                          => __DIR__.'/classes/adapter.php',
    'InfusedPay\\AdapterException'                 => __DIR__.'/classes/adapter.php',
    'InfusedPay\\Adapter_Authorizenet'             => __DIR__.'/classes/adapter/authorizenet.php',

    'InfusedPay\\Adapter_Paypal'                   => __DIR__.'/classes/adapter/paypal.php',

    'InfusedPay\\Controller_Example'                => __DIR__.'/classes/controller/example.php'
));