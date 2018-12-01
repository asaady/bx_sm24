<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class FabricaPartProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_fabrica_part_product";
	}

	public static function getMap()
	{
		return array(
			'PART_ID' => array(
				'data_type' => 'integer',
				'primary' => true,
			),
			'FABRICA_PROD_ID' => array(
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
			'FABRICA_PROD' => new \Bitrix\Main\Entity\ReferenceField(
				'FABRICA_PROD',
				'Yadadya\Shopmate\Internals\FabricaProductTable',
				array('=this.FABRICA_PROD_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
		);
	}
}