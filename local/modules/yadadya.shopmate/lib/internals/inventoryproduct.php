<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class InventoryProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_inventory_product";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'INVENTORY_ID' => array(
				'data_type' => 'integer',
				'required' => true,
			),
			'DATE' => array(
				'data_type' => 'datetime',
				'required' => true
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
			),
			'AMOUNT' => array(
				'data_type' => 'float'
			),
			'DIFFERENCE' => array(
				'data_type' => 'float'
			),
			'COMMENT' => array(
				'data_type' => 'text',
			),
		);
	}
}