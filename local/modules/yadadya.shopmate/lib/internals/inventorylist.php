<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class InventoryListTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_inventory_list";
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
				'required' => true
			),
			'NAME' => array(
				'data_type' => 'string',
			),
		);
	}
}