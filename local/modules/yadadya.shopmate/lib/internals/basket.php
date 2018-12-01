<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class BasketTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_basket";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
			),
			'PURCHASING_PRICE' => array(
				'data_type' => 'float'
			),
			'PURCHASING_CURRENCY' => array(
				'data_type' => 'string',
				'default_value' => 'RUB',
			),
			'QUANTITY_FIRST' => array(
				'data_type' => 'float',
			),

		);
	}
}