<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class CashSelf extends Cash
{
	protected static $currentFields = array("ID", "ACCOUNT_NUMBER", "PRICE", "DATE");
	protected static $currentSort = array("ID" => "DESC");
	protected static $propList = array(
		"PRODUCT" => array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"NUM_LIST" => "Y",
			"CLASS_ROW" => "product_block",
			"PROPERTY_LIST" => array(
				"PRODUCT_ID" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "AJAX",
					"REF_ENTITY" => "\\Yadadya\\Shopmate\\Components\\Products",
					"DATA" => ["url" => "", "info_url" => ""],
				),
				"AMOUNT" => array(
					"READONLY" => "Y",
					"DATA" => [
						"info_set" => "CAT_AMOUNT"
					]
				),
				"QUANTITY" => array(
					"DATA" => [
						"info_set" => "CAT_QUANTITY",
						"calc_input" => "QUANTITY",
					]
				),
				"PRICE" => array(
					"VERIFICATION" => "float",
					"READONLY" => "Y",
					"DATA" => [
						"info_set" => "DISCOUNT_PRICE",
						"calc_input" => "PRICE",
					]
				),
				"SUMM" => array(
					"READONLY" => "Y",
					"DATA" => [
						"calc_input" => "SUMM",
					],
					"CLASS" => "calc_summ__elem"
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		"USER_ID" => array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "-1",
		),
		"PRICE" => array(
			"VERIFICATION" => "float",
			"READONLY" => "Y",
			"PLACEHOLDER" => "0.0",
			"CLASS" => "calc_summ__result"
		),
		"PAID" => array(
			"PROPERTY_TYPE" => "H",
			"VERIFICATION" => "float",
			"PLACEHOLDER" => "0.0",
			"CLASS" => "calc_summ__result"
		),
		"DEDUCTED" => array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "Y",
		),
	);

	public function getList(array $parameters = array())
	{
		$parameters["filter"]["USER_ID"] = -1;
		return parent::getList($parameters);
	}

	public function update($primary, array $data, $result = null)
	{
		$data["PAID"] = $data["PRICE"];
		return parent::update($primary, $data, $result);
	}
}