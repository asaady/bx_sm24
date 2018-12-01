<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class FabricaProductConnectTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_fabrica_product_connect";
	}

	public static function getMap()
	{
		return array(
			'PARENT_ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'CONNECT_ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),

			'AMOUNT' => array(
				'data_type' => 'float',
				'default_value' => 0,
			),
			'MEASURE' => array(
				'data_type' => 'integer',
			),
			'MEASURE_FROM' => array(
				'data_type' => 'integer',
			),
			'MEASURE_TO' => array(
				'data_type' => 'integer',
			),

			'WASTE_RATE' => array(
				'data_type' => 'float',
			),
			'AMOUNT_RATIO' => array(
				'data_type' => 'float',
			),
		);
	}
}