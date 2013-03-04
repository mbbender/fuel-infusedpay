<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael
 * Date: 3/1/13
 * Time: 9:28 AM
 * To change this template use File | Settings | File Templates.
 */

namespace InfusedPay;

interface TransactionProcessor
{
    public function set_processor(Model_ProcessorInfo $processor);
    public function get_gateway();
    public function get_gateway_credentials();
}