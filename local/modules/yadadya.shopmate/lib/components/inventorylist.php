<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class InventoryList extends Base
{
	protected static $currentFields = array("ID", "DATE" , "NAME"/*, "PRODUCTS_CNT"*/);
	protected static $currentSort = array("DATE" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(),
		"SECTIONS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"PROPERTY_LIST" => array(
				"ITEM_ID" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "AJAX",
					"REF_ENTITY" => "\\Yadadya\\Shopmate\\Components\\Section",
					//"DATA" => ["url" => "", "info_url" => ""],
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		"PRODUCTS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"PROPERTY_LIST" => array(
				"ITEM_ID" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "AJAX",
					"REF_ENTITY" => "\\Yadadya\\Shopmate\\Components\\Products",
					//"DATA" => ["url" => "", "info_url" => ""],
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
	);
	protected static $item_types = ["S" => "SECTIONS", "P" => "PRODUCTS"];

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("inventory");
	}

	public function getList(array $parameters = array())
	{
		$parameters["filter"]["STORE_ID"] = Shopmate\Shops::getUserShop();

		$q = new Entity\Query(\Yadadya\Shopmate\Internals\InventoryListProductTable::getEntity());
		$q->setSelect(array("CNT" => new Entity\ExpressionField('CNT', 'COUNT(*)')));
		$q->setFilter(array("INV_LIST_ID" => new \Bitrix\Main\DB\SqlExpression("`yadadya_shopmate_internals_inventory_list_product`.`ID`")));
		$subProductsCnt = $q->getQuery();

		$referenceFields = array("PRODUCTS_CNT" =>  new Entity\ExpressionField("PRODUCTS_CNT", "(".$subProductsCnt.")"));
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["count_total"] = false;

		return Shopmate\Internals\InventoryListTable::getList($parameters);
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
		$result = Shopmate\Internals\InventoryListTable::getRowById($primary);
		$res = Shopmate\Internals\InventoryListProductTable::getList(array("filter" => array("INV_LIST_ID" => $primary)));
		while($row = $res->fetch())
			$result[self::$item_types[$row["ITEM_TYPE"]]][] = $row;
		return $result;
	}

	public function resultModifier($arResult)
	{

		if (isset($arResult["ITEM"]) && empty($arResult["ITEM"]["PRODUCTS"]) && $_REQUEST["filter"] == "last_month")
		{
			$cur_day = date("d");
			$cur_month = date("m");
			$cur_year = date("Y");
			/*$cur_quarter = floor($cur_month/3)*3;
			$cur_quarter = ($cur_quarter < 10 ? "0" : "").$cur_quarter;*/
			$today = $cur_day.".".$cur_month.".".$cur_year;
			$dateFrom = "01.".$cur_month.".".$cur_year;
			$dateTo = $today;

			$res = Shopmate\FinanceReport::getElements([
				"select" => ["ID", "NAME"],
				"filter" => parent::getDateFilter("REPORT.DATE", $dateFrom, $dateTo)
			]);
			while ($row = $res->fetch())
			{
				$arResult["ITEM"]["NAME"] = Loc::getMessage("LAST_MONTH_NAME", ["#DATE_FROM#" => $dateFrom, "#DATE_TO#" => $dateTo]);
				$arResult["ITEM"]["PRODUCTS"][] = ["ITEM_ID" => $row["ID"]];
				$arResult["PROPERTY_LIST"]["PRODUCTS"]["PROPERTY_LIST"]["ITEM_ID"]["ENUM"][$row["ID"]] = ["VALUE" => $row["NAME"]];

			}
		}

		if (isset($arResult["ITEM"]))
		{
			array_splice($arResult["BUTTONS"], 2, 0, [[
				"NAME" => "apply",
				"VALUE" => Loc::getMessage("ADD_INV_BTN"),
				"CLASS" => "btn-link",
			]]);
		}

		return $arResult;
	}

	public function add(array $data)
	{
		return self::update(0, $data);
	}

	public function update($primary, array $data)
	{		
		if(!($result instanceof \Bitrix\Main\Entity\AddResult)) 
		{
			if(is_object($this)) 
			{
				$this->result->setData(array("ID" => $primary));
				self::prepareResult();
			}
			$result = parent::update($primary, $data);
		}

		if($result->isSuccess())
		{
			$items = [];
			foreach (self::$item_types as $item_type) 
			{
				$items[$item_type] = $data[$item_type];
				unset($data[$item_type]);
			}
			unset($data["PRODUCTS"]);
			if ($primary > 0)
				$res = Shopmate\Internals\InventoryListTable::update($primary, $data);
			else
			{
				$data["DATE"] = new \Bitrix\Main\Type\DateTime($data["DATE"]);
				$data["STORE_ID"] = Shopmate\Shops::getUserStore();
				$res = Shopmate\Internals\InventoryListTable::add($data);
				if ($res->isSuccess())
					$primary = $res->GetID();
				else
					$result->addErrors($res->getErrors());
			}

			if ($primary > 0)
			{
				$oitem = new Shopmate\Internals\InventoryListProductTable;
				$last_id = [];
				$current_id = [];
				$res = $oitem->getList([
					"select" => ["ID"],
					"filter" => ["INV_LIST_ID" => $primary]
				]);
				while ($row = $res->fetch())
					$last_id[] = $row["ID"];

				foreach (self::$item_types as $item_type_key => $item_type) 
					foreach ($items[$item_type] as $item) 
						if ($item["ITEM_ID"] > 0)
						{
							$item["INV_LIST_ID"] = $primary;
							$item["ITEM_TYPE"] = $item_type_key;
							if (empty($item["ID"]))
							{
								$res = $oitem->add($item);
								if ($res->isSuccess())
									$item["ID"] = $res->getId();
							}
							else
								$res = $oitem->update($item["ID"], $item);

							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());
							$current_id[] = $item["ID"];
						}

				foreach ($last_id as $sel_id) 
					if (!in_array($sel_id, $current_id))
						$res = $oitem->delete($sel_id);
			}

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));

			if ($result->isSuccess() && $_POST["apply"] == Loc::getMessage("ADD_INV_BTN"))
			{
				if ($activeInventory = Inventory::searchActiveInventory())
					Inventory::closeInventory($activeInventory);
				LocalRedirect("/products/inventory/?edit=Y&list=".$primary);
			}
		}

		return $result;
	}
}