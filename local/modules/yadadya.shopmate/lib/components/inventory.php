<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class Inventory extends Base
{
	protected static $currentFields = array("ID", "DATE", "PRODUCTS_CNT", "PRIMARY", "ACTIVE", "COMMENT");
	protected static $currentSort = array("DATE" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"PRODUCTS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"DISABLED_SAVED" => "Y",
			"PROPERTY_LIST" => array(
				"PRODUCT_ID" => array(
				),
				"AMOUNT_PREV" => array(
					"DISABLED" => "Y",
				),
				"AMOUNT" => array(
				),
				"PRICE" => array(
					"DISABLED" => "Y",
				),
				"SUMM" => array(
					"DISABLED" => "Y",
				),
				"COMMENT" => array(
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		"COMMENT" => array(
			"PROPERTY_TYPE" => "T"
		)
	);

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("inventory");
	}*/

	public function getList(array $parameters = array())
	{
		$parameters["filter"]["STORE_ID"] = Shopmate\Shops::getUserShop();

		$q = new Entity\Query(\Yadadya\Shopmate\Internals\InventoryProductTable::getEntity());
		$q->setSelect(array("CNT" => new Entity\ExpressionField('CNT', 'COUNT(*)')));
		$q->setFilter(array("INVENTORY_ID" => new \Bitrix\Main\DB\SqlExpression("`yadadya_shopmate_internals_inventory_product`.`ID`")));
		$subProductsCnt = $q->getQuery();

		$referenceFields = array("PRODUCTS_CNT" =>  new Entity\ExpressionField("PRODUCTS_CNT", "(".$subProductsCnt.")"));
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["count_total"] = false;

		return Shopmate\Internals\InventoryTable::getList($parameters);
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		$arFilter = $filter;

		return $arFilter;
	}

	public function getByID($primary = 0)
	{
		$result = Shopmate\Internals\InventoryTable::getRowById($primary);
		$res = Shopmate\Internals\InventoryProductTable::getList(array("filter" => array("INVENTORY_ID" => $primary)));
		while($row = $res->fetch())
		{
			$row["AMOUNT_PREV"] = $row["AMOUNT"] + $row["DIFFERENCE"];
			$result["PRODUCTS"][] = $row;
		}
		return $result;
	}

	public static function closeInventory($store_id = 0, $invId = 0)
	{
		Shopmate\Inventory::closeInventory(Shopmate\Shops::getUserShop(), $invId);
	}

	public static function searchActiveInventory()
	{
		return Shopmate\Inventory::searchActiveInventory(Shopmate\Shops::getUserShop());
	}

	public static function searchPrimaryInventory()
	{
		return Shopmate\Inventory::searchPrimaryInventory(Shopmate\Shops::getUserShop());
	}

	public function add(array $data)
	{
		return self::update(0, $data);
	}

	public function update($primary, array $data)
	{
					
		$close = !empty($data["submit"]) ? true : false;

		$data = parent::checkFields($data, self::$propList);

		$result = $primary > 0 ? parent::update($primary, $data) : parent::add($data);

		$data["STORE_ID"] = Shopmate\Shops::getUserShop();

		$is_primary = false;
		if($primaryInv = self::searchPrimaryInventory())
		{
			if($primaryInv["ID"] == $primary)
				$is_primary = true;
		}
		else
			$is_primary = true;

		foreach($data["PRODUCTS"] as $key => $arProduct)
		{
			if($arProduct["ID"] > 0 || $arProduct["PRODUCT_ID"] <= 0)
			{
				unset($data["PRODUCTS"][$key]);
				continue;
			}
			if(!$is_primary && $arProduct["AMOUNT"] != $arProduct["AMOUNT_PREV"] && empty($arProduct["COMMENT"]))
				$result->addError(new Entity\EntityError(Loc::getMessage("ERROR_NO_COMMENT")));
		}

		if(empty($data["PRODUCTS"]))
			$result->addError(new Entity\EntityError(Loc::getMessage("ERROR_EMPTY_PRODUCTS")));
		
		$result->setData($data);

		if($result->isSuccess())
		{
			if($close) $data["ACTIVE"] = "N";
			
			$id = Shopmate\Inventory::DoSaveInventory($data, $primary);

			if($id > 0)
			{
				if($primary > 0)
					$result->setPrimary($id);
				else
					$result->setId($id);
			}
			else
				$result->addError(new Entity\EntityError(Loc::getMessage("ERROR_NOT_SAVED")));
		}

		return $result;
	}

	public function resultModifier($arResult)
	{
		if (isset($arResult["ITEM"]) && empty($arResult["ITEM"]["PRODUCTS"]) && !empty($_REQUEST["list"]))
		{
			unset($arResult["PROPERTY_LIST"]["PRODUCTS"]["DISABLED_SAVED"]);
			unset($arResult["PROPERTY_LIST"]["PRODUCTS"]["PROPERTY_LIST"]["PRODUCT_ID"]["DISABLED"]);
			$arResult["ITEM"]["ACTIVE"] = "Y";

			$q = new Entity\Query(\Yadadya\Shopmate\Internals\InventoryListProductTable::getEntity());
			$q->setSelect(["ITEM_ID"]);
			$q->setFilter(["INV_LIST_ID" => $_REQUEST["list"], "ITEM_TYPE" => "P"]);
			$prodIdQuery = $q->getQuery();

			$q = new Entity\Query(\Yadadya\Shopmate\Internals\InventoryListProductTable::getEntity());
			$q->setSelect(["ITEM_ID"]);
			$q->setFilter(["INV_LIST_ID" => $_REQUEST["list"], "ITEM_TYPE" => "S"]);
			$sectionIdQuery = $q->getQuery();

			$res = Products::getList([
				"select" => ["ID", "AMOUNT", "PRICE"],
				"filter" => [
					//"DISABLE_HISTORY" => "Y",
					[
						"LOGIC" => "OR",
						"@ID" => new \Bitrix\Main\DB\SqlExpression($prodIdQuery),
						"@IBLOCK_SECTION_ID" => new \Bitrix\Main\DB\SqlExpression(parent::getIblockSectionFilter(new \Bitrix\Main\DB\SqlExpression($sectionIdQuery)))
					],
				]
			]);
			while ($row = $res->fetch())
			{
				$arResult["ITEM"]["PRODUCTS"][] = [
					"PRODUCT_ID" => $row["ID"],
					"AMOUNT_PREV" => $row["AMOUNT"],
					"AMOUNT" => $row["AMOUNT"],
					"PRICE" => $row["PRICE"],
					"SUMM" => $row["AMOUNT"] * $row["PRICE"],
				];
			}
		}

		return $arResult;
	}
}