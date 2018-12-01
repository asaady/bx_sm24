<?php
namespace Yadadya\Shopmate\Egais;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class EgaisTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_egais";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'STORE_ID' => array(
				'data_type' => 'integer'
			),
			'REPLY_ID' => array(
				'data_type' => 'integer'
			),
			'URL' => array(
				'data_type' => 'string'
			),
			'DOCUMENT' => array(
				'data_type' => 'string'
			),
			'DOCUMENT_NUMBER' => array(
				'data_type' => 'string'
			),
			'XML' => array(
				'data_type' => 'string'
			)
		);
	}
}