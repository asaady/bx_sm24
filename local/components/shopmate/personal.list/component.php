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

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("personal") >= "R" || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
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

			// get elements list using generated filter
			//CShopmateStore
			$arFilter = array("GROUPS_ID" => array_merge(SM::GetGlobalGroups("personal"), SM::GetPersonalGroups()));
			$arNavParams = $arParams["NAV_ON_PAGE"] > 0 ? array("nPageSize"=>$arParams["NAV_ON_PAGE"]) : false;
			$rsElements = CUser::getList(($by="EMAIL"), ($order="ASC"), $arFilter);

			$arResult["ELEMENTS_COUNT"] = $rsElements->SelectedRowsCount();
			//$page_split = intval(COption::GetOptionString("iblock", "RESULTS_PAGEN"));
			$arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
			$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;

			$rsElements->NavStart($arParams["NAV_ON_PAGE"]);

			// get paging to component result
			if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"])
			{
				$arResult["NAV_STRING"] = $rsElements->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
			}

			// get current page elements to component result
			$arElements = $arUsersID = $arResult["ELEMENTS"] = array();
			$bCanEdit = false;
			$bCanDelete = false;
			while ($arElement = $rsElements->NavNext(false))
			{
				$arElement = htmlspecialcharsex($arElement);
				$arElements[] = $arElement;
				$arUsersID[] = $arElement["ID"];
			}

			if(!empty($arUsersID))
			{

				$arSMElements = array();
				$rsElements = SMUser::getList(array(), array("USER_ID" => $arUsersID));
				while ($arElement = $rsElements->Fetch())
				{
					$USER_ID = $arElement["USER_ID"];
					unset($arElement["ID"], $arElement["USER_ID"]);
					$arElement["START_DATE"] = FormatDate($DB->dateFormatToPHP(FORMAT_DATE), MakeTimeStamp($arElement["START_DATE"], CSite::GetDateFormat()));
					$arSMElements[$USER_ID] = $arElement;
				}

				foreach ($arElements as $keyElement => $arElement) 
				{
					if(!empty($arSMElements[$arElement["ID"]]))
						$arElements[$keyElement] = array_merge($arElement, $arSMElements[$arElement["ID"]]);
				}
			}

			$arResult["ELEMENTS"] = $arElements;
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