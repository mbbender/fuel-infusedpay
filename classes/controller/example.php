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

class Controller_Example extends \Controller
{
    public function action_charge()
    {
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

        try{
            Processor::forge(); // Make sure to configure your default gateway and credentials in config file (or pass in $config array here)
            Processor::charge($this->charge_transaction);
            $this->charge_transaction->save();

            Processor::void($this->charge_transaction);

            return 'Charged and Voided';
        }

        catch(PaymentGatewayInvalidException $e)
        {
            return 'Unexpected exception ('.$e->getCode().'): '.$e->getMessage();
        }
        catch(FailedTransactionException $e)
        {
            return 'Transaction failed because of error ('.$e->getCode().'): '.$e->getMessage();
        }
    }

    public function action_refund($third_party_trans_id)
    {
        try{
            $transaction = Model_Transaction::find('first',array('where'=>array('third_party_transaction_id'=>$third_party_trans_id)));
            Processor::forge(); // Make sure to configure your default gateway and credentials in config file (or pass in $config array here)
            Processor::refund($transaction);

            return 'Refunded third party transaction id: '.$third_party_trans_id;
        }

        catch(PaymentGatewayInvalidException $e)
        {
            return 'Unexpected exception ('.$e->getCode().'): '.$e->getMessage();
        }
        catch(FailedTransactionException $e)
        {
            return 'Transaction failed because of error ('.$e->getCode().'): '.$e->getMessage();
        }
    }
}
