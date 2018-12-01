<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class EgaisAlccodeTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_egais_alccode";
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
			'ALCCODE' => array(
				'data_type' => 'string',
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
			),
			'DATE_MODIFY' => array(
				'data_type' => 'datetime',
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime',
			),
			'CREATED_BY' => array(
				'data_type' => 'integer'
			),
			'MODIFIED_BY' => array(
				'data_type' => 'integer',
			),
		);
	}
}