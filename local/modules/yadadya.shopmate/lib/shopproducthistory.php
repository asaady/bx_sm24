<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ShopProductHistoryTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_shop_product_history";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true
			),
			'SHOP_ID' => array(
				'data_type' => 'integer'
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
			),
		);
	}
}