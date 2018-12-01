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
			case "DEPARTMENT":
			case "SECTION":
			case "SUBSECTION":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array(),
				);
				break;
			case "DATE_FROM":
			case "DATE_TO":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"USER_TYPE" => "DateTime"
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

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("products") >= "R" || is_array($arGroups) && is_array($arParams["GROUPS"]) && count(array_intersect($arGroups, $arParams["GROUPS"])) > 0)
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
			// "DEPARTMENT",
			"SECTION",
			//"SUBSECTION",
			"DATE_FROM",
			"DATE_TO",
			"SEARCH",
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		$rsSection = CIBlockSection::GetTreeList(array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "<DEPTH_LEVEL" => 3, "ACTIVE" => "Y"), array("ID", "NAME", "DEPTH_LEVEL", "IBLOCK_SECTION_ID"));
		while($arSection = $rsSection->GetNext())
		{
			/*if($arSection["DEPTH_LEVEL"] == 1)
				$arResult["PROPERTY_LIST_FULL"]["SECTION"]["ENUM"][$arSection["ID"]] = array(
					"VALUE" => $arSection["NAME"],
					"DATA" => array(
						"parent" => $arSection["ID"]
					),
				);
			elseif($arSection["DEPTH_LEVEL"] == 2)
			{
				$arResult["PROPERTY_LIST_FULL"]["SUBSECTION"]["ENUM"][$arSection["ID"]] = array(
					"VALUE" => $arSection["NAME"],
					"DATA" => array(
						"parent" => $arSection["IBLOCK_SECTION_ID"]
					),
				);
			}*/
			$arResult["PROPERTY_LIST_FULL"]["SECTION"]["ENUM"][$arSection["ID"]] = array(
				"VALUE" => str_repeat("- ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"],
				"DATA" => array(
					"parent" => $arSection["ID"]
				),
			);
		}
		$arResult["PROPERTY_LIST_FULL"]["DEPARTMENT"]["ENUM"] = $arResult["PROPERTY_LIST_FULL"]["SECTION"]["ENUM"];

		if (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"]))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;

			$arProperties = $_REQUEST["PROPERTY"];
			foreach($arProperties as $prop => $arProperty)
				$arProperties[$prop] = is_array($arProperty) ? current($arProperty) : $arProperty;

			${$FILTER_NAME} = $arProperties;

			foreach($arProperties as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}
		}
		else
		{
			$arResult["ELEMENT"]["DATE_FROM"] = ${$FILTER_NAME}["DATE_FROM"] = "01.".date("m").".".date("Y");
			$arResult["ELEMENT"]["DATE_TO"] = ${$FILTER_NAME}["DATE_TO"] = date("d").".".date("m").".".date("Y");
		}

		$cur_day = date("d");
		$cur_month = date("m");
		$cur_year = date("Y");
		$cur_quarter = floor($cur_month/3)*3;
		$arResult["CURRENT_MONTH"] = $APPLICATION->GetCurPageParam("PROPERTY[DATE_FROM][]=01.".$cur_month.".".$cur_year."&PROPERTY[DATE_TO][]=".$cur_day.".".$cur_month.".".$cur_year."&submit=Найти", array("PROPERTY", "submit"));
		$arResult["CURRENT_QUARTER"] = $APPLICATION->GetCurPageParam("PROPERTY[DATE_FROM][]=01.".($cur_quarter < 10 ? "0" : "").$cur_quarter.".".$cur_year."&PROPERTY[DATE_TO][]=".$cur_day.".".$cur_month.".".$cur_year."&submit=Найти", array("PROPERTY", "submit"));
		$arResult["CURRENT_YEAR"] = $APPLICATION->GetCurPageParam("PROPERTY[DATE_FROM][]=01.01.".$cur_year."&PROPERTY[DATE_TO][]=".$cur_day.".".$cur_month.".".$cur_year."&submit=Найти", array("PROPERTY", "submit"));

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}