<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class Products extends Base
{
	protected static $currentFields = array("ID", "NAME", "AMOUNT", "MEASURE"/*, "PURCHASE_AMOUNT", "SALE_QUANTITY"*/, "PURCHASING_PRICE", "PURCHASING_CURRENCY", "PRICE", "CURRENCY"/*, "MARGINALITY"*/, "END_DATE", "TIMESTAMP_X", "SECT_ID", "SECT_NAME", "SECT_LEFT_MARGIN", "SECT_DEPTH_LEVEL");
	protected static $currentSort = array("TIMESTAMP_X" => "DESC");
	const baseDisable = array(/*"IBLOCK_SECTION", "NAME", "BARCODE", */"MEASURE", "SHELF_LIFE", "DNC_TYPE_CODE", "ALCCODE", "PACK", "COUNTRY", "DETAIL_TEXT");
	protected static $filterList = array(
		/*"ID" => array(
			//"MULTIPLE" => "Y",
		),*/
		"SECTION" => array(
		),
		/*"SUBSECTION" => array(
		),*/
		"STORE_PRODUCT" => array(
			"PROPERTY_TYPE" => "L",
			"ENUM" => array(
				"ALL" => "Products of base and store",
				"BASE" => "Products of base",
				"STORE" => "Products of store",
			)
		),
		/*"OVERHEAD_DATE" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),*/
		"FINANCE_DATE_FROM" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"FINANCE_DATE_TO" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		/*"PACK" => array(
		),*/
		"PRODUCT" => array(
		),
		"CONTRACTOR" => array(
		),
		"PERISHABLE" => array(
			"PROPERTY_TYPE" => "L",
			"MULTIPLE" => "N",
			"LIST_TYPE" => "C",
			"USER_TYPE" => "checkbox",
			"ENUM" => array(1 => "perishable"),
			"STYLE" => array("display" => "inline", "width" => "auto")
		)
	);
	protected static $propList = array(
		"IBLOCK_SECTION" => Array(
			"PROPERTY_TYPE" => "L",
			/*"ENUM" => $arEnum["SECTIONS"]*/
		),
		"NAME" => Array(
			"REQUIRED" => "Y"
		),
		"BARCODE" => Array(
			"MULTIPLE" => "Y"
		),
		"MEASURE" => Array(
			"PROPERTY_TYPE" => "L",
			/*"ENUM" => $arEnum["MEASURE"]*/
		),
		"AMOUNT" => Array(
			"VERIFICATION" => "float"
		),
		"PURCHASING_PRICE" => Array(
			"VERIFICATION" => "float"
		),
		"PRICE" => Array(
			"VERIFICATION" => "float"
		),
		"SHELF_LIFE" => Array(
			"VERIFICATION" => "float"
		),
		"DNC_TYPE_CODE" => Array(
			"PROPERTY_TYPE" => "L",
			/*"ENUM" => array(
				0 => Loc::getMessage("DNC_TYPE_CODE_VALUE_0"),
				1 => Loc::getMessage("DNC_TYPE_CODE_VALUE_1"),
				2 => Loc::getMessage("DNC_TYPE_CODE_VALUE_2"),
				3 => Loc::getMessage("DNC_TYPE_CODE_VALUE_3"),
			)*/
		),
		/*"DEBT" => Array(
		),
		"PAYMENTS" => Array(
		),*/
		"ALCCODE" => Array(
			"MULTIPLE" => "Y"
		),
		/*"PACK" => Array(
		),
		"COUNTRY" => Array(
		),*/
		"DETAIL_TEXT" => Array(
			"PROPERTY_TYPE" => "T"
		),
		"COMMENT" => array(
		)
	);
	public static function getFilterList()
	{
		$filterList = static::$filterList;

		$filterList["FINANCE_DATE_FROM"]["DEFAULT_VALUE"] = "01.".date("m").".".date("Y");
		$filterList["FINANCE_DATE_TO"]["DEFAULT_VALUE"] = date("d").".".date("m").".".date("Y");

		return $filterList;
	}
	public static function getPropList()
	{
		$propList = static::$propList;

		global $USER;
		if($USER->IsAdmin())
			$propList = array("DATE_CREATE" => array("DISABLED" => "Y"), "TIMESTAMP_X" => array("DISABLED" => "Y")) + $propList;

		$arEnum = array();
		$arEnum["SECTIONS"] = self::getSectionsEnum();
		foreach ($arEnum["SECTIONS"] as $sectionId => $arSection)
			$arEnum["SECTIONS"][$sectionId]["VALUE"] = $arSection["DEPTH_LEVEL"] > 0 ? str_repeat(" .", $arSection["DEPTH_LEVEL"])." ".$arSection["VALUE"] : "- ".$arSection["VALUE"];

		$propList["IBLOCK_SECTION"]["ENUM"] = $arEnum["SECTIONS"];
		$propList["MEASURE"]["ENUM"] = Products::getMeasureEnumList();;
		$propList["DNC_TYPE_CODE"]["ENUM"] = array(
			0 => Loc::getMessage("DNC_TYPE_CODE_VALUE_0"),
			1 => Loc::getMessage("DNC_TYPE_CODE_VALUE_1"),
			2 => Loc::getMessage("DNC_TYPE_CODE_VALUE_2"),
			3 => Loc::getMessage("DNC_TYPE_CODE_VALUE_3"),
		);

		return $propList;
	}

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("product");
	}

	public function prepareResult()
	{
		$primary = $this->result->getData()["ID"];
		if($primary > 0)
		{
			$propList = $this->getProps();
			if(self::disableBaseStore($primary))
				self::disableInput($propList, self::baseDisable);
			$propList["COMMENT"]["REQUIRED"] = "Y";
			$this->setPropList($propList);
		}
	}

	public function disableBaseStore($primary = 0)
	{
		global $USER;
		if(!$USER->IsAdmin() && $primary > 0)
		{
			$q = new Entity\Query(\Bitrix\Main\UserGroupTable::getEntity());
			$q->setSelect(array("USER_ID"));
			$q->setFilter(array("GROUP_ID" => 1));
			$res = Iblock\ElementTable::getList(array(
				"select" => array("ID"),
				"filter" => array(
					"ID" => $primary,
					"@CREATED_BY" => new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")")
				)
			));

			if($res->fetch())
				return true;
		}
		return false;
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array("AMOUNT" => "CSTORE_PRODUCT.AMOUNT", "MEASURE" => "CPRODUCT.MEASURE", "PURCHASING_PRICE" => "SMSTORE_PRODUCT.PURCHASING_PRICE", "PURCHASING_CURRENCY" => "SMSTORE_PRODUCT.PURCHASING_CURRENCY", "CATALOG_GROUP_ID" => "CGROUP.ID", "PRICE" => "CPRICE.PRICE", "CURRENCY" => "CPRICE.CURRENCY", "END_DATE" => "SMSTORE_PRODUCT.END_DATE", "SECT_ID" => "SECT.ID", "SECT_NAME" => "SECT.NAME", "SECT_LEFT_MARGIN" => "SECT.LEFT_MARGIN", "SECT_DEPTH_LEVEL" => "SECT.DEPTH_LEVEL", "PURCHASE_AMOUNT" => new Entity\ExpressionField("PURCHASE_AMOUNT", "SUM(%s)", "FINANCE.FIN_PURCHASE_AMOUNT"), "SALE_QUANTITY" => new Entity\ExpressionField("SALE_QUANTITY", "SUM(%s)", "FINANCE.FIN_SALE_QUANTITY"), "MARGINALITY" => new Entity\ExpressionField("MARGINALITY", "%s*100/%s", ["PROD_SHOP_PRICE.AVG_PRICE", "PROD_PURCHASING_PRICE.AVG_PRICE"]));


		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$removeHardFields = ["PURCHASE_AMOUNT", "SALE_QUANTITY", "MARGINALITY"];
		$sel_order = self::getOrder();
		foreach ($parameters["select"] as $sel_field => $sel_val) 
			if (!empty($sel_field) && in_array($sel_field, $removeHardFields) && !array_key_exists($sel_field, $sel_order))
				unset($parameters["select"][$sel_field]);

		$runtimeParams = [];
		foreach ($parameters["select"] as $key_sel => $sel) 
		{
			if (!empty($key_sel) && in_array($key_sel, ["SALE", "SALE_QUANTITY", "PURCHASE", "PURCHASE_AMOUNT", "PROFIT", "PRICE_PROFIT"]))
				$runtimeParams[] = "FINANCE_REPORT";
			if (!empty($key_sel) && in_array($key_sel, ["MARGINALITY"]))
				$runtimeParams[] = "PROD_PRICE";
			elseif (strpos($sel, ".") !== false)
				$runtimeParams[] = strstr($sel, ".", true);
			elseif ($sel == "*")
				$runtimeParams[] = "*";
		}
		$runtimeParams = array_unique($runtimeParams);

		if (empty($parameters["filter"]["ID"]) &&$parameters["filter"]["DISABLE_HISTORY"] != "Y")
		{
			$parameters["runtime"][] = new Entity\ReferenceField(
				'HISTORY',
				'Yadadya\Shopmate\Internals\ShopProductHistory',
				array('=ref.PRODUCT_ID' => 'this.ID', 'ref.SHOP_ID' => new DB\SqlExpression(\Yadadya\Shopmate\Shops::getUserStore())),
				array('join_type' => 'INNER')
			);
		}
		unset($parameters["filter"]["DISABLE_HISTORY"]);

		if (in_array("CPRODUCT", $runtimeParams) || in_array("*", $runtimeParams))
			$parameters["runtime"][] = new Entity\ReferenceField(
				'CPRODUCT',
				'Bitrix\Catalog\Product',
				array('=ref.ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);
		if (in_array("CSTORE_PRODUCT", $runtimeParams) || in_array("*", $runtimeParams))
			$parameters["runtime"][] = new Entity\ReferenceField(
				'CSTORE_PRODUCT',
				'Yadadya\Shopmate\BitrixInternals\StoreProduct',
				array('=ref.PRODUCT_ID' => 'this.ID', 'ref.STORE_ID' => new DB\SqlExpression('?i', Shopmate\Shops::getUserShop())),
				array('join_type' => 'LEFT')
			);
		if (in_array("SMSTORE_PRODUCT", $runtimeParams) || in_array("*", $runtimeParams))
			$parameters["runtime"][] = new Entity\ReferenceField(
				'SMSTORE_PRODUCT',
				'Yadadya\Shopmate\Internals\StoreProduct',
				array('=ref.PRODUCT_ID' => 'this.ID', 'ref.STORE_ID' => new DB\SqlExpression('?i', Shopmate\Shops::getUserShop())),
				array('join_type' => 'LEFT')
			);
		if (in_array("CPRICE", $runtimeParams) || in_array("CGROUP", $runtimeParams) || in_array("*", $runtimeParams))
		{
			$parameters["runtime"][] = new Entity\ReferenceField(
				'CGROUP',
				'Bitrix\Catalog\Group',
				array('=ref.XML_ID' => new DB\SqlExpression('CONCAT("SHOP_ID_", ?i)', Shopmate\Shops::getUserShop())),
				array('join_type' => 'LEFT')
			);
			$parameters["runtime"][] = new Entity\ReferenceField(
				'CPRICE',
				'Bitrix\Catalog\Price',
				array(
					'=ref.PRODUCT_ID' => 'this.ID', 
					'=ref.CATALOG_GROUP_ID' => new DB\SqlExpression('`iblock_element_cgroup`.`ID`'),
					'!=ref.QUANTITY_FROM' => new DB\SqlExpression('1'),
					'!=ref.QUANTITY_TO' => new DB\SqlExpression('1'),
				),
				array('join_type' => 'LEFT')
			);
		}
		if (in_array("SECT", $runtimeParams) || in_array("*", $runtimeParams))
			$parameters["runtime"][] = new Entity\ReferenceField(
				'SECT',
				'Yadadya\Shopmate\BitrixInternals\SectionTable',
				array('=ref.ID' => 'this.IBLOCK_SECTION_ID'),
				array('join_type' => 'LEFT')
			);

		if (in_array("FINANCE_REPORT", $runtimeParams) || in_array("*", $runtimeParams))
		{
			$q = new Entity\Query(\Yadadya\Shopmate\Internals\ReportTable::getEntity());
			$q->setSelect(array(
				"PRODUCT_ID", 
				"FIN_SALE_QUANTITY" => new Entity\ExpressionField(
					"FIN_SALE_QUANTITY", 
					"SUM(%s)",
					"SALE_QUANTITY"
				),
				"FIN_PURCHASE_AMOUNT" => new Entity\ExpressionField(
					"FIN_PURCHASE_AMOUNT", 
					"SUM(%s)",
					"PURCHASE_AMOUNT"
				),
			));
			$dateFilter = [];
			$dateFilter = parent::getDateFilter("DATE", $parameters["filter"]["FINANCE.DATE_FROM"], $parameters["filter"]["FINANCE.DATE_TO"]);
			$q->setFilter(array_merge(["STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore()], $dateFilter));
			$q->setGroup("PRODUCT_ID");
			$parameters["runtime"][] = new Entity\ReferenceField(
				'FINANCE',
				Base::getSqlFieldClass($q->getQuery(), array("PRODUCT_ID" => array("data_type" => "integer", "primary" => "true"), "FIN_SALE_QUANTITY" => array("data_type" => "float"), "FIN_PURCHASE_AMOUNT" => array("data_type" => "float")), "ProdFinance"),
				array('=ref.PRODUCT_ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);
		}

		if (in_array("PROD_PRICE", $runtimeParams) || in_array("PROD_SHOP_PRICE", $runtimeParams) || in_array("*", $runtimeParams))
		{
			$q = new Entity\Query(\Yadadya\Shopmate\Internals\ProdPriceTable::getEntity());
			$q->setSelect(array(
				"PRODUCT_ID", 
				"AVG_PRICE" => new Entity\ExpressionField(
					"AVG_PRICE", 
					"AVG(%s)",
					"PRICE"
				),
			));
			$dateFilter = [];
			$dateFilter = parent::getDateFilter("DATE", $parameters["filter"]["FINANCE.DATE_FROM"], $parameters["filter"]["FINANCE.DATE_TO"]);
			$q->setFilter(array_merge(["STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore(), '>CATALOG_GROUP_ID' => 0], $dateFilter));
			$q->setGroup("PRODUCT_ID");
			$parameters["runtime"][] = new Entity\ReferenceField(
				'PROD_SHOP_PRICE',
				Base::getSqlFieldClass($q->getQuery(), array("PRODUCT_ID" => array("data_type" => "integer", "primary" => "true"), "AVG_PRICE" => array("data_type" => "float")), "ProdShopPrice"),
				array('=ref.PRODUCT_ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);
		}

		if (in_array("PROD_PRICE", $runtimeParams) || in_array("PROD_PURCHASING_PRICE", $runtimeParams) || in_array("*", $runtimeParams))
		{
			$q = new Entity\Query(\Yadadya\Shopmate\Internals\ProdPriceTable::getEntity());
			$q->setSelect(array(
				"PRODUCT_ID", 
				"AVG_PRICE" => new Entity\ExpressionField(
					"AVG_PRICE", 
					"AVG(%s)",
					"PRICE"
				),
			));
			$dateFilter = [];
			$dateFilter = parent::getDateFilter("DATE", $parameters["filter"]["FINANCE.DATE_FROM"], $parameters["filter"]["FINANCE.DATE_TO"]);
			$q->setFilter(array_merge(["STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore(), '<=CATALOG_GROUP_ID' => 0], $dateFilter));
			$q->setGroup("PRODUCT_ID");
			$parameters["runtime"][] = new Entity\ReferenceField(
				'PROD_PURCHASING_PRICE',
				Base::getSqlFieldClass($q->getQuery(), array("PRODUCT_ID" => array("data_type" => "integer", "primary" => "true"), "AVG_PRICE" => array("data_type" => "float")), "ProdPurchasingPrice"),
				array('=ref.PRODUCT_ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);
		}

		unset($parameters["filter"]["FINANCE.DATE_FROM"], $parameters["filter"]["FINANCE.DATE_TO"]);

		$parameters["filter"]["IBLOCK_ID"] = is_object($this) ? $this->getCatalogID() : self::getCatalogID();

		if (is_array($parameters["order"]))
			$parameters["order"] = array_merge(["SECT_LEFT_MARGIN" => "ASC"], $parameters["order"]);

		$parameters["count_total"] = false;

		return Iblock\ElementTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (is_array($arResult["ITEMS"]))
		{
			$prodId = [];
			foreach ($arResult["ITEMS"] as $arItem) 
				$prodId[] = $arItem["ID"];
			if (!empty($prodId))
			{
				$isset_fields = [
					"PURCHASE_AMOUNT" => array_key_exists("PURCHASE_AMOUNT", $arResult["SORTS"]),
					"SALE_QUANTITY" => array_key_exists("SALE_QUANTITY", $arResult["SORTS"]),
					"MARGINALITY" => array_key_exists("MARGINALITY", $arResult["SORTS"]),
				];
				$dateFilter = [];
				$dateFilter = parent::getDateFilter("DATE", $arResult["FILTER"]["FINANCE.DATE_FROM"], $arResult["FILTER"]["FINANCE.DATE_TO"]);
				if ($isset_fields["PURCHASE_AMOUNT"] || $isset_fields["SALE_QUANTITY"])
				{
					$financeReport = [];
					$select = ["PRODUCT_ID"];
					if ($isset_fields["PURCHASE_AMOUNT"])
						$select["FIN_PURCHASE_AMOUNT"] = new Entity\ExpressionField(
								"FIN_PURCHASE_AMOUNT", 
								"SUM(%s)",
								"PURCHASE_AMOUNT"
							);
					if ($isset_fields["SALE_QUANTITY"])
						$select["FIN_SALE_QUANTITY"] = new Entity\ExpressionField(
								"FIN_SALE_QUANTITY", 
								"SUM(%s)",
								"SALE_QUANTITY"
							);
					$res = \Yadadya\Shopmate\Internals\ReportTable::getList([
						"select" => $select,
						"filter" => array_merge(["PRODUCT_ID" => $prodId, "STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore()], $dateFilter),
					]);
					while ($row = $res->fetch()) 
						$financeReport[$row["PRODUCT_ID"]] = $row;

					foreach ($arResult["ITEMS"] as &$arItem) 
						if (!empty($financeReport[$arItem["ID"]]))
						{
							if ($isset_fields["PURCHASE_AMOUNT"])
								$arItem["PURCHASE_AMOUNT"] = $financeReport[$arItem["ID"]]["FIN_PURCHASE_AMOUNT"];
							if ($isset_fields["SALE_QUANTITY"])
								$arItem["SALE_QUANTITY"] = $financeReport[$arItem["ID"]]["FIN_SALE_QUANTITY"];
						}
				}

				if ($isset_fields["MARGINALITY"])
				{
					$avgPurchasingPrice = [];
					$res = \Yadadya\Shopmate\Internals\ProdPriceTable::getList([
						"select" => [
							"PRODUCT_ID", 
							"AVG_PRICE" => new Entity\ExpressionField(
								"AVG_PRICE", 
								"AVG(%s)",
								"PRICE"
							)
						],
						"filter" => array_merge(["STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore(), "<=CATALOG_GROUP_ID" => 0], $dateFilter),
					]);
					while ($row = $res->fetch()) 
						$avgPurchasingPrice[$row["PRODUCT_ID"]] = $row;
					foreach ($arResult["ITEMS"] as &$arItem) 
						if (!empty($avgPurchasingPrice[$arItem["ID"]]))
							$arItem["AVG_PURCHASING_PRICE"] = $avgPurchasingPrice[$arItem["ID"]]["AVG_PRICE"];

					$avgShopPrice = [];
					$res = \Yadadya\Shopmate\Internals\ProdPriceTable::getList([
						"select" => [
							"PRODUCT_ID", 
							"AVG_PRICE" => new Entity\ExpressionField(
								"AVG_PRICE", 
								"AVG(%s)",
								"PRICE"
							)
						],
						"filter" => array_merge(["STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore(), ">CATALOG_GROUP_ID" => 0], $dateFilter),
					]);
					while ($row = $res->fetch()) 
						$avgShopPrice[$row["PRODUCT_ID"]] = $row;
					foreach ($arResult["ITEMS"] as &$arItem) 
						if (!empty($avgShopPrice[$arItem["ID"]]))
							$arItem["AVG_SHOP_PRICE"] = $avgShopPrice[$arItem["ID"]]["AVG_PRICE"];

					foreach ($arResult["ITEMS"] as &$arItem) 
						if (!empty($arItem["AVG_PURCHASING_PRICE"]))
							$arItem["MARGINALITY"] = $arItem["AVG_SHOP_PRICE"] * 100 / $arItem["AVG_PURCHASING_PRICE"];
				}
			}
		}
		return $arResult;
	}

	public function getByID($primary = 0)
	{
		$result = self::getList(array(
			"select" => array("ID", "NAME", "DATE_CREATE", "TIMESTAMP_X", "IBLOCK_SECTION_ID", "DETAIL_TEXT", "SHELF_LIFE" => "SMPRODUCT.SHELF_LIFE", "DNC_TYPE_CODE" => "SMPRODUCT.DNC_TYPE_CODE", "MEASURE", "AMOUNT", "PURCHASING_PRICE", "PRICE"),
			"filter" => array("ID" => $primary),
			"runtime" => array(
				new Entity\ReferenceField(
					'SMPRODUCT',
					'Yadadya\Shopmate\Internals\Product',
					array('=ref.PRODUCT_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				)
			)
		))->fetch();

		$result["IBLOCK_SECTION"] = $result["IBLOCK_SECTION_ID"];

		$res = \Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::GetList(array(
			"select" => array("ID", "BARCODE"),
			"filter" => array("PRODUCT_ID" => $primary)
		));
		while($row = $res->fetch())
			$result["BARCODE"][] = $row["BARCODE"];

		$res = \Yadadya\Shopmate\Internals\EgaisAlccodeTable::GetList(array(
			"select" => array("ID", "ALCCODE"),
			"filter" => array("PRODUCT_ID" => $primary)
		));
		while($row = $res->fetch())
			$result["ALCCODE"][] = $row["ALCCODE"];

		if(is_object($this)) $this->result->setData(array("ID" => $primary));

		return $result;
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);
		
		$filterList = self::getFilterList();
		if (!isset($_REQUEST["FINANCE_DATE_FROM"]) && !isset($_REQUEST["FINANCE_DATE_TO"]))
			foreach (["FINANCE_DATE_FROM", "FINANCE_DATE_TO"] as $overhead_date)
				$filter[$overhead_date] = $filterList[$overhead_date]["DEFAULT_VALUE"];

		foreach($filter as $field => $value) 
			if(!empty($value))
			{
				switch($field) 
				{
					case "SECTION":
					case "SUBSECTION":

						$arFilter["@IBLOCK_SECTION_ID"] = new \Bitrix\Main\DB\SqlExpression("(".parent::getIblockSectionFilter($value).")");

						break;


					case "PACK":

						$arFilter[]["@ID"] = new \Bitrix\Main\DB\SqlExpression("(".parent::getIblockPropertyFilter("PACK", $value).")");

						break;


					case "OVERHEAD_DATE":
					case "FINANCE_DATE_FROM":

						$arFilter["FINANCE.DATE_FROM"] = $value;

						break;

					case "FINANCE_DATE_TO":

						$arFilter["FINANCE.DATE_TO"] = $value;

						break;
					case "CONTRACTOR":
					case "PERISHABLE":

						break;


					case "STORE_PRODUCT":

						$q = new Entity\Query(\Bitrix\Main\UserGroupTable::getEntity());
						$q->setSelect(array("USER_ID"));
						if(is_array($value)) $value = array_shift($value);
						$groups = array_merge($value == "BASE" ? array(1) : array(), $value == "STORE" ? array(Shopmate\Shops::getUserGroup()) : array());
						if(!empty($groups))
							$q->setFilter(array("GROUP_ID" => $groups));
						$arFilter["@CREATED_BY"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");

						break;

					case "PRODUCT_SEARCH":

						if(is_array($value)) $value = array_shift($value);
						$arFilter["NAME"] = parent::getSearchComboFilter($value);

						break;

					case "PRODUCT":

						if(is_array($value)) $value = array_shift($value);
						$arFilter["ID"] = $value;

						break;


					default:

						$arFilter[$field] = $value;

						break;
				}
		}
		if(!empty($filter["OVERHEAD_DATE"]) || !empty($filter["FINANCE_DATE_FROM"]) || !empty($filter["FINANCE_DATE_TO"]) || !empty($filter["CONTRACTOR"]) || !empty($filter["PERISHABLE"]))
		{
			$q = new Entity\Query(\Yadadya\Shopmate\BitrixInternals\StoreDocsElementTable::getEntity());
			$q->setSelect(array("ELEMENT_ID"));
			$q->registerRuntimeField("DOC",
				new Entity\ReferenceField(
					"DOC",
					"Yadadya\Shopmate\BitrixInternals\StoreDocs",
					array(
						'ref.ID' => 'this.DOC_ID',
						'ref.DOC_TYPE' => new DB\SqlExpression('?s', 'A'),
						'ref.STATUS' => new DB\SqlExpression('?s', 'Y'),
					),
					array("join_type" => "LEFT")
				)
			);
			if(is_array($filter["OVERHEAD_DATE"])) $filter["OVERHEAD_DATE"] = array_shift($filter["OVERHEAD_DATE"]);
			if(!empty($filter["OVERHEAD_DATE"]))
				$q->addFilter(null, parent::getDateFilter("DOC.DATE_DOCUMENT", $filter["OVERHEAD_DATE"], $filter["OVERHEAD_DATE"]));
			/*if (!empty($filter["FINANCE_DATE_FROM"]) || !empty($filter["FINANCE_DATE_TO"]))
				$q->addFilter(null, parent::getDateFilter("DOC.DATE_DOCUMENT", $filter["FINANCE_DATE_FROM"], $filter["FINANCE_DATE_TO"]));*/
			if(!empty($filter["CONTRACTOR"])) $q->addFilter(null, array("DOC.CONTRACTOR_ID" => $filter["CONTRACTOR"]));
			if(!empty($filter["PERISHABLE"]))
			{
				$q->registerRuntimeField("SMDOC_ELEMENT",
					new Entity\ReferenceField(
						"SMDOC_ELEMENT",
						"Yadadya\Shopmate\Internals\StoreDocsElement",
						array('ref.DOCS_ELEMENT_ID' => 'this.ID'),
						array("join_type" => "LEFT")
					)
				);
				$q->addFilter(null, array("!SMDOC_ELEMENT.END_DATE" => false));
			}
			$arFilter[]["@ID"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");
		}

		return $arFilter;
	}

	public static function getEnumByID(array $elementsID = array())
	{
		return self::getEnumList(array("ID" => $elementsID));
	}

	public static function getSectionsEnum($max_level = 0)
	{
		$max_level = intval($max_level);
		if($max_level <= 0) $max_level = 5;
		$result = Iblock\SectionTable::getList(array(
			"select" => array("ID", "IBLOCK_SECTION_ID", "NAME", "DEPTH_LEVEL"),
			"filter" => array(
				"IBLOCK_ID" => is_object($this) ? $this->getCatalogID() : self::getCatalogID(),
				"DEPTH_LEVEL" => range(1, $max_level),
				"ACTIVE" => "Y",
				"GLOBAL_ACTIVE" => "Y"
			),
			"order" => array("LEFT_MARGIN" => "ASC")
		));
		while ($row = $result->fetch())
			if($row["IBLOCK_SECTION_ID"] > 0)
				$arEnum[$row["ID"]] = array("VALUE" => $row["NAME"], "DEPTH_LEVEL" => $row["DEPTH_LEVEL"], "SECTION_ID" => $row["IBLOCK_SECTION_ID"]);
			else
				$arEnum[$row["ID"]] = array("VALUE" => $row["NAME"]);
		return $arEnum;
	}

	public function add(array $data)
	{
		$result = parent::add($data);

		if($result->isSuccess())
		{
			self::update(0, $data, $result);
		}

		return $result;
	}

	public function update($primary, array $data, $result = null)
	{
		global $USER;
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
			$tableFields = array(
				"ELEMENT" => array("IBLOCK_SECTION", "NAME", "DETAIL_TEXT"),
				"PRODUCT" => array("MEASURE"),
				"SMPRODUCT" => array("SHELF_LIFE", "DNC_TYPE_CODE"),
				"STORE_PRODUCT" => array("AMOUNT"),
				"STORE_BARCODE" => array("BARCODE"),
				"EGAIS_ALCCODE" => array("ALCCODE"),
				"PRICE" => array("PRICE", "CURRENCY"),
				"SMSTORE_PRODUCT" => array("PURCHASING_PRICE", "PURCHASING_CURRENCY", "END_DATE"),
			);
			foreach ($tableFields as $table => $fields) 
			{
				$tData = array();
				foreach ($fields as $field) 
					if(isset($data[$field]))
						$tData[$field] = $data[$field];
				if ($table != "ELEMENT" && empty($primary)) break;
				if(!empty($tData))
					switch ($table) 
					{
						case "ELEMENT":

							$tobj = new \CIBlockElement;
							$tData["MODIFIED_BY"] = $USER->GetID();
							if($primary > 0)
							{
								$res = $tobj->Update($primary, $tData);
								if(!$res) 
									$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
							}
							else
							{
								$tData["IBLOCK_ID"] = self::getCatalogID();
								$primary = $tobj->Add($tData);
								if(!$primary)
									$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
							}

							break;

						case "PRODUCT":

							$res = \Bitrix\Catalog\ProductTable::GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"ID" => $primary,
								)
							));
							if($row = $res->fetch())
								$res = \Bitrix\Catalog\ProductTable::update($row["ID"], $tData);
							else
							{
								$tData["ID"] = $primary;
								$res = \Bitrix\Catalog\ProductTable::add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

							break;

						case "SMPRODUCT":

							$res = \Yadadya\Shopmate\Internals\ProductTable::GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"PRODUCT_ID" => $primary,
								)
							));
							if($row = $res->fetch())
								$res = \Yadadya\Shopmate\Internals\ProductTable::update($row["ID"], $tData);
							else
							{
								$tData["PRODUCT_ID"] = $primary;
								$res = \Yadadya\Shopmate\Internals\ProductTable::add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

							break;

						case "STORE_PRODUCT":

							$tData["PRODUCT_ID"] = $primary;
							$tData["STORE_ID"] = Shopmate\Shops::getUserShop();
							\CCatalogStoreProduct::UpdateFromForm($tData);

							/*$res = \Yadadya\Shopmate\BitrixInternals\StoreProductTable::GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"PRODUCT_ID" => $primary,
									"STORE_ID" => Shopmate\Shops::getUserShop()
								)
							));
							if($row = $res->fetch())
								$res = \Yadadya\Shopmate\BitrixInternals\StoreProductTable::update($row["ID"], $tData);
							else
							{
								$tData["PRODUCT_ID"] = $primary;
								$tData["STORE_ID"] = Shopmate\Shops::getUserShop();
								$res = \Yadadya\Shopmate\BitrixInternals\StoreProductTable::add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());*/

							break;

						case "PRICE":

							$tData["PRICE"] = floatval($tData["PRICE"]);
							$res = \Bitrix\Catalog\PriceTable::GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"PRODUCT_ID" => $primary,
									"CATALOG_GROUP_ID" => Shopmate\Shops::getUserPrice()
								)
							));
							if($row = $res->fetch())
							{
								if(empty($tData["PRICE_SCALE"])) $tData["PRICE_SCALE"] = $tData["PRICE"];
								$res = \Bitrix\Catalog\PriceTable::update($row["ID"], $tData);
							}
							else
							{
								$tData["PRODUCT_ID"] = $primary;
								$tData["CATALOG_GROUP_ID"] = Shopmate\Shops::getUserPrice();
								if(empty($tData["CURRENCY"])) $tData["CURRENCY"] = "RUB";
								if(empty($tData["PRICE_SCALE"])) $tData["PRICE_SCALE"] = $tData["PRICE"];
								$res = \Bitrix\Catalog\PriceTable::add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

							break;

						case "SMSTORE_PRODUCT":

							$res = Shopmate\Internals\StoreProductTable::GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"PRODUCT_ID" => $primary,
									"STORE_ID" => Shopmate\Shops::getUserShop()
								)
							));
							if($row = $res->fetch())
								$res = Shopmate\Internals\StoreProductTable::update($row["ID"], $tData);
							else
							{
								$tData["PRODUCT_ID"] = $primary;
								$tData["STORE_ID"] = Shopmate\Shops::getUserShop();
								$res = Shopmate\Internals\StoreProductTable::add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

							break;

						case "STORE_BARCODE":

							$isset_barcodes = array();
							$res = \Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::GetList(array(
								"select" => array("ID", "BARCODE"),
								"filter" => array(
									"PRODUCT_ID" => $primary,
								)
							));
							while($row = $res->fetch())
								if(!empty($row["BARCODE"]) && in_array($row["BARCODE"], $tData["BARCODE"]))
									$isset_barcodes[] = $row["BARCODE"];
								else
									\Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::delete($row["ID"]);
							foreach ($tData["BARCODE"] as $barcode) 
								if(!empty($barcode) && !in_array($barcode, $isset_barcodes))
								{
									$bcData = array(
										"PRODUCT_ID" => $primary,
										"BARCODE" => $barcode
									);
									try
									{
										$res = \Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::add($bcData);
										if(!$res->isSuccess())
											$result->addErrors($res->getErrors());
									}
									catch (\Bitrix\Main\SystemException $e)
									{
										$res = \Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::GetList(array(
											"select" => array("ID", "PRODUCT_ID", "PRODUCT_NAME" => "ELEMENT.NAME", "BARCODE"),
											"filter" => array(
												"BARCODE" => $barcode,
											),
											"runtime" => array(
												new Entity\ReferenceField(
													'ELEMENT',
													'Bitrix\Iblock\Element',
													array('=this.PRODUCT_ID' => 'ref.ID'),
													array('join_type' => 'LEFT')
												)
											)
										));
										if($row = $res->fetch())
										{
											if($row["PRODUCT_ID"] != $primary)
												$result->addError(new Entity\EntityError(Loc::getMessage("ERROR_BARCODE_ISSET", array("#BARCODE#" => $barcode, "#PRODUCT#" => "[".$row["PRODUCT_ID"]."] ".$row["PRODUCT_NAME"]))));
										}
									}
								}

							break;

						case "EGAIS_ALCCODE":

							$isset_alccodes = array();
							$res = \Yadadya\Shopmate\Internals\EgaisAlccodeTable::GetList(array(
								"select" => array("ID", "ALCCODE"),
								"filter" => array(
									"PRODUCT_ID" => $primary,
								)
							));
							while($row = $res->fetch())
								if(!empty($row["ALCCODE"]) && in_array($row["ALCCODE"], $tData["ALCCODE"]))
									$isset_alccodes[] = $row["ALCCODE"];
								else
									\Yadadya\Shopmate\Internals\EgaisAlccodeTable::delete($row["ID"]);
							foreach ($tData["ALCCODE"] as $alccode) 
								if(!empty($alccode) && !in_array($alccode, $isset_alccodes))
								{
									$bcData = array(
										"PRODUCT_ID" => $primary,
										"ALCCODE" => $alccode
									);
									try
									{
										$res = \Yadadya\Shopmate\Internals\EgaisAlccodeTable::add($bcData);
										if(!$res->isSuccess())
											$result->addErrors($res->getErrors());
									}
									catch (\Bitrix\Main\SystemException $e)
									{
										$res = \Yadadya\Shopmate\Internals\EgaisAlccodeTable::GetList(array(
											"select" => array("ID", "PRODUCT_ID", "PRODUCT_NAME" => "ELEMENT.NAME", "ALCCODE"),
											"filter" => array(
												"ALCCODE" => $alccode,
											),
											"runtime" => array(
												new Entity\ReferenceField(
													'ELEMENT',
													'Bitrix\Iblock\Element',
													array('=this.PRODUCT_ID' => 'ref.ID'),
													array('join_type' => 'LEFT')
												)
											)
										));
										if($row = $res->fetch())
										{
											if($row["PRODUCT_ID"] != $primary)
												$result->addError(new Entity\EntityError(Loc::getMessage("ERROR_ALCCODE_ISSET", array("#ALCCODE#" => $alccode, "#PRODUCT#" => "[".$row["PRODUCT_ID"]."] ".$row["PRODUCT_NAME"]))));
										}
									}
								}

							break;
					}
			}
			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}
		
		return $result;
	}

	public function delete($primary)
	{
		$result = parent::delete($primary);
		if(!$result->isSuccess()) return $result;
		
		$result = Shopmate\Internals\ShopProductHistoryTable::delete(["PRODUCT_ID" => $primary, "SHOP_ID" => \Yadadya\Shopmate\Shops::getUserStore()]);

		return $result;
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();

		$filter["IBLOCK_ID"] = self::getCatalogID();

		if(isset($filter["SEARCH"]))
		{
			$q = $filter["SEARCH"];
			$wbp = Shopmate\Products::getProductsByBarcode($q);

			if(empty($wbp))
				$filter["NAME"] = "%".$q."%";
			else
				$filter[] = array(
					"LOGIC" => "OR",
					array("NAME" => "%".$q."%"),
					array("ID" => $wbp),
				);
		}
		unset($filter["SEARCH"]);
		$qBarcode = new Entity\Query(\Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::getEntity());
		$qBarcode->setSelect(array(new Entity\ExpressionField("BARCODES", "GROUP_CONCAT(%s SEPARATOR ', ')", array("BARCODE"))));
		$qBarcode->setFilter(array("PRODUCT_ID" => new \Bitrix\Main\DB\SqlExpression("`iblock_element`.`ID`")));

		$parameters = array(
			"select" => array("ID", "NAME", "BARCODES"),
			"filter" => $filter,
			"runtime" => array(
				new Entity\ExpressionField("BARCODES", "(".$qBarcode->getQuery().")"),
				new Entity\ReferenceField(
					'SMPRODUCT',
					'Yadadya\Shopmate\Internals\Product',
					array('=ref.PRODUCT_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				)
			),
			"limit" => empty($q) ? 0 : 10
		);

		$result = Iblock\ElementTable::GetList($parameters);
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["NAME"]." [".$row["BARCODES"]."]";
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}

	public static function getMeasureEnumList($full_format = false)
	{
		$arResult = array();
		$oMeasure = \CCatalogMeasure::GetList(array("IS_DEFAULT" => "DESC"), array(), false, false, array("ID", $full_format ? "MEASURE_TITLE" : "SYMBOL_RUS", "IS_DEFAULT"));
		while($arMeasure = $oMeasure->Fetch())
			$arResult[$arMeasure["ID"]] = array("VALUE" => $arMeasure[$full_format ? "MEASURE_TITLE" : "SYMBOL_RUS"], "DEFAULT" => "Y");
		return $arResult;
	}

	public static function getInfo($filter = array(), $json = false, $select = array())
	{
		$arResult = array();

		if (!is_array($filter))
			$filter = array("ID" => $filter);

		$defaultSelect = array("ID", "PURCHASING_PRICE", "PRICE", "DISCOUNT_PRICE", "CAT_AMOUNT", "CAT_MEASURE", "CAT_QUANTITY", "START_DATE", "NDS", "SHELF_LIFE");

		if (empty($select))
			$select = $defaultSelect;
		else
			foreach ($select as $key => $value) 
				if (!in_array($value, $defaultSelect))
					unset($select[$key]);

		if (in_array("DISCOUNT_PRICE", $select))
		{
			if (!in_array("PRICE", $select))
				$select[] = "PRICE";
			$select[] = "CURRENCY";
			$select[] = "CATALOG_GROUP_ID";
		}

		if (in_array("CAT_QUANTITY", $select) && Shopmate\Products::isWBarcode($BARCODE) && !in_array("CAT_MEASURE", $select))
			$select[] = "CAT_MEASURE";

		$ID = intval($filter["ID"]);
		$USER_ID = intval($filter["USER_ID"]);
		$BARCODE = $filter["BARCODE"];
		$BASKET_ID = intval($filter["BASKET_ID"]);
		$SHOP_ID = Shopmate\Shops::getUserShop();

		if($USER_ID > 0)
		{
			if (in_array("NDS", $select) && $arContractor = Shopmate\Internals\ContractorTable::getList(array("select" => array("ID", "CONTRACTOR_ID", "NDS"), "filter" => array("CONTRACTOR_ID" => $USER_ID)))->Fetch())
				$arResult["NDS"] = floatval($arContractor["NDS"]);
		}

		$referenceFields = array(
			"CAT_AMOUNT" => "CSTORE_PRODUCT.AMOUNT", 
			"CAT_MEASURE" => "CPRODUCT.MEASURE", 
			"SHELF_LIFE" => "SMPRODUCT.SHELF_LIFE", 
			"PURCHASING_PRICE" => "SMSTORE_PRODUCT.PURCHASING_PRICE", 
			"PURCHASING_CURRENCY" => "SMSTORE_PRODUCT.PURCHASING_CURRENCY", 
			"CATALOG_GROUP_ID" => "MIN_PRICE.CATALOG_GROUP_ID", 
			"PRICE" => new Entity\ExpressionField("PRICE", "MIN(DISTINCT %s)", array("MIN_PRICE.PRICE")), 
			"DISCOUNT_PRICE" => new Entity\ExpressionField("DISCOUNT_PRICE", "MIN(DISTINCT %s)", array("MIN_PRICE.PRICE")),
			"CURRENCY" => "MIN_PRICE.CURRENCY", 
			"END_DATE" => "SMSTORE_PRODUCT.END_DATE",
			"CAT_QUANTITY" => new Entity\ExpressionField("CAT_QUANTITY", "\"1\""),
			"START_DATE" => new Entity\ExpressionField("START_DATE", "\"".ConvertTimeStamp(time(), "SHORT")."\""),
			"NDS" => new Entity\ExpressionField("NDS", "\"".$arResult["NDS"]."\""),
		);

		$parameters = array(
			"select" => parent::getSelect($select, $defaultSelect, $referenceFields),
			"filter" => array("ID" => $ID, "MIN_PRICE.CATALOG_GROUP_ID" => \Yadadya\Shopmate\Shops::getUserPriceGroups($USER_ID)),
			"runtime" => array(
				new Entity\ReferenceField(
					'SMPRODUCT',
					'Yadadya\Shopmate\Internals\Product',
					array('=ref.PRODUCT_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				),
				new Entity\ReferenceField(
					'MIN_PRICE',
					'Yadadya\Shopmate\BitrixInternals\Price',
					array(
						'=ref.PRODUCT_ID' => 'this.ID',
					),
					array('join_type' => 'LEFT')
				)
			)
		);
		$res = self::getList($parameters);
		while ($row = $res->fetch())
			$arResult = $row;
		
		if(in_array($ID, Shopmate\Products::getUndefinedProducts()))
		{
			$arResult["PRICE"] = 0;
			$arResult["DISCOUNT_PRICE"] = 0;
			$arResult["CAT_AMOUNT"] = 0;
		}
		elseif ($arResult["DISCOUNT_PRICE"] > 0)
		{
			if ($USER_ID <= 0)
				$USER_ID = Shopmate\User::SimpleBuyerAdd();

			$IBLOCK_ID = is_object($this) ? $this->getCatalogID() : self::getCatalogID();

			$arUserGroups = array();

			$res = \CUser::GetUserGroupList(empty($USER_ID) ? false : $USER_ID);
			while ($arGroup = $res->Fetch())
				$arUserGroups[] = $arGroup["GROUP_ID"];

			\CCatalogDiscountSave::Disable();
			$arDiscounts = \CCatalogDiscount::GetDiscount($ID, $IBLOCK_ID, $arResult["CATALOG_GROUP_ID"], $arUserGroups);
			\CCatalogDiscountSave::Enable();
			$arResult["DISCOUNT_PRICE"] = empty($arDiscounts) ? $arResult['DISCOUNT_PRICE'] : \CCatalogProduct::CountPriceWithDiscount($arResult["DISCOUNT_PRICE"], $arResult["CURRENCY"], $arDiscounts);
		}

		if (in_array("CAT_QUANTITY", $select))
		{
			if (Shopmate\Products::isWBarcode($BARCODE) && ($arResult["CAT_MEASURE"] == 3 || $arResult["CAT_MEASURE"] == 4))
			{
				$weight = floatval(substr($BARCODE, -6, -1));
				if($arResult["CAT_MEASURE"] == 4) $weight = $weight / 1000;
				$arResult["CAT_QUANTITY"] = $weight;
				/*$BARCODE_WEIGHT = substr_replace($BARCODE, "00000_", -6);
				$rsBarCode = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $ID, "BARCODE" => $BARCODE_WEIGHT), false, false, array("BARCODE"));
				if($arBarCode = $rsBarCode->Fetch())	
					if($arBarCode["BARCODE"] == $BARCODE_WEIGHT)
						$arResult["CAT_QUANTITY"] = $weight;*/
			}
		}

		if($json)
			return json_encode($arResult);

		return $arResult;
	}

	public static function getVatEnumList()
	{
		$arResult = array();
		$res = \Bitrix\Catalog\VatTable::getList(array(
			"select" => array("ID", "NAME", "RATE"),
			"order" => array("SORT" => "ASC"),
			"filter" => array("ACTIVE" => "Y")
		));
		while($row = $res->Fetch())
			$arResult[floatval($row["RATE"])] = floatval($row["RATE"])."%";
		return $arResult;
	}

	public function onProlog()
	{
		if (!empty($_REQUEST["ITEM_CHECK"]))
		{
			$invList = [];
			foreach ($_REQUEST["ITEM_CHECK"] as $item_id => $check)
				$invList["PRODUCTS"][] = ["PRODUCT_ID" => $item_id];
			$inv = new InventoryList;
			$res = $inv->save(0, $invList);
			if ($res->isSuccess())
				LocalRedirect("/products/inventory/list/?edit=Y&CODE=".$res->getId());

		}
		return true;
	}
}