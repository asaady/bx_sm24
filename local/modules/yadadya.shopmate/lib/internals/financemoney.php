<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceMoneyTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_money";
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
				'data_type' => 'datetime',
				'default_value' => new Type\DateTime(),
				'default' => 'NULL',
			),
			'STORE_ID' => array(
				'data_type' => 'integer'
			),
			'TYPE' => array(
				'data_type' => 'string',
				'values' => array('cash', 'clearing'),
			),
			'OUTGO' => array(
				'data_type' => 'string',
				'values' => array('report', 'contractor', 'payroll', 'deposit', 'cash'),
			),
			'ITEM_TYPE' => array(
				'data_type' => 'string', //overhead, payroll_type
			),
			'ITEM_ID' => array(
				'data_type' => 'string', //overhead_contractor, payroll_type_user
			),
			'USER_ID' => array(
				'data_type' => 'integer'
			),
			'DESCRIPTION' => array(
				'data_type' => 'text'
			),
			'DESCRIPTION_FILE' => array(
				'data_type' => 'integer'
			),
			'PRICE' => array(
				'data_type' => 'float'
			),
			'PRICE_GRAY' => array(
				'data_type' => 'float'
			),
			'CURRENCY' => array(
				'data_type' => 'string',
			),
		);
	}
}
