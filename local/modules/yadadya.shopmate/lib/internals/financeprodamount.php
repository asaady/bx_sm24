<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProdAmountTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_prod_amount_log";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'DATE' => array(
				'data_type' => 'datetime',
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
			),
			'AMOUNT' => array(
				'data_type' => 'float',
			),
			'QUANTITY' => array(
				'data_type' => 'float'
			),

			'USER_ID' => array(
				'data_type' => 'integer',
				'default_value' => "",
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'default_value' => "",
			),
			'REQUEST_URI' => array(
				'data_type' => 'string',
				'default_value' => "",
			),
			'DESCRIPTION' => array(
				'data_type' => 'string',
				'default_value' => "",
			)
		);
	}
}