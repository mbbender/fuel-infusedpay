<?php

namespace InfusedPay;

// @todo: Create a bootstrap that adds all these libraries to the InfusedPay namespace on init of this adapter
require_once __DIR__.'/../../vendor/anet_php_sdk/AuthorizeNet.php';

class Adapter_Authorizenet extends Adapter
{
    const NAME = 'authorizenet';

    protected $api;

    protected $debug = false;
    protected $test_cards =  ['370000000000002','6011000000000012','4007000000027','4012888818888','3088000000000017','38000000000006'];

    protected $transaction_key;
    protected $api_id;


    public function __construct($credentials,$debug_on = false)
    {
        parent::__construct($credentials);

        \Config::load('authorizenet','infusedpay');

        //Setup debug mode
        if(\Fuel::$env == \Fuel::TEST OR \Fuel::$env == \Fuel::DEVELOPMENT OR \Fuel::$env == 'local' OR $debug_on) $this->debug = true;


        $this->name = static::NAME;
        $this->transaction_key = $this->debug ? \Config::get('infusedpay.authorizenet_test_transaction_key',null) :  (isset($credentials['transaction_key']) ? $credentials['transaction_key'] : null);
        $this->api_id = $this->debug ? \Config::get('infusedpay.authorizenet_test_api_id',null) : (isset($credentials['api_id']) ? $credentials['api_id'] : null);


        // Validate parameters
        if(empty($this->transaction_key)) throw new PaymentGatewayInvalidException('Invalid gateway credentials were provided. transaction_key is empty.', 4);
        if(empty($this->api_id)) throw new PaymentGatewayInvalidException('Invalid gateway credentials were provided. api_id is empty.', 4);

        // todo: Abstract switch statement into a strategy configuration
        switch(\Config::get('infusedpay.authorizenet_api','AIM'))
        {
            case 'AIM':
                $this->api = new \AuthorizeNetAIM($this->api_id,$this->transaction_key);
                if($this->debug) $this->api->duplicate_window = 0;
                break;
            default:
                throw new AdapterException('Invalid or unsupported api method set in adapter configuration file.',0);
        }

        // this is sepcific to AIM
        $this->api->setSandbox($this->debug);
    }

    /*
     * Method can be set to:
     *  Adapter::AUTH_CAPTURE
     */
    protected function _charge(Model_Transaction $trans, $method = null)
    {
        $this->_format_transaction($trans);

        // Set Debug Mode if card is a test card number
        if(in_array($trans->card_number,$this->test_cards)) $this->api->setSandbox(true);

        if(empty($method)) $method = static::AUTH_CAPTURE;

        $api_func = null;
        switch($method)
        {
            case static::AUTH_CAPTURE:
                $response = $this->api->authorizeAndCapture(
                    $trans->amount,
                    $trans->card_number,
                    date('m',time($trans->card_expiration)).date('y',time($trans->card_expiration)));
                break;
        }

        $this->_process_response($response);
        $trans->third_party_transaction_id = $response->transaction_id;
        return $response;
    }

    /**
     * Defaults to refunding the full amount of hte given transaction
     *
     * @param Model_Transaction $trans
     * @param null $amount Optional amount to refund.
     * @return bool
     */
    protected function _refund(Model_Transaction $trans, $amount = null)
    {
        $amount = empty($amount) ? $trans->amount : $amount;
        $gateway_response = $this->api->credit($trans->third_party_transaction_id,$amount,$trans->card_number);
        $this->_process_response($gateway_response,$trans);
        return $gateway_response;
    }

    protected function _void(Model_Transaction $trans)
    {
        $gateway_response = $this->api->void($trans->third_party_transaction_id);
        $this->_process_response($gateway_response);
        return $gateway_response;
    }

