<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceMoneyCatTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_money_cat";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'OUTGO' => array(
				'data_type' => 'string',
				'values' => array('report', 'payroll'),
			),
			'VALUE' => array(
				'data_type' => 'string',
			),
		);
	}
}
