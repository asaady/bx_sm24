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

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "R" || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
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

		if ($USER->GetID())
		{
			$arResult["NO_USER"] = "N";

			$arResult["ITEMS"] = array();
			if(!empty($_REQUEST["accepted_y"]) && !empty($_REQUEST["accepted_n"]))
				$filter = array();
			elseif($_REQUEST["accepted_y"] == "Y")
				$filter["ACCEPTED"] = $_REQUEST["accepted_y"];
			elseif($_REQUEST["accepted_n"] == "N")
				$filter["ACCEPTED"] = $_REQUEST["accepted_n"];
			else $filter = array();

			$rsElements = SMEGAISWaybill::GetList(array("DATE" => "DESC"), $filter);

			$arResult["ELEMENTS_COUNT"] = $rsElements->SelectedRowsCount();
			$arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
			$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;

			$rsElements->NavStart($arParams["NAV_ON_PAGE"]);
			// get paging to component result
			if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"])
			{
				$arResult["NAV_STRING"] = $rsElements->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
			}
			while($arElement = $rsElements->Fetch())
			{
				$waybill = simplexml_load_string($arElement["WAYBILL"]);
				$arElement["TOTAL_QUANTITY"] = $arElement["TOTAL"] = 0;
				$arElement["CNT"] = count($waybill->Document->WayBill->Content->Position);
				foreach ($waybill->Document->WayBill->Content->Position as $position) 
				{
					$arElement["TOTAL_QUANTITY"] += floatval($position->Quantity);
					$arElement["TOTAL"] += floatval($position->Quantity) * floatval($position->Price);
				}
				$arResult["ITEMS"][] = $arElement;
			}
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