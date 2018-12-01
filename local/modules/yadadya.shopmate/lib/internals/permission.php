<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class PermissionTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_permission";
	}

	public static function getMap()
	{
		return array(
			'CHAPTER_ID' => array(
				'data_type' => 'string',
				'primary' => true,
				'required' => true,
			),
			'PERMISSION_ID' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 50),
					);
				},
				'required' => true
			),
			'GROUP_ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'required' => true
			),
		);
	}
}
