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
			case "STORE_PRODUCT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array(
						"ALL" => array("VALUE" => GetMessage("STORE_PRODUCT_ALL")),
						"BASE" => array("VALUE" => GetMessage("STORE_PRODUCT_BASE")),
						"STORE" => array("VALUE" => GetMessage("STORE_PRODUCT_STORE")),
					)
				);
				break;
			case "CONTRACTOR":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					//"ENUM" => array(),
					"LIST_TYPE" => "AJAX"
				);
				if(!empty($_REQUEST["PROPERTY"]["CONTRACTOR"]))
				{
					$dbPropResultList = CCatalogContractor::GetList(array("COMPANY" => "ASC"), array("ID" => $_REQUEST["PROPERTY"]["CONTRACTOR"]), false, false, array("ID", "COMPANY"));
					while ($arPropResult = $dbPropResultList->Fetch())
						$arPropListFull["ENUM"][$arPropResult["ID"]]["VALUE"] = $arPropResult["COMPANY"];
				}
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_contractor.php";
				break;
			case "SECTION":
			case "SUBSECTION":
			case "PACK":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array(),
				);
				break;
			case "LAST_OVERHEAD":
			case "LAST_OVERHEAD_FROM":
			case "LAST_OVERHEAD_TO":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"USER_TYPE" => "DateTime"
				);
				break;
			case "PERISHABLE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "Y",
					"LIST_TYPE" => "C",
					"ENUM" => array(1 => array("VALUE" => GetMessage("CUSTOM_TITLE_PERISHABLE"))),
				);
				break;
			case "EXPIRATION":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "Y",
					"LIST_TYPE" => "C",
					"ENUM" => array(1 => array("VALUE" => GetMessage("CUSTOM_TITLE_EXPIRATION"))),
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
			"SECTION",
			"SUBSECTION",
			"LAST_OVERHEAD",
			"LAST_OVERHEAD_FROM",
			"LAST_OVERHEAD_TO",
			"PACK",
			"STORE_PRODUCT",
			"PRODUCT_SEARCH",
			"CONTRACTOR",
			"PERISHABLE",
			"EXPIRATION"
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		$rsSection = CIBlockSection::GetTreeList(array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "<DEPTH_LEVEL" => 3, "ACTIVE" => "Y"), array("ID", "NAME", "DEPTH_LEVEL", "IBLOCK_SECTION_ID"));
		while($arSection = $rsSection->GetNext())
		{
			if($arSection["DEPTH_LEVEL"] == 1)
				$arResult["PROPERTY_LIST_FULL"]["SECTION"]["ENUM"][$arSection["ID"]] = array(
					"VALUE" => $arSection["NAME"],
				);
			elseif($arSection["DEPTH_LEVEL"] == 2)
			{
				$arResult["PROPERTY_LIST_FULL"]["SUBSECTION"]["ENUM"][$arSection["ID"]] = array(
					"VALUE" => $arSection["NAME"],
					"DATA" => array(
						"parent" => $arSection["IBLOCK_SECTION_ID"]
					),
				);
			}
		}

		$rsPack = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => "PACK"));
		while($arPack = $rsPack->GetNext())
			$arResult["PROPERTY_LIST_FULL"]["PACK"]["ENUM"][$arPack["ID"]] = array("VALUE" => $arPack["VALUE"]);

		$arProperties = $_REQUEST["PROPERTY"];
		foreach($arProperties as $prop => $arProperty)
			$arProperties[$prop] = is_array($arProperty) ? current($arProperty) : $arProperty;

		if($arProperties["STORE_PRODUCT"] == "BASE")
			$arUsers = CGroup::GetGroupUser(1);
		elseif($arProperties["STORE_PRODUCT"] == "STORE")
			$arUsers = CGroup::GetGroupUser(SMShops::getUserGroup());
		else
			$arUsers = array_merge(CGroup::GetGroupUser(1), CGroup::GetGroupUser(SMShops::getUserGroup()));
		${$FILTER_NAME}["CREATED_BY"] = $arUsers;
			
		if (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"]))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;


			if($arProperties["SUBSECTION"] > 0)
				${$FILTER_NAME}["SECTION_ID"] = $arProperties["SUBSECTION"];
			elseif($arProperties["SECTION"] > 0)
				${$FILTER_NAME}["SECTION_ID"] = $arProperties["SECTION"];
			if(${$FILTER_NAME}["SECTION_ID"] > 0)
				${$FILTER_NAME}["INCLUDE_SUBSECTIONS"] = "Y";
			if($arProperties["PACK"] > 0)
				${$FILTER_NAME}["PROPERTY_PACK"] = $arProperties["PACK"];
			if($arProperties["PERISHABLE"] > 0 || $arProperties["EXPIRATION"] > 0)
			{
				$arDocFilter = array();
				if($arProperties["PERISHABLE"] > 0)
					$arDocFilter["!END_DATE"] = false;
				if($arProperties["EXPIRATION"] > 0)
				{
					$arDocFilter["!END_DATE"] = false;
					$arDocFilter["<=END_DATE"] = date("d.m.Y")." 23:59:59";
				}
				$rsDocElement = SMStoreDocsElement::getList(array("ID" => "ASC"), $arDocFilter, array("ELEMENT_ID"), false, array("ELEMENT_ID"));
				while($arDocElement = $rsDocElement->Fetch())
					${$FILTER_NAME}["ID"][] = $arDocElement["ELEMENT_ID"];
				if(empty(${$FILTER_NAME}["ID"])) ${$FILTER_NAME}["ID"] = false;
			}

			if(!empty($arProperties["PRODUCT_SEARCH"]))
			{
				${$FILTER_NAME}["NAME"] = str_combos($arProperties["PRODUCT_SEARCH"]);
			}

			if((!empty($arProperties["LAST_OVERHEAD"]) || !empty($arProperties["LAST_OVERHEAD_FROM"]) || !empty($arProperties["LAST_OVERHEAD_TO"]) || $arProperties["CONTRACTOR"] > 0) && ${$FILTER_NAME}["ID"] !== false)
			{
				$docs_id = array();
				$arDocFilter = array("DOC_TYPE" => "A");
				if($arProperties["CONTRACTOR"] > 0)
					$arDocFilter["CONTRACTOR_ID"] = $arProperties["CONTRACTOR"];
				if(!empty($arProperties["LAST_OVERHEAD"]))
				{
					$arDocFilter[">=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD"]." 00:00:00";
					$arDocFilter["<=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD"]." 23:59:59";
				}
				if(!empty($arProperties["LAST_OVERHEAD_FROM"]))
					$arDocFilter[">=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD_FROM"]." 00:00:00";
				if(!empty($arProperties["LAST_OVERHEAD_TO"]))
					$arDocFilter["<=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD_TO"]." 23:59:59";
				$rsDocs = CCatalogDocs::getList(array(), $arDocFilter, false, false, array("ID"));
				while($arDocs = $rsDocs->Fetch())
					$docs_id[] = $arDocs["ID"];
				if(!empty($docs_id))
				{
					$elems_id = array();

					$rsDocElement = CCatalogStoreDocsElement::getList(array(), array("DOC_ID" => $docs_id, "STORE_TO" => SMShops::getUserShop()), array("ELEMENT_ID"), false, array("ELEMENT_ID"));
					while($arDocElement = $rsDocElement->Fetch())
						$elems_id[] = $arDocElement["ELEMENT_ID"];

					${$FILTER_NAME}["ID"] = is_array(${$FILTER_NAME}["ID"]) ? array_intersect(${$FILTER_NAME}["ID"], $elems_id) : $elems_id;
				}
				if(empty(${$FILTER_NAME}["ID"])) ${$FILTER_NAME}["ID"] = false;
			}

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