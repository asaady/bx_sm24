<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_product";
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
				'data_type' => 'integer',
			),
			'SHELF_LIFE' => array(
				'data_type' => 'float'
			),
			'DNC_TYPE_CODE' => array(
				'data_type' => 'integer',
			),
		);
	}
}