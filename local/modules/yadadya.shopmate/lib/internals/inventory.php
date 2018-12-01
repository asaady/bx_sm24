<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class InventoryTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_inventory";
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
			'COMMENT' => array(
				'data_type' => 'text',
			),
			'PRIMARY' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => "N"
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => "Y"
			),
		);
	}
}