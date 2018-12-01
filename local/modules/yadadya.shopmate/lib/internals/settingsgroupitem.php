<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class SettingsGroupItemTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_settings_group_item";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'ITEM_ID' => array(
				'data_type' => 'integer',
			),
			'ITEM_TYPE' => array(
				'data_type' => 'string',
			),
			'GROUP_ID' => array(
				'data_type' => 'integer',
			),
			'GROUP' => new \Bitrix\Main\Entity\ReferenceField(
				'GROUP',
				'Yadadya\Shopmate\Internals\SettingsGroup',
				array('=this.GROUP_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'USER' => new \Bitrix\Main\Entity\ReferenceField(
				'USER',
				'Yadadya\Shopmate\BitrixInternals\User',
				array('=this.ITEM_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'POSITION' => new \Bitrix\Main\Entity\ReferenceField(
				'POSITION',
				'Yadadya\Shopmate\Internals\PersonalPositionTable',
				array('=this.ITEM_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'DEPARTMENT' => new \Bitrix\Main\Entity\ReferenceField(
				'DEPARTMENT',
				'Yadadya\Shopmate\Internals\PersonalDepartmentTable',
				array('=this.ITEM_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
		);
	}
}
