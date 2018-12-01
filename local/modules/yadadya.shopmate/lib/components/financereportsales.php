<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\Internals;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;

Loc::loadMessages(__FILE__);

class FinanceReportSales extends Base
{
	protected static $currentFields = array("ID");
	protected static $sectionsFields = array("ID", "SECTIONS_NAME", "SALE_QUANTITY", "SALE", "PROFIT");
	protected static $elementsFields = array("ID", "ELEMENTS_NAME", "SALE_QUANTITY", "SALE", "PURCHASING_PRICE", "STORE_PRICE", "PROFIT");
	protected static $basketFields = array("ID", "ORDER_ID", "ORDER_NAME", "DATE", "SUMMARY_PRICE", "HAS_DISCOUNT", "IS_CANCEL");
	protected static $orderFields = array("ID", "BASKET_NAME", "SUMMARY_PRICE", "HAS_DISCOUNT", "CANCELED_COUNT");
	protected static $currentSort = array("PROFIT" => "DESC");
	protected static $filterList = array(
		"DATE_FROM" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"DATE_TO" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"SECTION" => array(
			"PROPERTY_TYPE" => "H",
		),
		"ELEMENT" => array(
			"PROPERTY_TYPE" => "H",
		),
		"ORDER" => array(
			"PROPERTY_TYPE" => "H",
		),
	);

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("financereport");
	}

	public function resultModifier($arResult)
	{
		global $listType;

		$arResult["LIST_TYPE"] = strtoupper($listType);

		if (!empty($listType))
			$arResult["SORTS"] = Base::getOrderList(self::${$listType."Fields"});

		return $arResult;
	}

	protected static function paramsByType(array $parameters = array())
	{
		global $listType;

		$referenceFields = array(strtoupper($listType)."_NAME" => "NAME");
		$parameters["select"] = parent::getSelect($parameters["select"], self::${$listType."Fields"}, $referenceFields);

		foreach ($parameters["order"] as $sort => $order) 
			if (!in_array($sort, self::${$listType."Fields"}))
				unset($parameters["order"][$sort]);
		if (empty($parameters["order"]))
			$parameters["order"] = self::$currentSort;

		return $parameters;
	}

	public function getList(array $parameters = array())
	{
		global $listType;

		$dateFrom = $parameters["filter"]["DATE_FROM"];
		$dateTo = $parameters["filter"]["DATE_TO"];
		unset($parameters["filter"]["DATE_FROM"], $parameters["filter"]["DATE_TO"]);
		$saveParameters = $parameters;

		if (!empty($parameters["filter"]["ORDER_ID"]))
		{
			$listType = "order";

			foreach ($parameters["order"] as $sort => $order) 
				if (!in_array($sort, self::${$listType."Fields"}))
					unset($parameters["order"][$sort]);
			if (empty($parameters["order"]))
				$parameters["order"] = array("ID" => "ASC");

			$parameters = self::paramsByType($parameters);

			$referenceFields = array(
				"BASKET_NAME" => "ELEMENT.NAME", 
				"DATE" => "ORDER.DATE_INSERT",
				"HAS_DISCOUNT" => new Entity\ExpressionField("HAS_DISCOUNT", 
					"(CASE
						WHEN (%1\$s = 0) OR (%1\$s IS NULL)
							THEN \"".Loc::getMessage("HAS_DISCOUNT_NO")."\"
						ELSE %1\$s 
					END)", 
					array("DISCOUNT_VALUE")
				),
				"CANCELED_COUNT" => new Entity\ExpressionField("CANCELED_COUNT", 
					"(CASE %1\$s 
						WHEN \"Y\"
							THEN %2\$s
						ELSE (IFNULL(%3\$s, %2\$s) - IFNULL(%2\$s, 0))
					END)", 
					array("ORDER.CANCELED", "QUANTITY", "SMBASKET.QUANTITY_FIRST")
				),
			);
			$parameters["select"] = parent::getSelect($parameters["select"], self::${$listType."Fields"}, $referenceFields);

			$parameters["runtime"][] = new Entity\ReferenceField(
				'SMBASKET',
				'Yadadya\Shopmate\Internals\Basket',
				array('=ref.ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);
			$parameters["runtime"][] = new Entity\ReferenceField(
				'ORDER',
				'Yadadya\Shopmate\BitrixInternals\Order',
				array('=ref.ID' => 'this.ORDER_ID'),
				array('join_type' => 'LEFT')
			);
			$parameters["runtime"][] = new Entity\ReferenceField(
				'ELEMENT',
				'Yadadya\Shopmate\BitrixInternals\Element',
				array('=ref.ID' => 'this.PRODUCT_ID'),
				array('join_type' => 'LEFT')
			);
			$parameters["filter"]["ORDER.STORE_ID"] = \Yadadya\Shopmate\Shops::getUserStore();

			$result = Shopmate\BitrixInternals\BasketTable::getList($parameters);
			//if ($result->getSelectedRowsCount() > 0)
				return $result;
			unset($saveParameters["filter"]["ORDER_ID"]);
		}

		if (!empty($parameters["filter"]["PRODUCT_ID"]))
		{
			$listType = "basket";

			foreach ($parameters["order"] as $sort => $order) 
				if (!in_array($sort, self::${$listType."Fields"}))
					unset($parameters["order"][$sort]);
			if (empty($parameters["order"]))
				$parameters["order"] = array("DATE" => "DESC");

			$parameters = self::paramsByType($parameters);

			//$parameters["filter"][] = parent::getDateFilter("ORDER.DATE_INSERT", $dateFrom, $dateTo);
			$parameters["filter"] = array_merge($parameters["filter"], parent::getDateFilter("DATE", $dateFrom, $dateTo));

			$referenceFields = array(
				"ORDER_NAME" => new Entity\ExpressionField("ORDER_NAME", 
					"(CASE
						WHEN (%1\$s = 0) OR (%1\$s IS NULL)
							THEN %2\$s
						ELSE %1\$s 
					END)", 
					array("ORDER.ACCOUNT_NUMBER", "ORDER.ID")
				),
				"DATE" => "ORDER.DATE_INSERT",
				"HAS_DISCOUNT" => new Entity\ExpressionField("HAS_DISCOUNT", 
					"(CASE
						WHEN (%1\$s = 0) OR (%1\$s IS NULL)
							THEN \"".Loc::getMessage("HAS_DISCOUNT_NO")."\"
						ELSE %1\$s 
					END)", 
					array("DISCOUNT_VALUE")
				),
				"IS_CANCEL" => new Entity\ExpressionField("IS_CANCEL", 
					"(CASE %s 
						WHEN \"Y\"
							THEN \"".Loc::getMessage("IS_CANCEL_YES")."\"
						ELSE \"".Loc::getMessage("IS_CANCEL_NO")."\"
					END)", 
					array("ORDER.CANCELED")
				),
			);
			$parameters["select"] = parent::getSelect($parameters["select"], self::${$listType."Fields"}, $referenceFields);

			$parameters["runtime"][] = new Entity\ReferenceField(
				'ORDER',
				'Yadadya\Shopmate\BitrixInternals\Order',
				array('=ref.ID' => 'this.ORDER_ID'),
				array('join_type' => 'LEFT')
			);
			$parameters["filter"]["ORDER.STORE_ID"] = \Yadadya\Shopmate\Shops::getUserStore();

			$result = Shopmate\BitrixInternals\BasketTable::getList($parameters);
			//if ($result->getSelectedRowsCount() > 0)
				return $result;
			unset($saveParameters["filter"]["PRODUCT_ID"]);
		}

		$listType = "sections";

		$parameters = $saveParameters;
		$parameters = self::paramsByType($parameters);

		$parameters["filter"]["REPORT.STORE_ID"] = Shopmate\Shops::getUserShop();
		if (empty($parameters["filter"]["DEPTH_LEVEL"])) 
			$parameters["filter"]["DEPTH_LEVEL"] = 1;

		//$parameters["filter"][] = parent::getDateFilter("REPORT.DATE", $dateFrom, $dateTo);
		$parameters["filter"] = array_merge($parameters["filter"], parent::getDateFilter("REPORT.DATE", $dateFrom, $dateTo));
		unset($parameters["offset"], $parameters["limit"]);

		$result = Shopmate\FinanceReport::getSections($parameters);
		if ($result->getSelectedRowsCount() > 0)
			return $result;
		else
		{
			$listType = "elements";

			$parameters = $saveParameters;
			$parameters = self::paramsByType($parameters);

			unset($parameters["filter"]["DEPTH_LEVEL"]);

			//$prodPriceFilter = parent::getDateFilter("DATE", $dateFrom, $dateTo);
			$prodPriceFilter["STORE_ID"] = \Yadadya\Shopmate\Shops::getUserStore();

			/*$q = new \Bitrix\Main\Entity\Query(Internals\ProdPriceTable::getEntity());
			$q->setSelect(array("PRODUCT_ID", "STORE_ID", "CATALOG_GROUP_ID", "PRICE_AVG" => new Entity\ExpressionField("PRICE_AVG", "AVG(PRICE)")));
			$q->setFilter(array_merge($prodPriceFilter, array("CATALOG_GROUP_ID" => 0, ">PRICE" => 0)));
			$parameters["runtime"][] = new Entity\ReferenceField(
				'AVG_PURCHASING_PRICE',
				Base::getSqlFieldClass($q->getQuery(), array("PRODUCT_ID" => array("data_type" => "integer", "primary" => "true"), "PRICE_AVG" => array("data_type" => "float", "primary" => "true")), "FinanceProdAvgPurchasingPrice"),
				array('=ref.PRODUCT_ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);
			
			$q = new \Bitrix\Main\Entity\Query(Internals\ProdPriceTable::getEntity());
			$q->setSelect(array("PRODUCT_ID", "STORE_ID", "CATALOG_GROUP_ID", "PRICE_AVG" => new Entity\ExpressionField("PRICE_AVG", "AVG(PRICE)")));
			$q->setFilter(array_merge($prodPriceFilter, array("CATALOG_GROUP_ID" => \Yadadya\Shopmate\Shops::getUserPrice())));
			$parameters["runtime"][] = new Entity\ReferenceField(
				'AVG_STORE_PRICE',
				Base::getSqlFieldClass($q->getQuery(), array("PRODUCT_ID" => array("data_type" => "integer", "primary" => "true"), "PRICE_AVG" => array("data_type" => "float", "primary" => "true")), "FinanceProdAvgStorePrice"),
				array('=ref.PRODUCT_ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			);

			$referenceFields = array("PURCHASING_PRICE" => "AVG_PURCHASING_PRICE.PRICE_AVG", "STORE_PRICE" => "AVG_STORE_PRICE.PRICE_AVG");*/
			$referenceFields = array(
				"PURCHASING_PRICE" => new Entity\ExpressionField("PURCHASING_PRICE", 
					"(%s-%s)/%s", 
					array("SALE", "PROFIT", "SALE_QUANTITY")
				),
				"STORE_PRICE" => new Entity\ExpressionField("STORE_PRICE", 
					"%s/%s", 
					array("SALE", "SALE_QUANTITY")
				),
			);
			$parameters["select"] = parent::getSelect($parameters["select"], self::${$listType."Fields"}, $referenceFields);
			$parameters["filter"] = array_merge($parameters["filter"], parent::getDateFilter("REPORT.DATE", $dateFrom, $dateTo));
			//$parameters["filter"][] = parent::getDateFilter("REPORT.DATE", $dateFrom, $dateTo);

			$parameters["count_total"] = false;

			return Shopmate\FinanceReport::getElements($parameters);
		}
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		if (!empty($filter["ELEMENT"]))
			unset($filter["SECTION"]);
		if (!empty($filter["ORDER"]))
			unset($filter["ELEMENT"]);

		foreach($filter as $field => $value) 
			if(!empty($value))
			{
				switch($field) 
				{
					case "SECTION":

						if(empty($filter["SUBSECTION"]))
						{
							$arFilter["IBLOCK_SECTION_ID"] = $value;
							$arFilter["DEPTH_LEVEL"] = 2;
						}

						break;
					case "ELEMENT":

						$arFilter["PRODUCT_ID"] = $value;

						break;

					case "ORDER":

						$arFilter["ORDER_ID"] = $value;

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
		return array();
	}
}