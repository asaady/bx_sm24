<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class FabricaProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_fabrica_product";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'autocomplete' => true,
				'primary' => true,
			),
			'PARENT_ID' => array(
				'data_type' => 'integer',
			),

			'PRODUCT_ID' => array(
				'data_type' => 'integer',
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true
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

			'TYPE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),

			'AMOUNT' => array(
				'data_type' => 'float',
				'default_value' => 0,
			),
			'AMOUNT_MEASURE' => array(
				'data_type' => 'integer',
			),

			'FAULT_RATIO' => array(
				'data_type' => 'float',
			),
			'WASTE_RATE' => array(
				'data_type' => 'float',
			),

			'STORE_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'USER_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),

			'QUANTITY' => array(
				'data_type' => 'float',
			),

			'ACTIVE' => array(
				'data_type' => 'string',
				'default_value' => 'Y',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),

			'USER' => new \Bitrix\Main\Entity\ReferenceField(
				'USER',
				'Yadadya\Shopmate\BitrixInternals\User',
				array('=this.USER_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'PRODUCT' => new \Bitrix\Main\Entity\ReferenceField(
				'PRODUCT',
				'Yadadya\Shopmate\BitrixInternals\Product',
				array('=this.PRODUCT_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
		);
	}
}