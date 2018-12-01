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

if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";
$FILTER_NAME = $arParams["FILTER_NAME"];
global ${$FILTER_NAME};

if($componentTemplate == "cooperative") $arParams["PREV_YEAR"] = "Y";

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
				"select" => array("ID", "NAME", "DEPTH_LEVEL", "REPORT.*"),
				"order" => array($sort_field => $sort_order)
			);

			$arFilter = array(
				"REPORT.STORE_ID" => SMShops::getUserShop(),
				"DEPTH_LEVEL" => 1, 
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
								//$arFilter["ID"] = $value;
								$arFilter["IBLOCK_SECTION_ID"] = $value;
								$arFilter["DEPTH_LEVEL"] = 2;
							}
							break;
						case "SECTION":
							if(empty(${$FILTER_NAME}["SUBSECTION"]))
							{
								//$arFilter["ID"] = $value;
								$arFilter["IBLOCK_SECTION_ID"] = $value;
								$arFilter["DEPTH_LEVEL"] = 2;
							}
							break;
						case "SUBSECTION":
							//$arFilter["ID"] = $value;
							break;
						case "DATE_FROM":
							$arFilter[">=REPORT.DATE"] = new Type\DateTime(date("Y-m-d", strtotime($value)), "Y-m-d");
							unset($arFilter[$key]);
							break;
						case "DATE_TO":
							$arFilter["<REPORT.DATE"] = new Type\DateTime(date("Y-m-d", strtotime($value)+24*60*60), "Y-m-d");
							unset($arFilter[$key]);
							break;
						case "SEARCH":
							break;
					}

			$get = $_GET;
			unset($_GET["PROPERTY"]["SECTION"], $_GET["PROPERTY"]["SUBSECTION"]);
			$APPLICATION->SetCurPage($componentPath."/ajax.php");
			if(!empty($_REQUEST["PROPERTY"]["SECTION"]) || !empty($_REQUEST["PROPERTY"]["SUBSECTION"])) 
				$arResult["BACK_URL"] = $APPLICATION->GetCurPageParam("", array("graph"));

			if($arParams["PREV_YEAR"] == "Y") $sectionFilter = array();

			$parameters["filter"] = $arFilter;

			$result = Shopmate\FinanceReport::getSections($parameters);
			while($fields = $result->fetch())
			{
				$fields["SALE"] = floatval($fields["SALE"]);
				$fields["PURCHASE"] = floatval($fields["PURCHASE"]);
				$fields["PROFIT"] = floatval($fields["PROFIT"]);
				$fields["PRICE_PROFIT"] = floatval($fields["PRICE_PROFIT"]);

				if($arParams["PREV_YEAR"] == "Y") 
				{
					$sectionFilter[] = $fields["ID"];
					$fields["SALE_PREV"] = 0;
					$fields["PURCHASE_PREV"] = 0;
					$fields["PROFIT_PREV"] = 0;
					$fields["PRICE_PROFIT_PREV"] = 0;
				}


				if($fields["DEPTH_LEVEL"] > 1)
					$_GET["PROPERTY"]["SECTION"] = $get["PROPERTY"]["SECTION"];
				
				$fields["URL"] = $APPLICATION->GetCurPageParam("PROPERTY[".($fields["DEPTH_LEVEL"] > 1 ? "SUBSECTION" : "SECTION")."]=".$fields["ID"]."&submit=Y", array("graph"));

				$arElements[$fields["ID"]] = $fields;
			}

			if($arParams["PREV_YEAR"] == "Y" && !empty($sectionFilter))
			{
				unset($parameters["count_total"], $parameters["offset"], $parameters["limit"]);
				$parameters["filter"]["ID"] = $sectionFilter;
				if(!empty(${$FILTER_NAME}["DATE_FROM"]))
					$parameters["filter"][">=REPORT.DATE"] = new Type\DateTime((date("Y", strtotime(${$FILTER_NAME}["DATE_FROM"]))-1).date("-m-d 00:00:00", strtotime(${$FILTER_NAME}["DATE_FROM"])), "Y-m-d");
				if(!empty(${$FILTER_NAME}["DATE_TO"]))
					$parameters["filter"]["<REPORT.DATE"] = new Type\DateTime((date("Y", strtotime(${$FILTER_NAME}["DATE_TO"])+24*60*60)-1).date("-m-d 00:00:00", strtotime(${$FILTER_NAME}["DATE_TO"])+24*60*60), "Y-m-d");
				$result = Shopmate\FinanceReport::getSections($parameters);
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

			//$_GET = $get;
			$_GET["PROPERTY"]["SUBSECTION"] = $get["PROPERTY"]["SUBSECTION"];
			$APPLICATION->reinitPath();

			$arResult["ELEMENTS"] = array_values($arElements);
		}
		else
		{
			$arResult["NO_USER"] = "Y";
		}

		$arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strIMessage"]);

		if(empty($arResult["ELEMENTS"])) return false;

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}

	return true;
}
?>