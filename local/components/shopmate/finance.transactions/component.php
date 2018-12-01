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
		if(!empty($arParams["TRANSACTION"])) $arFilter["TRANSACTION"] = $arParams["TRANSACTION"];
		if(!empty($arParams["ITEM_ID"])) $arFilter["ITEM_ID"] = $arParams["ITEM_ID"];
		$arSort = array(
			$arParams["SORT_FIELD"] => $arParams["SORT_ORDER"],
			$arParams["SORT_FIELD2"] => $arParams["SORT_ORDER2"],
		);
		$rsFinanceLog = SMFinanceLog::getList($arSort, array_merge($arrFilter, $arFilter));

		if($arParams["PAGE_ELEMENT_COUNT"] > 0)
		{
			$arResult["ELEMENTS_COUNT"] = $rsFinanceLog->SelectedRowsCount();
			//$page_split = intval(COption::GetOptionString("iblock", "RESULTS_PAGEN"));
			$arParams["NAV_ON_PAGE"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
			$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;
			$rsFinanceLog->NavStart($arParams["NAV_ON_PAGE"]);
			// get paging to component result
			if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"])
				$arResult["NAV_STRING"] = $rsFinanceLog->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
		}

		while($arFinanceLog = $rsFinanceLog->Fetch())
		{
			if($arFinanceLog["CREDIT"] == "Y")
			{
				$arFinanceLog["CREDIT_PRICE"] = $arFinanceLog["PRICE"];
				$arFinanceLog["CREDIT_CURRENCY"] = $arFinanceLog["CURRENCY"];
				$arResult["CREDIT_SUMM"] += $arFinanceLog["CREDIT_PRICE"];
			}
			else
			{
				$arFinanceLog["PAYED_PRICE"] = $arFinanceLog["PRICE"];
				$arFinanceLog["PAYED_CURRENCY"] = $arFinanceLog["CURRENCY"];
			}
			unset($arFinanceLog["PRICE"], $arFinanceLog["CURRENCY"]);
			$arResult["ITEMS"][$arFinanceLog["TYPE"].$arFinanceLog["ITEM_ID"]] = array_merge($arFinanceLog, (array) $arResult["ITEMS"][$arFinanceLog["TYPE"].$arFinanceLog["ITEM_ID"]]);
		}
		$arResult["ITEMS"] = array_values($arResult["ITEMS"]);

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}