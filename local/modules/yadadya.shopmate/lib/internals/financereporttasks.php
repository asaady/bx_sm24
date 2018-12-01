<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Type;

class ReportTasksTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_report_tasks";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(//future change md5(DATE, PRODUCT_ID, STORE_ID)
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
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => 'N',
			),
			'SALE_QUANTITY' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => 'N',
			),
			'PURCHASE' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => 'N',
			),
			'PURCHASE_AMOUNT' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => 'N',
			),
			'PROFIT' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => 'N',
			),
			'PRICE_PROFIT' => array(
				'data_type' => 'boolean',
				'values' => array('N','Y'),
				'default_value' => 'N',
			),
		);
	}
}