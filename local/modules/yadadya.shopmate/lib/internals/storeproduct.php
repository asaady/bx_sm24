<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StoreProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_store_product";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
			),
			'PURCHASING_PRICE' => array(
				'data_type' => 'float'
			),
			'PURCHASING_CURRENCY' => array(
				'data_type' => 'string',
				'default_value' => 'RUB',
			),
			'END_DATE' => array(
				'data_type' => 'datetime',
			),
		);
	}
}