<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StoreProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_catalog_store_product";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer'
			),
			'AMOUNT' => array(
				'data_type' => 'float'
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
			),
		);
	}
}