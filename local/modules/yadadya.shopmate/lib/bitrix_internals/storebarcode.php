<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StoreBarcodeTable extends DataManager
{
	public static function getTableName()
	{
		return "b_catalog_store_barcode";
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
				'data_type' => 'integer'
			),
			'BARCODE' => array(
				'data_type' => 'string'
			),
			'STORE_ID' => array(
				'data_type' => 'integer'
			),
			'ORDER_ID' => array(
				'data_type' => 'integer'
			),
			'DATE_MODIFY' => array(
				'data_type' => 'datetime'
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime'
			),
			'CREATED_BY' => array(
				'data_type' => 'integer'
			),
			'MODIFIED_BY' => array(
				'data_type' => 'integer'
			),
		);
	}
}