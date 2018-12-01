<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class Cash extends Base
{
	protected static $currentFields = array("ID", "ACCOUNT_NUMBER", "USER_NAME", "USER_PHONE", "PRICE", "SUM_NOPAID", "DATE", "STATUS_NAME");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array(
		"DATE_FROM" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"DATE_TO" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"ACCOUNT_NUMBER" => array(
		),
		"DEBT" => array(
			"PROPERTY_TYPE" => "L",
			"MULTIPLE" => "N",
			"LIST_TYPE" => "C",
			"USER_TYPE" => "checkbox",
			"ENUM" => array("Y" => ""),
		),
		"DEDUCTED" => array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "Y",
		),
	);
	protected static $propList = array(
		"PRODUCT" => array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"NUM_LIST" => "Y",
			"PROPERTY_LIST" => array(
				"PRODUCT_ID" => array(
				),
				"AMOUNT" => array(
					"READONLY" => "Y",
				),
				"QUANTITY" => array(
				),
				"PRICE" => array(
					"VERIFICATION" => "float",
					"READONLY" => "Y",
				),
				"SUMM" => array(
					"READONLY" => "Y",
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		"USER_ID" => array(
			"REQUIRED" => "Y"
		),
		"PRICE" => array(
			"READONLY" => "Y",
			"PLACEHOLDER" => "0.0",
		),
		"SUM_NOPAID" => array(
			"READONLY" => "Y",
			"PLACEHOLDER" => "0.0",
		),
		"PAID" => array(
			"VERIFICATION" => "float",
			"PLACEHOLDER" => "0.0",
		),
		"DEDUCTED" => array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "Y",
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		$user_perm = parent::GetUserPermission("cash");
		if ($user_perm >= "X")
			$user_perm = "W";
		return $user_perm;
		//return parent::GetUserPermission("cash");
	}*/

	public function resultModifier($arResult)
	{
		/*$arResult["PAY_SYSTEM"] = array();
		$arFilter = array(
			"ACTIVE" => "Y",
			//"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
			"PSA_HAVE_PAYMENT" => "Y"
		);
		$dbPaySystem = \CSalePaySystem::GetList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			$arFilter
		);
		while ($arPaySystem = $dbPaySystem->Fetch())
		{
			$arPaySystem["NAME"] = htmlspecialcharsEx($arPaySystem["NAME"]);
			$arResult["PAY_SYSTEM"][$arPaySystem["ID"]] = $arPaySystem;
		}*/
		return $arResult;
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"USER_NAME" => new Entity\ExpressionField("USER_NAME", 
				"(CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE %s 
				END)", 
				array("CUSER.SMUSER.PERSON_TYPE", "CUSER.WORK_COMPANY", "CUSER.NAME", "CUSER.NAME")
			),
			"USER_PHONE" => "CUSER.WORK_PHONE",
			"SUM_NOPAID" => new Entity\ExpressionField("SUM_NOPAID", 
				"IFNULL(%s, 0) - IFNULL(%s, 0)", 
				array("PRICE", "SUM_PAID")
			),
			"DATE" => "DATE_INSERT",
			"STATUS_NAME" => new Entity\ExpressionField("STATUS_NAME", 
				"(CASE %s 
					WHEN \"0\" 
						THEN \"".Loc::getMessage("STATUS_NAME_PAYED")."\"
					ELSE (CASE %s 
						WHEN \"Y\" 
							THEN \"".Loc::getMessage("STATUS_NAME_NOPAYED")."\"
						ELSE \"".Loc::getMessage("STATUS_NAME_NODEDUCTED")."\" 
					END)
				END)", 
				array("SUM_NOPAID", "DEDUCTED")
			),
			/*"ACCOUNT_NUMBER" => new Entity\ExpressionField("ACCOUNT_NUMBER", 
				"(CASE 
					WHEN (%1\$s = \"\") OR (%1\$s IS NULL)
						THEN %2\$s
					ELSE %1\$s
				END)", 
				array("ACCOUNT_NUMBER", "ID")
			),*/
			"AVG_SUMM" => new Entity\ExpressionField("AVG_SUMM", "AVG(%s)", array('PRICE')),
			"CNT" => new Entity\ExpressionField("CNT", "COUNT(*)"),
		);

		$parameters["select"] = parent::getSelect($parameters["select"], static::$currentFields, $referenceFields);
		if (empty($parameters["filter"]["USER_ID"]) && in_array(self::$arParams["CORPORATE"], array("Y", "N")))
			$parameters["filter"][(self::$arParams["CORPORATE"] != "Y" ? "!" : "")."CUSER.SMUSER.CORPORATE"] = "Y";

		$parameters["count_total"] = false;

		return Shopmate\BitrixInternals\OrderTable::getList($parameters);
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		foreach($filter as $field => $value) 
			if(!empty($value))
			{
				switch($field) 
				{
					case "DATE_FROM":

						$arFilter[] = parent::getDateFilter("DATE", $value);

						break;

					case "DATE_TO":

						$arFilter[] = parent::getDateFilter("DATE", null, $value);

						break;

					case "DEBT":

						//$arFilter["DEBT"] = $value;

						break;

					case "ACCOUNT_NUMBER":

						$arFilter[] = array("LOGIC" => "OR", "ID" => $value, $field => $value);

						break;

					default:

						$arFilter[$field] = $value;

						break;
				}
		}

		return $arFilter;
	}

	public function getByID($primary = 0)
	{
		$parameters = array("filter" => array("ID" => $primary), "select" => array("ID", "PRICE", "PAYED", "CANCELED", "USER_ID", "SUM_NOPAID"));
		$item = self::getList($parameters)->fetch();
		$rsBasket = Shopmate\BitrixInternals\BasketTable::getList(array("filter" => array("ORDER_ID" => $primary)));
		while($arBasket = $rsBasket->fetch())
		{
			$arBasket["SUMM"] = $arBasket["QUANTITY"] * $arBasket["PRICE"];
			$item["PRODUCT"][] = $arBasket;
		}
		return $item;
	}

	/*public static function checkErrors(array $data, array $propList = array(), &$errors, $ignoreRequired = false)
	{
		$errors = (array) $errors;
		$errors = array_merge((array) $errors, parent::checkErrors($data, $propList, $errors, $ignoreRequired));

		return $errors;
	}*/

	public function add(array $data)
	{
		if (!empty($data["nodeducted"]))
			$data["DEDUCTED"] = "N";
		$result = parent::add($data);

		if($result->isSuccess())
		{
			self::update(0, $data, $result);
		}

		return $result;
	}

	public function update($primary, array $data, $result = null)
	{
		if(!($result instanceof \Bitrix\Main\Entity\AddResult)) 
		{
			if(is_object($this)) 
			{
				$this->result->setData(array("ID" => $primary));
				self::prepareResult();
			}
			$data["ID"] = $primary;
			$result = parent::update($primary, $data);
		}

		if($result->isSuccess())
		{
			$data["STORE_ID"] = Shopmate\Shops::getUserShop();
			if ($primary > 0)
			{
				$order = self::getList(array("filter" => array("ID" => $primary), "select" => array("SUM_PAID", "DATE_INSERT")))->fetch();
				$data["DATE"] = $order["DATE_INSERT"];
			}
			else
				$data["SUM_PAID"] = 0;
			$data["SUM_PAID"] += $data["PAID"];
			$data["PAYED"] = $data["SUM_PAID"] >= $data["PRICE"] ? "Y" : "N";
			$log = file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $data["SUM_PAID"]." = ".$data["PRICE"]);
			$data["BASKET_ITEMS"] = $data["PRODUCT"];
			unset($data["PRODUCT"]);
			$primary = Shopmate\Cash::DoSaveOrder($data, $primary);

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}

		return $result;
	}

	public static function redirectAfterSave($to_list = true, \Bitrix\Main\Result $result = null)
	{
		global $APPLICATION;
		/*if(!is_object($result))
			$to_list = true;
*/
		if($to_list)
		{

			$sRedirectUrl = $APPLICATION->GetCurPageParam("", array("edit", "CODE", "successMessage"), $get_index_page=false);

			$sAction = ($result instanceof \Bitrix\Main\Entity\AddResult) ? "ADD" : "UPDATE";
			$sRedirectUrl .= (strpos($sRedirectUrl, "?") === false ? "?" : "&") . "successMessage=" . $sAction;

			LocalRedirect($sRedirectUrl);
			exit();
		}
		else
		{
			parent::redirectAfterSave($to_list, $result);
		}
	}

	public function delete($primary)
	{
		$primary = intval($primary);
		$result = parent::delete($primary);
		if(!$result->isSuccess()) return $result;
		
		//$result = Shopmate\Internals\FinanceMoneyCatTable::delete($primary);

		return $result;
	}


}