<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class NotifyTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_notify";
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
				'data_type' => 'datetime'
			),
			'USER_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'EVENT_TYPE' => array(
				'data_type' => 'string',
			),
			'STORE_TO' => array(
				'data_type' => 'integer',
			),
			'USER_TO' => array(
				'data_type' => 'integer',
			),
			'ITEM_OBJECT' => array(
				'data_type' => 'string',
			),
			'ITEM_ID' => array(
				'data_type' => 'integer',
			),
			'URL' => array(
				'data_type' => 'string',
			),
			'DESCRIPTION' => array(
				'data_type' => 'text',
				'column_type' => 'mediumtext',
			),
			/*'ACTIVE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				},
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
			),*/
			'USER' => new \Bitrix\Main\Entity\ReferenceField(
				'USER',
				'Yadadya\Shopmate\BitrixInternals\User',
				array('=this.USER_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'BEFORE' => array(
				'data_type' => 'text',
			),
			'AFTER' => array(
				'data_type' => 'text',
			),
		);
	}
}
