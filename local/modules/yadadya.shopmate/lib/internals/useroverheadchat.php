<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class UserOverheadChatTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_user_overhead_chat";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'UO_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'DATE' => array(
				'data_type' => 'datetime'
			),
			'USER_ID' => array(
				'data_type' => 'integer'
			),
			'DESCRIPTION' => array(
				'data_type' => 'text'
			)
		);
	}
}
