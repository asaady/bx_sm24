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
function SetInput($propList = array(), &$arParams)
{
	$arPropsListFull = array();
	foreach($propList as $prop)
	{
		switch($prop)
		{
			/*case "PERSON_TYPE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"REQUIRED" => "Y",
					"ENUM" => array(
						1 => array("VALUE" => GetMessage("PERSON_TYPE_FIZ")),
						2 => array("VALUE" => GetMessage("PERSON_TYPE_YUR")),
					)
				);
				break;
			case "REGULAR":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"REQUIRED" => "Y",
					"ENUM" => array(
						1 => array("VALUE" => GetMessage("YES")),
						0 => array("VALUE" => GetMessage("NO")),
					)
				);
				break;
			case "NDS":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"LIST_TYPE" => "C",
					"ENUM" => array(
						18 => array("VALUE" => GetMessage("NDS_18")),
						0 => array("VALUE" => GetMessage("NDS_0")),
					)
				);
				break;*/
			case "GROUP_ID_SHOP":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "Y",
					"ENUM" => array()
				);
				foreach($arParams["PERSONAL_GROUPS"] as $arGroup)
					if(in_array($arGroup["ID"], SM::GetPersonalGroups("shop")))
						$arPropListFull["ENUM"][$arGroup["ID"]] = array("VALUE" => $arGroup["NAME"]);
				
				break;
			case "GROUP_ID_DEPARTMENT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array()
				);
				foreach($arParams["PERSONAL_GROUPS"] as $arGroup)
					if(in_array($arGroup["ID"], SM::GetPersonalGroups("department")))
						$arPropListFull["ENUM"][$arGroup["ID"]] = array("VALUE" => $arGroup["NAME"]);
				
				break;
			case "GROUP_ID_POSITION":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array()
				);
				foreach($arParams["PERSONAL_GROUPS"] as $arGroup)
					if(in_array($arGroup["ID"], SM::GetPersonalGroups("position")))
						$arPropListFull["ENUM"][$arGroup["ID"]] = array("VALUE" => $arGroup["NAME"]);
				
				break;
			case "START_DATE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"USER_TYPE" => "DateTime"
				);
				break;
			case "PERSONAL_STREET":
			case "PERSONAL_NOTES":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "T",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"ROW_COUNT" => 5
				);
				break;
			/*case "PRODUCTION":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
					"MULTIPLE" => "Y",
					"ENUM" => array()
				);
				if(!empty($arParams["PRODUCTION_TYPES"]) && is_array($arParams["PRODUCTION_TYPES"]))
					foreach ($arParams["PRODUCTION_TYPES"] as $key => $value) 
						$arPropListFull["ENUM"][$value] = array("VALUE" => $value);
				break;*/
			/*case "CONTRACTOR_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array()
				);
				$dbPropResultList = CCatalogContractor::GetList(array("COMPANY" => "ASC"), array());
				while ($arPropResult = $dbPropResultList->Fetch())
					$arPropListFull["ENUM"][$arPropResult["ID"]]["VALUE"] = $arPropResult["COMPANY"];
				break;
			case "ELEMENT_END_DATE":
			case "DATE_DOCUMENT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"USER_TYPE" => "DateTime"
				);
				break;
			case "TOTAL_FACT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "N",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
				break;
			case "ELEMENT_ELEMENT_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"LIST_TYPE" => "AJAX"
				);
				//$arPropListFull["URL"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", __DIR__."/search_element.php");
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_element.php";
				break;*/
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
		$arParams["ID"] = intval($_REQUEST["CODE"]);

		$arParams["USER_MESSAGE_ADD"] = trim($arParams["USER_MESSAGE_ADD"]);
		if(strlen($arParams["USER_MESSAGE_ADD"]) <= 0)
			$arParams["USER_MESSAGE_ADD"] = GetMessage("USER_MESSAGE_ADD_DEFAULT");

		$arParams["USER_MESSAGE_EDIT"] = trim($arParams["USER_MESSAGE_EDIT"]);
		if(strlen($arParams["USER_MESSAGE_EDIT"]) <= 0)
			$arParams["USER_MESSAGE_EDIT"] = GetMessage("USER_MESSAGE_EDIT_DEFAULT");

		$arResult["PROPERTY_LIST"] = array(

			"NAME",
			"GROUP_ID_SHOP",
			"GROUP_ID_DEPARTMENT",
			"GROUP_ID_POSITION",
			"PERSONAL_PHONE",
			"EMAIL",
			"START_DATE",
			"SALARY",
			"RATE",
			"RATE_SECTIONS",
			"PERSONAL_STREET",
			"PERSONAL_NOTES",
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		
		$arParams["PERSONAL_GROUPS"] = array();
		$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array("ID" => implode("|", SM::GetPersonalGroups())));
		while($arGroup = $rsGroups->Fetch())
			$arParams["PERSONAL_GROUPS"][] = $arGroup;
		
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		if (check_bitrix_sessid() && ($USER->IsAdmin() || SM::GetUserPermission("personal") >= "W") && (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"])))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;

			$arDocElements = array();

			$arProperties = $_REQUEST["PROPERTY"];

			$arUpdateValues = array(
				//"MODIFIED_BY" => $USER->GetId(),
			);

			foreach($arProperties as $prop => $arProperty)
				if($prop == "PRODUCTION")
					$arUpdateValues[$prop] = is_array($arProperty) ? implode(";", $arProperty) : $arProperty;
				else
					$arUpdateValues[$prop] = is_array($arProperty) ? $arProperty[0] : $arProperty;


			$arUpdateValues["GROUP_ID"] = array();
			$res = CUser::GetUserGroupList($arParams["ID"]);
			while($arGroup = $res->Fetch())
				$arUpdateValues["GROUP_ID"][] = $arGroup["GROUP_ID"];
			$arUpdateValues["GROUP_ID"] = array_merge($arUpdateValues["GROUP_ID"], (array) SM::GetGlobalGroups("personal"));
			$arUpdateValues["GROUP_ID"] = array_merge($arUpdateValues["GROUP_ID"], (array) $arUpdateValues["GROUP_ID_SHOP"]);
			$arUpdateValues["GROUP_ID"] = array_merge($arUpdateValues["GROUP_ID"], (array) $arUpdateValues["GROUP_ID_DEPARTMENT"]);
			$arUpdateValues["GROUP_ID"] = array_merge($arUpdateValues["GROUP_ID"], (array) $arUpdateValues["GROUP_ID_POSITION"]);
			unset($arUpdateValues["GROUP_ID_SHOP"], $arUpdateValues["GROUP_ID_DEPARTMENT"], $arUpdateValues["GROUP_ID_POSITION"]);

			//$arUpdatePropertyValues = array();

			/*print_p($arUpdateValues);
			die();*/
			// update existing element
			$oElement = new SMUser();
			if ($arParams["ID"] > 0)
			{
				$sAction = "EDIT";



				if (!$res = $oElement->Update($arParams["ID"], $arUpdateValues))
				{
					$arResult["ERRORS"][] = $oElement->LAST_ERROR;
				}
			}
			// add new element
			else
			{
				$sAction = "ADD";

				//

				if (!$arParams["ID"] = $oElement->Add($arUpdateValues))
				{
					$arResult["ERRORS"][] = $oElement->LAST_ERROR;
					//print_p($arUpdateValues);
				}

				if (!empty($_REQUEST["apply"]) && strlen($SEF_URL) > 0)
				{
					if (strpos($SEF_URL, "?") === false) $SEF_URL .= "?edit=Y";
					elseif (strpos($SEF_URL, "edit=") === false) $SEF_URL .= "&edit=Y";
					$SEF_URL .= "&CODE=".$arParams["ID"];
				}
			}

			// redirect to element edit form or to elements list
			if (empty($arResult["ERRORS"]))
			{
				if (!empty($_REQUEST["submit"]))
				{
					if (strlen($arParams["LIST_URL"]) > 0)
					{
						$sRedirectUrl = $arParams["LIST_URL"];
					}
					else
					{
						if (strlen($SEF_URL) > 0)
						{
							$SEF_URL = str_replace("edit=Y", "", $SEF_URL);
							$SEF_URL = str_replace("?&", "?", $SEF_URL);
							$SEF_URL = str_replace("&&", "&", $SEF_URL);
							$sRedirectUrl = $SEF_URL;
						}
						else
						{
							$sRedirectUrl = $APPLICATION->GetCurPageParam("", array("edit", "CODE"), $get_index_page=false);
						}

					}
				}
				else
				{
					if (strlen($SEF_URL) > 0)
						$sRedirectUrl = $SEF_URL;
					else
						$sRedirectUrl = $APPLICATION->GetCurPageParam("edit=Y&CODE=".$arParams["ID"], array("edit", "CODE"), $get_index_page=false);
				}

				$sAction = $sAction == "ADD" ? "ADD" : "EDIT";
				$sRedirectUrl .= (strpos($sRedirectUrl, "?") === false ? "?" : "&")."strIMessage=";
				$sRedirectUrl .= urlencode($arParams["USER_MESSAGE_".$sAction]);

				LocalRedirect($sRedirectUrl);
				exit();
			}

			// check captcha
			if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0)
			{
				if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
				{
					$arResult["ERRORS"][] = GetMessage("IBLOCK_FORM_WRONG_CAPTCHA");
				}
			}

		}



		//prepare data for form
		if ($arParams["ID"] > 0)
		{
			$arResult["ELEMENT"] = array();
			$arElement = CUser::getList(($by="EMAIL"), ($order="ASC"), array("ID" => $arParams["ID"]))->Fetch();
			/*$arElement["USER_ID"] = array(
				array("VALUE" => $arElement["USER_ID"])
			);*/
			foreach($arElement as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}
			//SMDocs::add(array("DOC_ID" => $arParams["ID"], "NUMBER_DOCUMENT" => "123", "TOTAL_FACT" => "777"));
			$arElement = SMUser::getList(array("ID" => "ASC"), array("USER_ID" => $arParams["ID"]))->Fetch();
			unset($arElement["ID"], $arElement["USER_ID"]);
			$production = explode(";", $arElement["PRODUCTION"]);
			$arElement["PRODUCTION"] = array();
			foreach($production as $prod)
				$arElement["PRODUCTION"][] = array("VALUE" => $prod);
		
			foreach($arElement as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}

			$arResult["ELEMENT"]["CONTRACT_DATE"] = FormatDate($DB->dateFormatToPHP(FORMAT_DATE), MakeTimeStamp($arResult["ELEMENT"]["CONTRACT_DATE"], CSite::GetDateFormat()));
			$arResult["ELEMENT"]["START_DATE"] = FormatDate($DB->dateFormatToPHP(FORMAT_DATE), MakeTimeStamp($arResult["ELEMENT"]["START_DATE"], CSite::GetDateFormat()));

			$res = CUser::GetUserGroupList($arParams["ID"]);
			while($arGroup = $res->Fetch())
			{
				$arResult["ELEMENT"]["GROUP_ID"][] = array("VALUE" => $arGroup["GROUP_ID"]);
				if(in_array($arGroup["GROUP_ID"], SM::GetPersonalGroups("shop"))) $arResult["ELEMENT"]["GROUP_ID_SHOP"][] = array("VALUE" => $arGroup["GROUP_ID"]);
				if(in_array($arGroup["GROUP_ID"], SM::GetPersonalGroups("department"))) $arResult["ELEMENT"]["GROUP_ID_DEPARTMENT"][] = array("VALUE" => $arGroup["GROUP_ID"]);
				if(in_array($arGroup["GROUP_ID"], SM::GetPersonalGroups("position"))) $arResult["ELEMENT"]["GROUP_ID_POSITION"][] = array("VALUE" => $arGroup["GROUP_ID"]);
			}

			$bShowForm = true;
		}
		else
		{
			$bShowForm = true;
		}

		$arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strIMessage"]);

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}