    protected function _log_response($response)
    {
        $data = array();
        foreach($response as $key=>$val)
        {
            switch($key){
                case 'authorization_code':
                    $data['x_auth_code'] = $val;
                    break;
                case 'avs_response':
                    $data['x_avs_code'] = $val;
                    break;
                case 'transaction_id':
                    $data['x_trans_id'] = $val;
                    break;
                case 'invoice_number':
                    $data['x_invoice_num'] = $val;
                    break;
                case 'transaction_type':
                    $data['x_type'] = $val;
                    break;
                case 'customer_id':
                    $data['x_cust_id'] = $val;
                    break;
                case 'email_address':
                    $data['x_email'] = $val;
                    break;
                case 'purchase_order_number':
                    $data['x_po_num'] = $val;
                    break;
                case 'md5_hash':
                    $data['x_MD5_Hash'] = $val;
                    break;
                case 'card_code_response':
                    $data['x_cvv2_resp_code'] = $val;
                    break;
                default:
                    $data['x_'.$key] = $val;
                    break;
            }

        }

        try{
            $r = Model_Response_Authnet::forge($data);
            if($r->save()) return true;
            else throw new AdapterException('Failed to log raw gateway response: '.json_encode($response));
        }
        catch(\Database_Exception $e)
        {
            throw new AdapterException('Failed to log raw gateway response: '.json_encode($response).'. Due to: '.$e->getMessage());
        }
    }

    protected function _format_transaction(Model_Transaction $t)
    {
        /*  Not used fields

        "allow_partial_auth",
        "auth_code",
        "authentication_indicator",
        "bank_aba_code",
        "bank_acct_name",
        "bank_acct_num",
        "bank_acct_type",
        "bank_check_number",
        "bank_name",
        "cardholder_authentication_value",
        "cust_id",
        "customer_ip",
        "delim_char",
        "delim_data",
        "description",
        "duplicate_window",
        "duty",
        "echeck_type",
        "email_customer",
        "exp_date",
        "fax",
        "footer_email_receipt",
        "header_email_receipt",
        "invoice_num",
        "login",
        "method",
        "po_num",
        "recurring_billing",
        "relay_response",
        "split_tender_id",
        "tax_exempt",
        "test_request",
        "tran_key",
        "type",
        "version",

        */
        try{
            $this->api->ship_to_first_name = $t->ship_to_first;
            $this->api->ship_to_last_name = $t->ship_to_last;
            $this->api->ship_to_company = $t->ship_to_company;
            $this->api->ship_to_address = $t->ship_to_address1.' '.$t->ship_to_address2;
            $this->api->ship_to_city = $t->ship_to_city;
            $this->api->ship_to_state = $t->ship_to_state;
            $this->api->ship_to_zip = $t->ship_to_zipcode;
            $this->api->ship_to_country = $t->ship_to_country;
            $this->api->first_name = $t->bill_to_first;
            $this->api->last_name = $t->bill_to_last;
            $this->api->company = $t->bill_to_company;
            $this->api->address = $t->bill_to_address1.' '.$t->bill_to_address2;
            $this->api->city = $t->bill_to_city;
            $this->api->state = $t->bill_to_state;
            $this->api->zip = $t->bill_to_zipcode;
            $this->api->country = $t->bill_to_country;
            $this->api->phone = $t->bill_to_phone;
            $this->api->email = $t->bill_to_email;
            //$this->api->amount = $t->currency;
            $this->api->amount = $t->amount;
            $this->api->tax = $t->tax;
            $this->api->freight = $t->freight;
            //$this->api->amount = $t->card_holder_first_name;
            //$this->api->amount = $t->card_holder_last_name;
            $this->api->card_num = $t->card_number;
            $this->api->card_code = $t->card_cvv;
            $this->api->exp_date = date('m',time($t->card_expiration)).date('y',time($t->card_expiration));
            $this->api->trans_id = $t->third_party_transaction_id;

            foreach($t->lineitems as $li)
            {
                if(strlen($li->sku) > 31) throw new Exception('Authorize.net does not support SKUs longer than 31 characters.');
                $this->api->addLineItem($li->sku,substr($li->name,0,31),substr($li->description,0,255),$li->quantity,$li->unit_price,$li->taxable);
            }
        }

        catch(\AuthorizeNetException $e)
        {
            throw new AdapterException($e->getMessage());
        }

        return  $this->api->amount = $t;
    }

