<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class FabricaPartTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_fabrica_part";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'autocomplete' => true,
				'primary' => true,
			),
			'NAME' => array(
				'data_type' => 'string'
			),
			'DATE' => array(
				'data_type' => 'datetime'
			),
			'COMMENT' => array(
				'data_type' => 'text',
			),
		);
	}
}