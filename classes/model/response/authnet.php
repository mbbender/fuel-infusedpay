<?php
namespace InfusedPay;

class Model_Response_Authnet extends \Orm\Model
{

    protected static $_belongs_to = array('transaction');

	protected static $_properties = array(
		'id',
		'x_response_code',
		'x_response_reason_code',
		'x_response_reason_text',
		'x_auth_code',
		'x_avs_code',
		'x_trans_id',
		'x_invoice_num',
		'x_description',
		'x_amount',
		'x_method',
		'x_type',
		'x_account_number',
		'x_card_type',
		'x_split_tender_id',
		'x_prepaid_requested_amount',
		'x_prepaid_balance_on_card',
		'x_cust_id',
		'x_first_name',
		'x_last_name',
		'x_company',
		'x_address',
		'x_city',
		'x_state',
		'x_zip',
		'x_country',
		'x_phone',
		'x_email',
		'x_ship_to_first_name',
		'x_ship_to_last_name',
		'x_ship_to_company',
		'x_ship_to_address',
		'x_ship_to_city',
		'x_ship_to_state',
		'x_ship_to_zip',
		'x_ship_to_country',
		'x_tax',
		'x_duty',
		'x_freight',
		'x_tax_exempt',
		'x_po_num',
		'x_MD5_Hash',
		'x_cvv2_resp_code',
		'x_cavv_response',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('x_response_code', 'X Response Code', 'valid_string[numeric]');
		$val->add_field('x_response_reason_code', 'X Response Reason Code', 'valid_string[numeric]');
		$val->add_field('x_response_reason_text', 'X Response Reason Text', 'max_length[255]');
		$val->add_field('x_auth_code', 'X Auth Code', 'max_length[255]');
		$val->add_field('x_avs_code', 'X Avs Code', 'max_length[255]');
		$val->add_field('x_trans_id', 'X Trans Id', 'max_length[255]');
		$val->add_field('x_invoice_num', 'X Invoice Num', 'max_length[255]');
		$val->add_field('x_description', 'X Description', 'max_length[255]');
		$val->add_field('x_method', 'X Method', 'max_length[255]');
		$val->add_field('x_type', 'X Type', 'max_length[255]');
		$val->add_field('x_account_number', 'X Account Number', 'max_length[255]');
		$val->add_field('x_card_type', 'X Card Type', 'max_length[255]');
		$val->add_field('x_split_tender_id', 'X Split Tender Id', 'max_length[255]');
		$val->add_field('x_cust_id', 'X Cust Id', 'max_length[255]');
		$val->add_field('x_first_name', 'X First Name', 'max_length[255]');
		$val->add_field('x_last_name', 'X Last Name', 'max_length[255]');
		$val->add_field('x_company', 'X Company', 'max_length[255]');
		$val->add_field('x_address', 'X Address', 'max_length[255]');
		$val->add_field('x_city', 'X City', 'max_length[255]');
		$val->add_field('x_state', 'X State', 'max_length[255]');
		$val->add_field('x_zip', 'X Zip', 'max_length[255]');
		$val->add_field('x_country', 'X Country', 'max_length[255]');
		$val->add_field('x_phone', 'X Phone', 'max_length[255]');
		$val->add_field('x_email', 'X Email', 'max_length[255]');
		$val->add_field('x_ship_to_first_name', 'X Ship To First Name', 'max_length[255]');
		$val->add_field('x_ship_to_last_name', 'X Ship To Last Name', 'max_length[255]');
		$val->add_field('x_ship_to_company', 'X Ship To Company', 'max_length[255]');
		$val->add_field('x_ship_to_address', 'X Ship To Address', 'max_length[255]');
		$val->add_field('x_ship_to_city', 'X Ship To City', 'max_length[255]');
		$val->add_field('x_ship_to_state', 'X Ship To State', 'max_length[255]');
		$val->add_field('x_ship_to_zip', 'X Ship To Zip', 'max_length[255]');
		$val->add_field('x_ship_to_country', 'X Ship To Country', 'max_length[255]');
		$val->add_field('x_po_num', 'X Po Num', 'max_length[255]');
		$val->add_field('x_MD5_Hash', 'X MD5 Hash', 'max_length[255]');
		$val->add_field('x_cvv2_resp_code', 'X Cvv2 Resp Code', 'max_length[255]');
		$val->add_field('x_cavv_response', 'X Cavv Response', 'max_length[255]');

		return $val;
	}

}
