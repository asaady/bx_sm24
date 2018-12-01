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
$arParams["componentPath"] = $componentPath;

if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";
$FILTER_NAME = $arParams["FILTER_NAME"];
global ${$FILTER_NAME};

function SetInput($propList = array(), &$arParams)
{
	$arPropsListFull = array();
	foreach($propList as $prop)
	{
		switch($prop)
		{
			case "DATE_FROM":
			case "DATE_TO":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"USER_TYPE" => "DateTime"
				);
				break;
			case "DEBT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "Y",
					"LIST_TYPE" => "C",
					"ENUM" => array(1 => array("VALUE" => GetMessage("CUSTOM_TITLE_DEBT"))),
				);
				break;
			default:
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
		}
		$arPropsListFull[$prop] = $arPropListFull;
	}

	return $arPropsListFull;
}

function URLDateInterval($date_from, $date_to)
{
	global $APPLICATION;
	return $APPLICATION->GetCurPageParam("PROPERTY[DATE_FROM][]=".ConvertTimeStamp($date_from, "SHORT")."&PROPERTY[DATE_TO][]=".ConvertTimeStamp($date_to, "SHORT")."&submit=Y", array("PROPERTY", "submit"));
}

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("cash") >= "R" || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
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
		$arParams["ID"] = intval($_REQUEST["CODE"]);

		$arParams["USER_MESSAGE_ADD"] = trim($arParams["USER_MESSAGE_ADD"]);
		if(strlen($arParams["USER_MESSAGE_ADD"]) <= 0)
			$arParams["USER_MESSAGE_ADD"] = GetMessage("USER_MESSAGE_ADD_DEFAULT");

		$arParams["USER_MESSAGE_EDIT"] = trim($arParams["USER_MESSAGE_EDIT"]);
		if(strlen($arParams["USER_MESSAGE_EDIT"]) <= 0)
			$arParams["USER_MESSAGE_EDIT"] = GetMessage("USER_MESSAGE_EDIT_DEFAULT");

		$arResult["PROPERTY_LIST"] = array(
			"DATE_FROM",
			"DATE_TO",
			"ACCOUNT_NUMBER",
			"DEBT",
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		$today = time();
		$arResult["FILTER_DATE"] = array(
			"TODAY" => URLDateInterval($today, $today),
			"WEEK" => URLDateInterval($today - 7 * 24 * 60 * 60, $today),
			"MONTH" => URLDateInterval($today - 30 * 24 * 60 * 60, $today),
			"QUARTER" => URLDateInterval($today - 4 * 30 * 24 * 60 * 60, $today),
		);

		if (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"]))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;

			$arProperties = $_REQUEST["PROPERTY"];
			foreach($arProperties as $prop => $arProperty)
				$arProperties[$prop] = is_array($arProperty) ? current($arProperty) : $arProperty;
			
			if(!empty($arProperties["DATE_FROM"]) || !empty($arProperties["DATE_TO"]))
			{
				if(!empty($arProperties["DATE_FROM"]))
					${$FILTER_NAME}[">=DATE_INSERT"] = $arProperties["DATE_FROM"]." 00:00:00";
				if(!empty($arProperties["DATE_TO"]))
					${$FILTER_NAME}["<=DATE_INSERT"] = $arProperties["DATE_TO"]." 23:59:59";
			}
			if(!empty($arProperties["ACCOUNT_NUMBER"]))
				${$FILTER_NAME}["~ACCOUNT_NUMBER"] = "%".$arProperties["ACCOUNT_NUMBER"]."%";
			if($arProperties["DEBT"] > 0)
				${$FILTER_NAME}["!PAYED"] = "Y";


			foreach($arProperties as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}
		}

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}