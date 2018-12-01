<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProdPriceTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_prod_price_log";
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
			'CATALOG_GROUP_ID' => array(
				'data_type' => 'integer',
			),
			'PRICE' => array(
				'data_type' => 'float'
			),
			'CURRENCY' => array(
				'data_type' => 'string'
			),
			'USER_ID' => array(
				'data_type' => 'integer',
				'default_value' => null,
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'default_value' => null,
			),
			'REQUEST_URI' => array(
				'data_type' => 'string',
				'default_value' => null,
			),
			'DESCRIPTION' => array(
				'data_type' => 'string',
				'default_value' => null,
			)
		);
	}
}