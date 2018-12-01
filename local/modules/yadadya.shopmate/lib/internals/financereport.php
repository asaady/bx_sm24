<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class ReportTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_report";
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
			'PRODUCT_ID' => array(
				'data_type' => 'integer'
			),
			'STORE_ID' => array(
				'data_type' => 'integer'
			),
			'SALE' => array(
				'data_type' => 'float'
			),
			'SALE_QUANTITY' => array(
				'data_type' => 'float'
			),
			'PURCHASE' => array(
				'data_type' => 'float'
			),
			'PURCHASE_AMOUNT' => array(
				'data_type' => 'float'
			),
			'PROFIT' => array(
				'data_type' => 'float'
			),
			'PRICE_PROFIT' => array(
				'data_type' => 'float'
			),

		);
	}
}
