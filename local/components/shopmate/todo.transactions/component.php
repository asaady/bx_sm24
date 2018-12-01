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

$arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
if($arParams["PAGE_ELEMENT_COUNT"]<=0)
	$arParams["PAGE_ELEMENT_COUNT"]=10;

if(empty($arParams["FILTER_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	global ${$arParams["FILTER_NAME"]};
	$arrFilter = ${$arParams["FILTER_NAME"]};
	if(!is_array($arrFilter))
		$arrFilter = array();
}

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("contractors") >= "R" || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
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
		$arParams["ITEM_ID"] = intval($arParams["ITEM_ID"]);
		$arResult["ITEMS"] = array();
		$arResult["CREDIT_SUMM"] = 0;

		$arFilter = array("STORE_ID" => SMShops::getUserShop(), "ACTIVE" => "Y");
		if(!empty($arParams["SECTION_ID"])) $arFilter["TRANSACTION"] = $arParams["SECTION_ID"];
		if(!empty($arParams["ITEM_ID"])) $arFilter["ITEM_ID"] = $arParams["ITEM_ID"];
		$arSort = array(
			$arParams["SORT_FIELD"] => $arParams["SORT_ORDER"],
			$arParams["SORT_FIELD2"] => $arParams["SORT_ORDER2"],
		);
		$arNavParams = $arParams["NAV_ON_PAGE"] > 0 ? array("nPageSize"=>$arParams["NAV_ON_PAGE"]) : false;
		$rsElements = SMTodo::getList($arSort, array_merge($arrFilter, $arFilter));

		$arResult["ELEMENTS_COUNT"] = $rsElements->SelectedRowsCount();
		$arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
		$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;

		$rsElements->NavStart($arParams["NAV_ON_PAGE"]);

		// get paging to component result
		if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"])
		{
			$arResult["NAV_STRING"] = $rsElements->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
		}

		while($arElement = $rsElements->NavNext())
		{
			if($arElement["TYPE"] == "TODO")
			{
				$arResult["ITEMS"][] = $arElement;
			}
			else
			{
				if($arElement["CREDIT"] == "Y")
				{
					$arElement["CREDIT_PRICE"] = $arElement["PRICE"];
					$arElement["CREDIT_CURRENCY"] = $arElement["CURRENCY"];
					$arResult["CREDIT_SUMM"] += $arElement["CREDIT_PRICE"];
				}
				else
				{
					$arElement["PAYED_PRICE"] = $arElement["PRICE"];
					$arElement["PAYED_CURRENCY"] = $arElement["CURRENCY"];
				}
				unset($arElement["PRICE"], $arElement["CURRENCY"]);
				$arResult["ITEMS"][$arElement["TYPE"].$arElement["ITEM_ID"]] = array_merge($arElement, (array) $arResult["ITEMS"][$arElement["TYPE"].$arElement["ITEM_ID"]]);
			}
		}
		$arResult["ITEMS"] = array_values($arResult["ITEMS"]);

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}