    // Should return TRUE if all went well or throw a FailedTransactionException
    protected function _process_response($response,$transaction)
    {
        $ret_val = false;
        switch($response->response_code)
        {
            // Approved
            case 1:
                $ret_val = true;
                // Authorize.net puts some tax in the duty column for some reason on occasion and we need it to be
                // declared as tax.
                if($response->duty != 0) $transaction->tax += $response->duty;
                break;
            // Declined
            case 2:
                /*
                response reason codes:
                    2 - This transaction has been declined.
                    3 - This transaction has been declined.
                    4 - This transaction has been declined.
                    25 - An error occurred during processing. Please try again in 5 minutes.
                    26 - An error occurred during processing. Please try again in 5 minutes.
                    27 - The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder.
                    28 - The merchant does not accept this type of credit card.
                    29 - The Paymentech identification numbers are incorrect. Call Merchant Service Provider.
                    30 - The configuration with the processor is invalid. Call Merchant Service Provider.
                    31 - The FDC Merchant ID or Terminal ID is incorrect. Call Merchant Service Provider.
                    34 - The VITAL identification numbers are incorrect. Call Merchant Service Provider.
                    35 - An error occurred during processing. Call Merchant Service Provider.
                    37 - The credit card number is invalid.
                    38 - The Global Payment System identification numbers are incorrect. Call Merchant Service Provider.
                    41 - This transaction has been declined.
                    44 - This transaction has been declined. / The card code submitted with the transaction did not match the card code on file at the card issuing bank and the transaction was declined
                    45 - This transaction has been declined.
                    65 - This transaction has been declined.
                    127 - The transaction resulted in an AVS mismatch. The address provided does not match billing address of cardholder.
                    141 - This transaction has been declined. //The system-generated void for the original FraudScreen-rejected transaction failed.
                    145 - This transaction has been declined. //The system-generated void for the original card code-rejected and AVS-rejected transaction failed.
                    165 - This transaction has been declined.
                    171 - An error occurred during processing. Please contact the merchant.
                    172 - An error occurred during processing. Please contact the merchant.
                    174 - The transaction type is invalid. Please contact the merchant.
                    200 - This transaction has been declined.
                    201 - This transaction has been declined.
                    202 - This transaction has been declined.
                    203 - This transaction has been declined.
                    204 - This transaction has been declined.
                    205 - This transaction has been declined.
                    206 - This transaction has been declined.
                    207 - This transaction has been declined.
                    208 - This transaction has been declined.
                    209 - This transaction has been declined.
                    210 - This transaction has been declined.
                    211 - This transaction has been declined.
                    212 - This transaction has been declined.
                    213 - This transaction has been declined.
                    214 - This transaction has been declined.
                    215 - This transaction has been declined.
                    216 - This transaction has been declined.
                    217 - This transaction has been declined.
                    218 - This transaction has been declined.
                    219 - This transaction has been declined.
                    220 - This transaction has been declined.
                    221 - This transaction has been declined.
                    222 - This transaction has been declined.
                    223 - This transaction has been declined.
                    224 - This transaction has been declined.
                    250 - This transaction has been declined. //This transaction was submitted from a blocked IP address.
                    251 - This transaction has been declined. //The transaction was declined as a result of triggering a Fraud Detection Suite filter.
                    254 - Your transaction has been declined. //The transaction was declined after manual review.
                    315 - The credit card number is invalid.
                    316 - The credit card expiration date is invalid.
                    317 - The credit card has expired.
                    318 - A duplicate transaction has been submitted.
                    319 - The transaction cannot be found.
                    */
                throw new FailedTransactionException($response->response_reason_text,$response->response_reason_code);
                break;
            // Error
            case 3:
                /* reason response codes:
                    33 - FIELD cannot be left blank.
                    36 - The authorization was approved, but settlement failed.
                    40 - This transaction must be encrypted.
                    43 - The merchant was incorrectly set up at the processor. Call your Merchant Service Provider.[]
                    46 - Your session has expired or does not exist. You must log in to continue working.
                    47 - The amount requested for settlement may not be greater than the original amount authorized.
                    48 - This processor does not accept partial reversals.
                    49 - A transaction amount greater than $[amount] will not be accepted.
                    50 - This transaction is awaiting settlement and cannot be refunded.
                    51 - The sum of all credits against this transaction is greater than the original transaction amount.
                    52 - The transaction was authorized, but the client could not be notified; the transaction will not be settled.
                    53 - The transaction type was invalid for ACH transactions.
                    54 - The referenced transaction does not meet the criteria for issuing a credit.
                    55 - The sum of credits against the referenced transaction would exceed the original debit amount.
                    56 - This merchant accepts ACH transactions only; no credit card transactions are accepted.
                    57 - An error occurred in processing. Please try again in 5 minutes.
                    58 - An error occurred in processing. Please try again in 5 minutes.
                    59 - An error occurred in processing. Please try again in 5 minutes.
                    60 - An error occurred in processing. Please try again in 5 minutes.
                    61 - An error occurred in processing. Please try again in 5 minutes.
                    62 - An error occurred in processing. Please try again in 5 minutes.
                    63 - An error occurred in processing. Please try again in 5 minutes.
                    66 - This transaction cannot be accepted for processing.
                    68 - The version parameter is invalid. //The value submitted in x_version was invalid.
                    69 - The transaction type is invalid. //The value submitted in x_type was invalid.
                    70 - The transaction method is invalid. //The value submitted in x_method was invalid.
                    71 - The bank account type is invalid. //The value submitted in x_bank_acct_type was invalid.
                    72 - The authorization code is invalid. //The value submitted in x_auth_code was more than six characters in length.
                    73 - The drivers license date of birth is invalid. //The format of the value submitted in x_drivers_license_dob was invalid.
                    74 - The duty amount is invalid. //The value submitted in x_duty failed format validation
                    75 - The freight amount is invalid. //The value submitted in x_freight failed format validation.
                    76 - The tax amount is invalid. // The value submitted in x_tax failed format validation.
                    77 - The SSN or tax ID is invalid. //The value submitted in x_customer_tax_id failed validation.
                    78 - The Card Code (CVV2/CVC2/CID) is invalid. //The value submitted in x_card_code failed format validation.
                    79 - The drivers license number is invalid. //The value submitted in x_drivers_license_num failed format validation.
                    80 - The drivers license state is invalid. //The value submitted in x_drivers_license_state failed format validation.
                    81 - The requested form type is invalid. //The merchant requested an integration method not compatible with the AIM API.
                    82 - Scripts are only supported in version 2.5.
                    83 - The requested script is either invalid or no longer supported.
                    91 - Version 2.5 is no longer supported
                    92 - The gateway no longer supports the requested method of integration.
                    97 - This transaction cannot be accepted. //The transaction fingerprint has expired.
                    98 - This transaction cannot be accepted. //The transaction fingerprint has already been used.
                    99 - This transaction cannot be accepted. //The server-generated fingerprint does not match the merchant-specified fingerprint in the x_fp_hash field.
                    100 - The eCheck.Net type is invalid. //Applicable only to eCheck.Net. The value specified in the x_echeck_type field is invalid.
                    101 - The given name on the account and/or the account type does not match the actual account.
                    102 - This request cannot be accepted. //A password or Transaction Key was submitted with this WebLink request. This is a high security risk.
                    103 - This transaction cannot be accepted. //A valid fingerprint, Transaction Key, or password is required for this transaction.
                    104 - This transaction is currently under review.
                    105 - This transaction is currently under review.
                    106 - This transaction is currently under review.
                    107 - This transaction is currently under review.
                    108 - This transaction is currently under review.
                    109 - This transaction is currently under review.
                    110 - This transaction is currently under review.
                    116 - The authentication indicator is invalid. //This error is only applicable to Verified by Visa and MasterCard SecureCode transactions. The ECI value for a Visa transaction; or the UCAF indicator for a MasterCard transaction submitted in the x_authentication_indicator field is invalid.
                    117 - The cardholder authentication value is invalid. //This error is only applicable to Verified by Visa and MasterCard SecureCode transactions. The CAVV for a Visa transaction; or the AVV/UCAF for a MasterCard transaction is invalid.
                    118 - The combination of authentication indicator and cardholder authentication value is invalid. //This error is only applicable to Verified by Visa and MasterCard SecureCode transactions. The combination of authentication indicator and cardholder authentication value for a Visa or MasterCard transaction is invalid.
                    119 - Transactions having cardholder authentication values cannot be marked as recurring. //This error is only applicable to Verified by Visa and MasterCard SecureCode transactions. Transactions submitted with a value in x_authentication_indicator and x_recurring_billing=YES will be rejected.
                    120 - An error occurred during processing. Please try again. //The system-generated void for the original timed-out transaction failed. (The original transaction timed out while waiting for a response from the authorizer.)
                    121 - An error occurred during processing. Please try again. //The system-generated void for the original errored transaction failed. (The original transaction experienced a database error.)
                    122 - An error occurred during processing. Please try again. //The system-generated void for the original errored transaction failed. (The original transaction experienced a processing error.)
                    123 - This account has not been given the permission(s) required for this request. //The transaction request must include the API Login ID associated with the payment gateway account.
                    128 - This transaction cannot be processed. //The customers financial institution does not currently allow transactions for this account.
                    130 - This payment gateway account has been closed. //IFT: The payment gateway account status is Blacklisted.
                    131 - This transaction cannot be accepted at this time. //IFT: The payment gateway account status is Suspended-STA.
                    132 - This transaction cannot be accepted at this time. //IFT: The payment gateway account status is Suspended-Blacklist.
                    152 - The transaction was authorized, but the client could not be notified; the transaction will not be settled.
                    170 - An error occurred during processing. Please contact the merchant.
                    173 - An error occurred during processing. Please contact the merchant.
                    175 - The processor does not allow voiding of credits.
                    180 - An error occurred during processing. Please try again. //The processor response format is invalid.
                    181 - An error occurred during processing. Please try again. //The system-generated void for the original invalid transaction failed. (The original transaction included an invalid processor response format.)
                    185 - This reason code is reserved or not applicable to this API.
                    243 - Recurring billing is not allowed for this eCheck.Net type. //The combination of values submitted for x_recurring_billing and x_echeck_type is not allowed.
                    244 - This eCheck.Net type is not allowed for this Bank Account Type. //The combination of values submitted for x_bank_acct_type and x_echeck_type is not allowed.
                    245 - This eCheck.Net type is not allowed when using the payment gateway hosted payment form. //The value submitted for x_echeck_type is not allowed when using the payment gateway hosted payment form.
                    246 - This eCheck.Net type is not allowed. //The merchants payment gateway account is not enabled to submit the eCheck.Net type.
                    247 - This eCheck.Net type is not allowed. //The combination of values submitted for x_type and x_echeck_type is not allowed.
                     */
                throw new FailedTransactionException($response->response_reason_text,$response->response_reason_code);
                break;
            // Held for Review
            case 4:
                /* response codes
				193 - The transaction is currently under review. //The transaction was placed under review by the risk management system.
				252 - Your order has been received. Thank you for your business!//The transaction was accepted, but is being held for merchant review. The merchant may customize the customer response in the Merchant Interface.
				253 - Your order has been received. Thank you for your business! //The transaction was accepted and was authorized, but is being held for merchant review. The merchant may customize the customer response in the Merchant Interface.
				*/
                //todo: flag this transaction for review
                $ret_val = true;
                break;
            // Unknown
            default:
                throw new FailedTransactionException('An unknown error has occurred while processing the transaction.',0);
                break;
        }

        return $ret_val;
    }
}