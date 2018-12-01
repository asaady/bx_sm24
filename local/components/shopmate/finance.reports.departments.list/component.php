<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);
use \Yadadya\Shopmate;
use \Bitrix\Main\Type;

$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);

if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";
$FILTER_NAME = $arParams["FILTER_NAME"];
global ${$FILTER_NAME};

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("finance") >= "R" || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
	{
		$bAllowAccess = true;
	}
	else
	{
		$bAllowAccess = false;
	}

	// if user has access
	if ($bAllowAccess)
	{
		$arResult["CAN_EDIT"] = $arParams["ALLOW_EDIT"] == "Y" ? "Y" : "N";
		$arResult["CAN_DELETE"] = $arParams["ALLOW_DELETE"] == "Y" ? "Y" : "N";
		if(!$USER->CanDoOperation('catalog_store'))
			$arResult["CAN_EDIT"] = $arResult["CAN_DELETE"] = "N";

		if ($USER->GetID())
		{
			$arResult["NO_USER"] = "N";

			$arSort = array("NAME", "SALE", "PURCHASE", "PROFIT", "PRICE_PROFIT");
			$sort_field = in_array(strtoupper($_REQUEST["SORT"]), $arSort) ? $_REQUEST["SORT"] : "NAME";
			$sort_order = strtoupper($_REQUEST["ORDER"]) != "DESC" ? "ASC" : "DESC";

			$parameters = array(
				"select" => array("ID", "NAME", "REPORT.*"),
				"order" => array($sort_field => $sort_order)
			);

			$arFilter = array(
				"REPORT.STORE_ID" => SMShops::getUserShop(),
				/*array(
					"LOGIC" => "OR",
					"!SALE" => false, 
					"!PURCHASE" => false
				)*/
			);
			if(!empty(${$FILTER_NAME}))
				foreach (${$FILTER_NAME} as $key => $value) 
					if(!empty($value))
					switch ($key) 
					{
						case "DEPARTMENT":
							if(empty(${$FILTER_NAME}["SECTION"]))
							{
								$arFilter["SECTION_ID"] = $value;
								$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
							}
							break;
						case "SECTION":
							if(empty(${$FILTER_NAME}["SUBSECTION"]))
							{
								$arFilter["SECTION_ID"] = $value;
								$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
							}
							break;
						case "SUBSECTION":
							$arFilter["SECTION_ID"] = $value;
							$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
							break;
						case "DATE_FROM":
							$arFilter[">=REPORT.DATE"] = new Type\DateTime(date("Y-m-d", strtotime($value)), "Y-m-d");
							break;
						case "DATE_TO":
							$arFilter["<REPORT.DATE"] = new Type\DateTime(date("Y-m-d", strtotime($value)+24*60*60), "Y-m-d");
							break;
						case "SEARCH":
							$arFilter[] = array(
								"LOGIC" => "OR",
								"NAME" => str_combos($value),
								"PREVIEW_TEXT" => str_combos($value),
								"DETAIL_TEXT" => str_combos($value),
								"ID" => SMStoreBarcode::getProductsByBarcode($value),
							);
							break;
					}

			$parameters["filter"] = $arFilter;

			$arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
			$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;

			$nav = new \Bitrix\Main\UI\PageNavigation("page");
			$nav->allowAllRecords(true)
				->setPageSize($arParams["NAV_ON_PAGE"])
				->initFromUri();

			$parameters["count_total"] = true;
			$parameters["offset"] = $nav->getOffset();
			$parameters["limit"] = $nav->getLimit();
			$result = Shopmate\FinanceReport::getElements($parameters);

			$nav->setRecordCount($result->getCount());

			$productFilter = array();

			while($fields = $result->fetch())
			{
				$fields["SALE"] = floatval($fields["SALE"]);
				$fields["PURCHASE"] = floatval($fields["PURCHASE"]);
				$fields["PROFIT"] = floatval($fields["PROFIT"]);
				$fields["PRICE_PROFIT"] = floatval($fields["PRICE_PROFIT"]);
				$productFilter[] = $fields["ID"];
				$fields["SALE_PREV"] = 0;
				$fields["PURCHASE_PREV"] = 0;
				$fields["PROFIT_PREV"] = 0;
				$fields["PRICE_PROFIT_PREV"] = 0;

				$arElements[$fields["ID"]] = $fields;
			}
			if(!empty($productFilter))
			{
				unset($parameters["count_total"], $parameters["offset"], $parameters["limit"]);
				$parameters["filter"]["ID"] = $productFilter;
				if(!empty(${$FILTER_NAME}["DATE_FROM"]))
					$parameters["filter"][">=REPORT.DATE"] = new Type\DateTime(date("Y-m-d", (date("Y", strtotime(${$FILTER_NAME}["DATE_FROM"]))-1).date("-m-d 00:00:00", strtotime(${$FILTER_NAME}["DATE_FROM"]))), "Y-m-d");
				if(!empty(${$FILTER_NAME}["DATE_TO"]))
					$parameters["filter"]["<REPORT.DATE"] = new Type\DateTime(date("Y-m-d", (date("Y", strtotime(${$FILTER_NAME}["DATE_TO"])+24*60*60)-1).date("-m-d 00:00:00", strtotime(${$FILTER_NAME}["DATE_TO"])+24*60*60)), "Y-m-d");
				$result = Shopmate\FinanceReport::getElements($parameters);
				while($fields = $result->fetch())
				{
					$arElement = array(
						"SALE_PREV" => floatval($fields["SALE"]),
						"PURCHASE_PREV" => floatval($fields["PURCHASE"]),
						"PROFIT_PREV" => floatval($fields["PROFIT"]),
						"PRICE_PROFIT_PREV" => floatval($fields["PRICE_PROFIT"]),
					);
					$arElements[$fields["ID"]] = array_merge($arElements[$fields["ID"]], $arElement);
				}
			}

			ob_start();
			$APPLICATION->IncludeComponent(
				"bitrix:main.pagenavigation",
				$arParams["PAGER_TEMPLATE"],
				array(
					"NAV_OBJECT" => $nav,
					//"SEF_MODE" => "Y",
				),
				null,
				array('HIDE_ICONS' => 'Y')
			);
			$arResult["NAV_STRING"] = ob_get_contents();
			ob_end_clean();

			$arResult["ELEMENTS"] = array_values($arElements);
		}
		else
		{
			$arResult["NO_USER"] = "Y";
		}

		$arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strIMessage"]);

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}
?>