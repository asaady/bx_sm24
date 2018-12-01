<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class UserOverheadProductTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_user_overhead_product";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'CODE' => array(
				'data_type' => 'string',
				'required' => true
			),
		);
	}

	public static function add(array $data)
	{
		if ($find = self::getList(array("filter" => $data, "select" => array("ID")))->fetch())
			return self::update($find["ID"], $data);
		return parent::add($data);
	}
}
