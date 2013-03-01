<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael
 * Date: 2/20/13
 * Time: 1:13 PM
 * To change this template use File | Settings | File Templates.
 */
namespace InfusedPay;

abstract class TransactionFormatter
{
    /**
     * @abstract
     * @param $raw_transaction_data array  Data in any format, the concrete Formatter implementation should define the data format required
     * @return Model_Transaction A direct or subclass of Model_Transaction
     */
    abstract public function format($raw_transaction_data);
}
