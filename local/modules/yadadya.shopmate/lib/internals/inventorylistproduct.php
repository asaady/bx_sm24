<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class InventoryListProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_inventory_list_product";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'INV_LIST_ID' => array(
				'data_type' => 'integer',
				'required' => true,
			),
			'ITEM_ID' => array(
				'data_type' => 'integer',
			),
			'ITEM_TYPE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new \Bitrix\Main\Entity\Validator\Length(null, 1),
					);
				},
				'values' => array('S', 'P'), // section, product
				'default_value' => 'P',
			),
		);
	}
}