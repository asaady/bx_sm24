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
			/*case "CONTRACTOR_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					//"ENUM" => array(),
					"LIST_TYPE" => "AJAX"
				);
				$dbPropResultList = CCatalogContractor::GetList(array("COMPANY" => "ASC"), array());
				while ($arPropResult = $dbPropResultList->Fetch())
					$arPropListFull["ENUM"][$arPropResult["ID"]]["VALUE"] = $arPropResult["COMPANY"];
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_contractor.php";
				break;
			case "ELEMENT_START_DATE":
			case "ELEMENT_END_DATE":
			case "DATE_DOCUMENT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"USER_TYPE" => "DateTime"
				);
				break;
			case "ELEMENT_START_DATE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "N",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
				break;
			case "ELEMENT_PURCHASING_NDS":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array(),
					"CLASS" => " purchasing_nds_selecter "
				);
				$dbResult = CCatalogVat::GetListEx(array("SORT" => "ASC"), array("ACTIVE" => "Y"), false, false, array("ID", "NAME", "RATE"));
				while ($arRes = $dbResult->Fetch())
					$arPropListFull["ENUM"][floatval($arRes["RATE"])] = array("VALUE" => $arRes["NAME"]);
				break;
			case "ELEMENT_NDS_VALUE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"CLASS" => "nds_value"
				);
				$dbResult = CCatalogVat::GetListEx(array("SORT" => "ASC"), array("ACTIVE" => "Y"), false, false, array("ID", "NAME", "RATE"));
				while ($arRes = $dbResult->Fetch())
					$arPropListFull["ENUM"][floatval($arRes["RATE"])] = array("VALUE" => $arRes["NAME"]);
				break;
			case "ELEMENT_ELEMENT_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"LIST_TYPE" => "AJAX"
				);
				//$arPropListFull["URL"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", __DIR__."/search_element.php");
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_element.php";
				$arPropListFull["INFO_URL"] = $arParams["componentPath"]."/search_element_info.php";
				break;
			case "ELEMENT_MEASURE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"DISABLED" => "Y",
				);
				$oMeasure = CCatalogMeasure::GetList();
				while($arMeasure = $oMeasure->Fetch())
					$arPropListFull["ENUM"][$arMeasure["ID"]]["VALUE"] = $arMeasure["SYMBOL_RUS"];
				break;*/
			case "CONTRACTOR_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					//"ENUM" => array(),
					"LIST_TYPE" => "AJAX"
				);
				$dbPropResultList = CCatalogContractor::GetList(array("COMPANY" => "ASC"), array());
				while ($arPropResult = $dbPropResultList->Fetch())
					$arPropListFull["ENUM"][$arPropResult["ID"]]["VALUE"] = $arPropResult["COMPANY"];
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_contractor.php";
				break;
			case "ELEMENT_ELEMENT_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"LIST_TYPE" => "AJAX"
				);
				//$arPropListFull["URL"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", __DIR__."/search_element.php");
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_element.php";
				$arPropListFull["INFO_URL"] = $arParams["componentPath"]."/search_element_info.php";
				break;
			case "ELEMENT_AMOUNT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
				break;
			case "ELEMENT_ELEMENT_NAME":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "T",
					"MULTIPLE" => "N",
					"DISABLED" => "Y",
					"ROW_COUNT" => 5,
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
				break;
			case "ELEMENT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "DOC_ELEMENT",
					"MULTIPLE" => "Y",
					"DISABLED" => "Y",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"ENUM" => array()
				);
				$tmpPropList = array(
					"ELEMENT_ID", 
					"ELEMENT_NAME", 
					"DOC_AMOUNT",
					//"MEASURE",
					"AMOUNT", 
					//"SHOP_PRICE", 
					"PURCHASING_PRICE", 
					//"PURCHASING_NDS", 
					"PURCHASING_SUMM", 
					//"NDS_VALUE",
					//"START_DATE",
				);
				foreach($tmpPropList as $tmpProp)
					$arPropListFull["arResult"]["PROPERTY_LIST"][] = $prop."_".$tmpProp;
				foreach($arPropListFull["arResult"]["PROPERTY_LIST"] as $propertyID) 
					$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
				$arPropListFull["arResult"]["PROPERTY_LIST_FULL"] = SetInput($arPropListFull["arResult"]["PROPERTY_LIST"], $arParams);
				/*if($arParams["ID"] > 0)
				{
					$arDocsElementIDs = array();
					$arEnumElementIDs = array();
					$dbElement = CCatalogStoreDocsElement::getList(array(), array("DOC_ID" => $arParams["ID"]));
					while($tmpElement = $dbElement->Fetch())
					{
						$arElement = array();
						foreach($tmpElement as $keyElem => $arElem)
							$arElement[$prop."_".$keyElem] = $arElem;
						$arPropListFull["ENUM"][] = $arElement;
						$arDocsElementIDs[] = $tmpElement["ID"];
						$arEnumElementIDs[] = $tmpElement["ELEMENT_ID"];
					}

					if(!empty($arEnumElementIDs))
					{
						$product_id = array();
						$arProducts = array();
						$rsElement = CIBlockElement::GetList(Array(), array("ID" => $arEnumElementIDs), false, false, array("ID", "NAME"));
						while($arFields = $rsElement->Fetch())
						{
							$product_id[] = $arFields["ID"];
							$arProducts[$arFields["ID"]] = $arFields;
							$arProducts[$arFields["ID"]]["BARCODES"] = array();
						}
						if(!empty($product_id))
						{
							$rsBarCode = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $product_id), false, false, array("PRODUCT_ID", "BARCODE"));
							while($arBarCode = $rsBarCode->Fetch())		
								$arProducts[$arBarCode["PRODUCT_ID"]]["BARCODES"][] = $arBarCode["BARCODE"];
						}
						foreach ($arProducts as $arProduct) 
							$arPropListFull["arResult"]["PROPERTY_LIST_FULL"][$prop."_"."ELEMENT_ID"]["ENUM"][$arProduct["ID"]] = array("VALUE" => $arProduct["NAME"].(!empty($arProduct["BARCODES"]) ? " [".implode(", ", $arProduct["BARCODES"])."]" : ""));
					}
					
					$tmpDocsElements = array();
					$dbElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("DOCS_ELEMENT_ID" => $arDocsElementIDs));
					while($tmpElement = $dbElement->Fetch())
					{
						$docElementId = $tmpElement["DOCS_ELEMENT_ID"];
						unset($tmpElement["ID"], $tmpElement["DOCS_ELEMENT_ID"]);
						$tmpDocsElements[$docElementId] = $tmpElement;
					}
					foreach ($arPropListFull["ENUM"] as $keyEnum => $arEnum) 
					{
						foreach($tmpDocsElements[$arEnum[$prop."_"."ID"]] as $key => $value)
						{
							if(!is_array($value) && !is_object($value))
								$arPropListFull["ENUM"][$keyEnum][$prop."_".$key] = htmlspecialcharsbx($value);
							else
								$arPropListFull["ENUM"][$keyEnum][$prop."_".$key] = $value;
						}
					}
					$tmpProducts = array();
					if(!empty($arEnumElementIDs))
					{
						$rsProps = SMProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $arEnumElementIDs), false, false, array("ID", "PRODUCT_ID", "SHELF_LIFE"));
						while($arProp = $rsProps->GetNext())
							$tmpProducts[$arProp["PRODUCT_ID"]] = $arProp["SHELF_LIFE"];
					}
					foreach ($arPropListFull["ENUM"] as $keyEnum => $arEnum) 
					{
						if(!empty($arEnum[$prop."_END_DATE"]) && !empty($tmpProducts[$arEnum[$prop."_ELEMENT_ID"]]))
							$arPropListFull["ENUM"][$keyEnum][$prop."_START_DATE"] = ConvertTimeStamp(strtotime($arEnum[$prop."_END_DATE"]) - floatval($tmpProducts[$arEnum[$prop."_ELEMENT_ID"]])*24*60*60, "SHORT");
						$arPropListFull["ENUM"][$keyEnum][$prop."_PURCHASING_SUMM"] = round($arEnum[$prop."_PURCHASING_PRICE"] * $arEnum[$prop."_DOC_AMOUNT"] * ($arEnum[$prop."_PURCHASING_NDS"] + 100) / 100, 2);
						$arPropListFull["ENUM"][$keyEnum][$prop."_PURCHASING_PRICE"] = round($arEnum[$prop."_PURCHASING_PRICE"], 2);
					}
				}*/
				break;
			case "NOTE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "T",
				);
				break;
			/*case "TOTAL_SUMM":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"DISABLED" => "Y",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
				break;*/
			default:
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"DISABLED" => "Y",
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
		$arParams["ID"] = intval($_REQUEST["CODE"]);

		$arParams["USER_MESSAGE_ADD"] = trim($arParams["USER_MESSAGE_ADD"]);
		if(strlen($arParams["USER_MESSAGE_ADD"]) <= 0)
			$arParams["USER_MESSAGE_ADD"] = GetMessage("USER_MESSAGE_ADD_DEFAULT");

		$arParams["USER_MESSAGE_EDIT"] = trim($arParams["USER_MESSAGE_EDIT"]);
		if(strlen($arParams["USER_MESSAGE_EDIT"]) <= 0)
			$arParams["USER_MESSAGE_EDIT"] = GetMessage("USER_MESSAGE_EDIT_DEFAULT");

		$arResult["PROPERTY_LIST"] = array(
			"NUMBER_DOCUMENT",
			"DATE_DOCUMENT",
			//"CONTRACTOR_NAME",
			"CONTRACTOR_ID",
			"ELEMENT",
			"NOTE",
			"TOTAL_SUMM",
			//"TOTAL_FACT",
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		if (check_bitrix_sessid() && ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "W") && (!empty($_POST["accepted"]) || !empty($_POST["rejected"]) || !empty($_POST["changed"])))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;
			$arProperties = $_REQUEST["PROPERTY"];

			$arResult["ACT_DATA"]["WAYBILL_ID"] = $arParams["ID"];

			if(!empty($_POST["accepted"])) $arResult["SEND_ACT"] = "accepted";
			if(!empty($_POST["rejected"]))
			{
				$arResult["SEND_ACT"] = "rejected";
				$arResult["ACT_DATA"] = array("IsAccept" => "Rejected", "Note" => "Отказ от накладной");
			}
			if(!empty($_POST["changed"]))
			{
				$arResult["SEND_ACT"] = "accepted";
				$arResult["ACT_DATA"] = array("Positions" => array());

				foreach($arProperties["ELEMENT"]["ID"] as $keyID => $Identity)
				{
					if($arProperties["ELEMENT"]["ELEMENT_DOC_AMOUNT"][$keyID] > $arProperties["ELEMENT"]["ELEMENT_AMOUNT"][$keyID])
						$arResult["ACT_DATA"]["Positions"][$Identity] = $arProperties["ELEMENT"]["ELEMENT_AMOUNT"][$keyID];
				}
			}
			if(!empty($arProperties["NOTE"][0]))
				$arResult["ACT_DATA"]["Note"] = $arProperties["NOTE"][0];

			$arResult["ELEMENT"] = $arElement = SMEGAISWaybill::getList(array("ID" => "ASC"), array("ID" => $arParams["ID"]), false, false, array("WAYBILL", "FORMBREGINFO"))->Fetch();
			
			foreach ($arProperties["ELEMENT"]["ELEMENT_ELEMENT_NAME"] as $elem_key => $elem_name) 
				if(!empty($elem_name) && empty($arProperties["ELEMENT"]["ELEMENT_ELEMENT_ID"][$elem_key]))
					$arResult["ERRORS"][] = GetMessage("ERROR_PROD_SELECT")." \"".$elem_name."\"";

			$waybill = simplexml_load_string($arElement["WAYBILL"]);

			$save_elem_id = array();
			if(is_array($arProperties["ELEMENT"]["ELEMENT_ELEMENT_ID"]))
				foreach($arProperties["ELEMENT"]["ELEMENT_ELEMENT_ID"] as $elem_key => $elem_id) 
					if($elem_id > 0)
						$save_elem_id[$arProperties["ELEMENT"]["ID"][$elem_key]] = $elem_id;

			$err_alccode = $cur_alccode = $alccode_title = $elem_alccode = array();
			foreach($waybill->Document->WayBill->Content->Position as $position) 
			{
				$Identity = (string) $position->Identity;
				$AlcCode = (string) $position->Product->AlcCode;
				if($save_elem_id[$Identity] > 0)
				{
					$elem_alccode[$AlcCode] = $save_elem_id[$Identity];
					$alccode_title[$AlcCode] = (string) $position->Product->FullName;
				}
			}
			if(!empty($elem_alccode))
			{
				$dbResultList = SMEGAISAlcCode::getList(array(), array("ALCCODE" => array_keys($elem_alccode)));
				while($arFields = $dbResultList->Fetch())
					$cur_alccode[$arFields["ALCCODE"]] = $arFields["PRODUCT_ID"];
			}
			if(!empty($cur_alccode))
			{
				foreach($cur_alccode as $alccode => $elem_id) 
				{
					if($elem_alccode[$alccode] != $elem_id)
						$err_alccode[$alccode] = $elem_id;
					unset($elem_alccode[$alccode]);
				}
			}
			if(!empty($err_alccode))
			{
				$elem_ids = array();
				foreach($err_alccode as $alccode => $elem_id)
					$elem_ids[] = $elem_id;
				if(!empty($elem_ids))
				{
					$dbResultList = CIBlockElement::getList(array(), array("ID" => $elem_ids), false, false, array("ID", "NAME"));
					while($arFields = $dbResultList->Fetch())
						$err_title[$arFields["ID"]] = $arFields["NAME"];
				}
				foreach($err_alccode as $alccode => $elem_id)
					$arResult["ERRORS"][] = GetMessage("ERROR_PROD_SELECT_FROM")." \"".$alccode_title[$alccode]."\" (AlcCode: ".$alccode.") ".GetMessage("ERROR_PROD_SELECT_TO")." \"".$err_title[$elem_id]."\" (ID: ".$elem_id.")";
			}
			if(!empty($elem_alccode))
			{
				global $USER;
				$userId = intval($USER->GetID());
				foreach($elem_alccode as $alccode => $elem_id)
				{
					SMEGAISAlcCode::Add(array(
						"ALCCODE" => $alccode,
						"PRODUCT_ID" => $elem_id,
						"CREATED_BY" => $userId,
						"MODIFIED_BY" => $userId,
						"STORE_ID" => 0,
					));
				}
			}
			/*$contractor = $waybill->Document->WayBill->Header->Shipper;
			if($arContractor = CCatalogContractor::getList(array("ID" => "ASC"), array("INN" => (string) $contractor->INN), false, false, array("ID"))->Fetch())
			{
				$_REQUEST["PROPERTY"]["CONTRACTOR_ID"] = array($arContractor["ID"]);
			}
			else
			{
				$arContractor = array(
					"PERSON_TYPE" => 2,
					"PERSON_NAME" => (string) $contractor->ShortName,
					"COMPANY" => (string) $contractor->FullName,
					"INN" => (string) $contractor->INN,
					"KPP" => (string) $contractor->KPP,
					"ADDRESS" => (string) $contractor->address->description,
					"MODIFIED_BY" => $USER->GetId(),
				);
				$arContractor["ID"] = SMContractor::Add($arContractor);
				$_REQUEST["PROPERTY"]["CONTRACTOR_ID"] = array($arContractor["ID"]);
			}*/

			if(empty($arResult["ERRORS"]))
			{
				$arDocElements = array();

				$arProperties = $_REQUEST["PROPERTY"];
				$arNum = array("ELEMENT" => array("ELEMENT_DOC_AMOUNT", "ELEMENT_AMOUNT", "ELEMENT_SHOP_PRICE", "ELEMENT_PURCHASING_PRICE", "ELEMENT_PURCHASING_NDS", "ELEMENT_PURCHASING_SUMM"), "TOTAL_FACT");
				$arProperties = floatCase($arProperties, $arNum);

				$arUpdateValues = array(
					"CURRENCY" => "RUB",
					"TOTAL" => 0,
					"MODIFIED_BY" => $USER->GetId(),
					"DOC_TYPE" => "A",
					"SITE_ID" => SITE_ID,
					"STATUS" => "Y",
					"STORE_TO" => SMShops::getUserShop(),
					"STORE_ID" => SMShops::getUserShop(),
				);

				foreach($arProperties as $prop => $arProperty)
					if($prop == "ELEMENT")
					{
						//$arDocElements = array();
						$elemsKeys = array_keys($arProperty[$prop."_"."ELEMENT_ID"]);
						$elemsPropKeys = array_keys($arProperty);
						foreach($elemsKeys as $elemKey)
						{
							$arDocElement = array();
							foreach($elemsPropKeys as $elemsPropKey)
							{
								$elemProp = stripos($elemsPropKey, $prop."_") === 0 ? substr($elemsPropKey, strlen($prop."_")) : $elemsPropKey;
								$arDocElement[$elemProp] = $arProperty[$elemsPropKey][$elemKey];
							}
							$arDocElement["STORE_TO"] = SMShops::getUserShop();
							if($arDocElement["PURCHASING_SUMM"] > 0) 
								$arDocElement["PURCHASING_PRICE"] = $arDocElement["PURCHASING_SUMM"] * 100 / (100 + $arDocElement["PURCHASING_NDS"]) / $arDocElement["DOC_AMOUNT"];
							if($arDocElement["ELEMENT_ID"] > 0) $arDocElements[] = $arDocElement;
							$arUpdateValues["TOTAL"] += $arDocElement["PURCHASING_PRICE"] * $arDocElement["AMOUNT"];
						}
						//$arUpdateValues[$prop] = $arDocElements;
						$tmpProducts = array();
						if(!empty($arProperty[$prop."_"."ELEMENT_ID"]))
						{
							$rsProps = SMProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $arProperty[$prop."_"."ELEMENT_ID"]), false, false, array("ID", "PRODUCT_ID", "SHELF_LIFE"));
							while($arProp = $rsProps->GetNext())
								$tmpProducts[$arProp["PRODUCT_ID"]] = $arProp["SHELF_LIFE"];
						}
						foreach ($arDocElements as $keyDoc => $arDocElement) 
						{
							if(!empty($tmpProducts[$arDocElement["ELEMENT_ID"]]))
							{
								$arDocElements[$keyDoc]["END_DATE"] = ConvertTimeStamp((empty($arDocElement["START_DATE"]) ? time() : strtotime($arDocElement["START_DATE"])) + floatval($tmpProducts[$arDocElement["ELEMENT_ID"]])*24*60*60, "SHORT");
							}
						}
						
					}
					else
						$arUpdateValues[$prop] = is_array($arProperty) ? $arProperty[0] : $arProperty;

				$oElement = new CCatalogDocs();
				$DOC_ID = 0;
				$rsElement = SMDocs::getList(array("ID" => "ASC"), array("NUMBER_DOCUMENT" => $arUpdateValues["NUMBER_DOCUMENT"]), false, false, array("DOC_ID"));
				if($arElement = $rsElement->Fetch())
				{
					$doc_date = strtotime($arUpdateValues["DATE_DOCUMENT"]);
					$doc_curr = ConvertTimeStamp($doc_date, "SHORT");
					$doc_next = ConvertTimeStamp($doc_date + 24*60*60, "SHORT");
					$rsCElement = CCatalogDocs::getList(array("ID" => "ASC"), array(">=DATE_DOCUMENT" => $doc_curr, "<DATE_DOCUMENT" => $doc_next, "DOC_TYPE" => "A"), false, false, array("ID"));
					if($arCElement = $rsCElement->Fetch())
						$DOC_ID = $arCElement["ID"];
				}


				// update existing element
				if ($DOC_ID > 0)
				{
					$sAction = "EDIT";

					if (!$res = $oElement->Update($DOC_ID, $arUpdateValues))
					{
						$arResult["ERRORS"][] = $oElement->LAST_ERROR;
					}
				}
				// add new element
				else
				{
					$sAction = "ADD";

					if (!$DOC_ID = $oElement->Add($arUpdateValues))
					{
						$arResult["ERRORS"][] = $oElement->LAST_ERROR;
						//print_p($arUpdateValues);
					}

					if (!empty($_REQUEST["apply"]) && strlen($SEF_URL) > 0)
					{
						if (strpos($SEF_URL, "?") === false) $SEF_URL .= "?edit=Y";
						elseif (strpos($SEF_URL, "edit=") === false) $SEF_URL .= "&edit=Y";
						$SEF_URL .= "&CODE=".$DOC_ID;
					}
				}

				if ($DOC_ID > 0 && !empty($arDocElements))
				{
					$elem_amount = array();
					$doc_elem_ids = array();
					$idtoelem = array();
					$idtoamount = array();
					$dbElement = CCatalogStoreDocsElement::getList(array(), array("DOC_ID" => $DOC_ID), false, false, array("ID", "ELEMENT_ID"));
					while($tmpElement = $dbElement->Fetch())
					{
						$doc_elem_ids[] = $tmpElement["ID"];
						$idtoelem[$tmpElement["ID"]] = $tmpElement["ELEMENT_ID"];
					}
					if(!empty($doc_elem_ids))
					{
						$dbElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("DOCS_ELEMENT_ID" => $doc_elem_ids));
						while($tmpElement = $dbElement->Fetch())
							$idtoamount[$tmpElement["DOCS_ELEMENT_ID"]] = $tmpElement["DOC_AMOUNT"];

						foreach($doc_elem_ids as $doc_elem_id) 
							$elem_amount[$idtoelem[$doc_elem_id]][$idtoamount[$doc_elem_id]] = $doc_elem_id;
					}

					foreach($arDocElements as $keyDocElement => $arDocElement)
					{
						if($elem_amount[$arDocElement["ELEMENT_ID"]][$arDocElement["DOC_AMOUNT"]] > 0)
							$arDocElements[$keyDocElement]["ID"] = $elem_amount[$arDocElement["ELEMENT_ID"]][$arDocElement["DOC_AMOUNT"]];
						else
							unset($arDocElements[$keyDocElement]["ID"]);
					}
				}

				if ($DOC_ID > 0 && !empty($arDocElements))
				{
					$oDocElement = new CCatalogStoreDocsElement();
					$arNewElements = $arUpdateElements = array();

					foreach($arDocElements as $arDocElement)
						if(empty($arDocElement["ID"]))
							$arNewElements[] = $arDocElement;
						elseif($arDocElement["ELEMENT_ID"] > 0)
							$arUpdateElements[$arDocElement["ID"]] = $arDocElement;

					$arLastElements = array();
					$dbElement = $oDocElement->getList(array(), array("DOC_ID" => $DOC_ID));
					while($arElement = $dbElement->Fetch())
						$arLastElements[] = $arElement;


					foreach ($arLastElements as $arLastElement) 
						if(array_key_exists($arLastElement["ID"], $arUpdateElements))
							$oDocElement->update($arLastElement["ID"], $arUpdateElements[$arLastElement["ID"]]);
						else
							$oDocElement->delete($arLastElement["ID"]);

					foreach($arNewElements as $arNewElement)
					{
						unset($arNewElement["ID"]);
						$arNewElement["DOC_ID"] = $DOC_ID;
						$oDocElement->add($arNewElement);
					}

					//update products
					$arProductStoreUpdates = array();
					foreach ($arLastElements as $arLastElement) 
					{
						$delta_amount = 0;
						if(array_key_exists($arLastElement["ID"], $arUpdateElements))
							$delta_amount = $arUpdateElements[$arLastElement["ID"]]["AMOUNT"] - $arLastElement["AMOUNT"];
						else
							$delta_amount = 0 - $arLastElement["AMOUNT"];

						
						$arProductStoreUpdates[$arLastElement["ELEMENT_ID"]] = array(
							"ELEMENT_ID" => $arLastElement["ELEMENT_ID"],
							"DELTA_AMOUNT" => $arProductStoreUpdates[$arLastElement["ELEMENT_ID"]]["DELTA_AMOUNT"] + $delta_amount,
							"PURCHASING_PRICE" => $arUpdateElements[$arLastElement["ID"]]["PURCHASING_PRICE"],
							"SHOP_PRICE" => $arUpdateElements[$arLastElement["ID"]]["SHOP_PRICE"],
						);
					}
					foreach($arNewElements as $arNewElement)
					{
						//if($arNewElement["AMOUNT"] > 0)
							$arProductStoreUpdates[$arNewElement["ELEMENT_ID"]] = array(
								"ELEMENT_ID" => $arNewElement["ELEMENT_ID"],
								"DELTA_AMOUNT" => $arNewElement["AMOUNT"],
								"PURCHASING_PRICE" => $arNewElement["PURCHASING_PRICE"],
								"SHOP_PRICE" => $arNewElement["SHOP_PRICE"],
							);
					}

					if(!empty($arProductStoreUpdates))
					{
						//update products purchasing price
						foreach ($arProductStoreUpdates as $arProductStoreUpdate) 
							if($arProductStoreUpdate["PURCHASING_PRICE"] > 0)
								CCatalogProduct::Update($arProductStoreUpdate["ELEMENT_ID"], array("PURCHASING_PRICE" => $arProductStoreUpdate["PURCHASING_PRICE"]));
						
						//update producs store price
						$price_products_id = array();
						foreach($arProductStoreUpdates as $arProductStoreUpdate) 
							if($arProductStoreUpdate["SHOP_PRICE"] > 0)
								$price_products_id[] = $arProductStoreUpdate["ELEMENT_ID"];
						if(!empty($price_products_id))
						{
							$oProductStore = new CPrice();
							$dbProductStore = $oProductStore->getList(
								array(), 
								array(
									"PRODUCT_ID" => $price_products_id,
									"CATALOG_GROUP_ID" => SMShops::getUserPrice()
								),
								false,
								false,
								array("ID", "PRODUCT_ID", "CATALOG_GROUP_ID", "PRICE")
							);
							while($arProductStore = $dbProductStore->Fetch())
							{
								$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["ID"] = $arProductStore["ID"];
								$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["PRICE"] = $arProductStore["PRICE"];
								$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["CATALOG_GROUP_ID"] = $arProductStore["CATALOG_GROUP_ID"];
							}
							foreach ($price_products_id as $product_id)
							{
								$arProductStoreFields = array(
									"PRODUCT_ID" => $arProductStoreUpdates[$product_id]["ELEMENT_ID"],
									"CATALOG_GROUP_ID" => SMShops::getUserPrice(),
									"PRICE" => $arProductStoreUpdates[$product_id]["SHOP_PRICE"],
									"CURRENCY" => "RUB",
								);
								if(!empty($arProductStoreUpdates[$product_id]["ID"]))
									$arProductStoreUpdates[$product_id]["ID"] = $oProductStore->Update($arProductStoreUpdates[$product_id]["ID"], $arProductStoreFields);
								else
									$arProductStoreUpdates[$product_id]["ID"] = $oProductStore->Add($arProductStoreFields);
								
							}
						}
					}
				}

				$formbreginfo = simplexml_load_string($arResult["ELEMENT"]["FORMBREGINFO"]);
				$arResult["ACT_DATA"]["WBRegId"] = (string) $formbreginfo->Document->TTNInformBReg->Header->WBRegId;

				if(!empty($arResult["ACT_DATA"]["Positions"]))
					foreach ($formbreginfo->Document->TTNInformBReg->Content->Position as $position) 
					{
						$identity = (string) $position->Identity;
						if($arResult["ACT_DATA"]["Positions"][$identity] > 0)
							$arResult["ACT_DATA"]["Positions"][$identity] = array(
								"Identity" => $identity,
								"RealQuantity" => $arResult["ACT_DATA"]["Positions"][$identity],
								"InformBRegId" => (string) $position->InformBRegId,
							);
					}
				SMEGAISWaybill::sendWayBillAct($arResult["ACT_DATA"]);
			}


			/*$arDocElements = array();

			$arProperties = $_REQUEST["PROPERTY"];
			$arNum = array("ELEMENT" => array("ELEMENT_DOC_AMOUNT", "ELEMENT_AMOUNT", "ELEMENT_SHOP_PRICE", "ELEMENT_PURCHASING_PRICE", "ELEMENT_PURCHASING_NDS", "ELEMENT_PURCHASING_SUMM"), "TOTAL_FACT");
			$arProperties = floatCase($arProperties, $arNum);

			$arUpdateValues = array(
				"CURRENCY" => "RUB",
				"TOTAL" => 0,
				"MODIFIED_BY" => $USER->GetId(),
				"DOC_TYPE" => "A",
				"SITE_ID" => SITE_ID,
				"STATUS" => "Y",
				"STORE_TO" => SMShops::getUserShop()
			);

			foreach($arProperties as $prop => $arProperty)
				if($prop == "ELEMENT")
				{
					//$arDocElements = array();
					$elemsKeys = array_keys($arProperty[$prop."_"."ELEMENT_ID"]);
					$elemsPropKeys = array_keys($arProperty);
					foreach($elemsKeys as $elemKey)
					{
						$arDocElement = array();
						foreach($elemsPropKeys as $elemsPropKey)
						{
							$elemProp = stripos($elemsPropKey, $prop."_") === 0 ? substr($elemsPropKey, strlen($prop."_")) : $elemsPropKey;
							$arDocElement[$elemProp] = $arProperty[$elemsPropKey][$elemKey];
						}
						$arDocElement["STORE_TO"] = SMShops::getUserShop();
						if($arDocElement["PURCHASING_SUMM"] > 0) 
							$arDocElement["PURCHASING_PRICE"] = $arDocElement["PURCHASING_SUMM"] * 100 / (100 + $arDocElement["PURCHASING_NDS"]) / $arDocElement["DOC_AMOUNT"];
						if($arDocElement["ELEMENT_ID"] > 0) $arDocElements[] = $arDocElement;
						$arUpdateValues["TOTAL"] += $arDocElement["PURCHASING_PRICE"] * $arDocElement["AMOUNT"];
					}
					//$arUpdateValues[$prop] = $arDocElements;
					$tmpProducts = array();
					if(!empty($arProperty[$prop."_"."ELEMENT_ID"]))
					{
						$rsProps = SMProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $arProperty[$prop."_"."ELEMENT_ID"]), false, false, array("ID", "PRODUCT_ID", "SHELF_LIFE"));
						while($arProp = $rsProps->GetNext())
							$tmpProducts[$arProp["PRODUCT_ID"]] = $arProp["SHELF_LIFE"];
					}
					foreach ($arDocElements as $keyDoc => $arDocElement) 
					{
						if(!empty($tmpProducts[$arDocElement["ELEMENT_ID"]]))
						{
							$arDocElements[$keyDoc]["END_DATE"] = ConvertTimeStamp((empty($arDocElement["START_DATE"]) ? time() : strtotime($arDocElement["START_DATE"])) + floatval($tmpProducts[$arDocElement["ELEMENT_ID"]])*24*60*60, "SHORT");
						}
					}
				}
				else
					$arUpdateValues[$prop] = is_array($arProperty) ? $arProperty[0] : $arProperty;

			//$arUpdatePropertyValues = array();

			// update existing element
			$oElement = new CCatalogDocs();
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

			if ($arParams["ID"] > 0 && !empty($arDocElements))
			{
				$oDocElement = new CCatalogStoreDocsElement();
				$arNewElements = $arUpdateElements = array();

				foreach($arDocElements as $arDocElement)
					if(empty($arDocElement["ID"]))
						$arNewElements[] = $arDocElement;
					elseif($arDocElement["ELEMENT_ID"] > 0)
						$arUpdateElements[$arDocElement["ID"]] = $arDocElement;

				$arLastElements = array();
				$dbElement = $oDocElement->getList(array(), array("DOC_ID" => $arParams["ID"]));
				while($arElement = $dbElement->Fetch())
					$arLastElements[] = $arElement;


				foreach ($arLastElements as $arLastElement) 
					if(array_key_exists($arLastElement["ID"], $arUpdateElements))
						$oDocElement->update($arLastElement["ID"], $arUpdateElements[$arLastElement["ID"]]);
					else
						$oDocElement->delete($arLastElement["ID"]);

				foreach($arNewElements as $arNewElement)
				{
					unset($arNewElement["ID"]);
					$arNewElement["DOC_ID"] = $arParams["ID"];
					$oDocElement->add($arNewElement);
				}

				//update products
				$arProductStoreUpdates = array();
				foreach ($arLastElements as $arLastElement) 
				{
					$delta_amount = 0;
					if(array_key_exists($arLastElement["ID"], $arUpdateElements))
						$delta_amount = $arUpdateElements[$arLastElement["ID"]]["AMOUNT"] - $arLastElement["AMOUNT"];
					else
						$delta_amount = 0 - $arLastElement["AMOUNT"];

					
					$arProductStoreUpdates[$arLastElement["ELEMENT_ID"]] = array(
						"ELEMENT_ID" => $arLastElement["ELEMENT_ID"],
						"DELTA_AMOUNT" => $arProductStoreUpdates[$arLastElement["ELEMENT_ID"]]["DELTA_AMOUNT"] + $delta_amount,
						"PURCHASING_PRICE" => $arUpdateElements[$arLastElement["ID"]]["PURCHASING_PRICE"],
						"SHOP_PRICE" => $arUpdateElements[$arLastElement["ID"]]["SHOP_PRICE"],
					);
				}
				foreach($arNewElements as $arNewElement)
				{
					if($arNewElement["AMOUNT"] > 0)
						$arProductStoreUpdates[$arNewElement["ELEMENT_ID"]] = array(
							"ELEMENT_ID" => $arNewElement["ELEMENT_ID"],
							"DELTA_AMOUNT" => $arNewElement["AMOUNT"],
							"PURCHASING_PRICE" => $arNewElement["PURCHASING_PRICE"],
							"SHOP_PRICE" => $arNewElement["SHOP_PRICE"],
						);
				}

				if(!empty($arProductStoreUpdates))
				{
					//update products purchasing price
					foreach ($arProductStoreUpdates as $arProductStoreUpdate) 
						if($arProductStoreUpdate["PURCHASING_PRICE"] > 0)
							CCatalogProduct::Update($arProductStoreUpdate["ELEMENT_ID"], array("PURCHASING_PRICE" => $arProductStoreUpdate["PURCHASING_PRICE"]));
					
					//update producs store price
					$price_products_id = array();
					foreach($arProductStoreUpdates as $arProductStoreUpdate) 
						if($arProductStoreUpdate["SHOP_PRICE"] > 0)
							$price_products_id[] = $arProductStoreUpdate["ELEMENT_ID"];
					if(!empty($price_products_id))
					{
						$oProductStore = new CPrice();
						$dbProductStore = $oProductStore->getList(
							array(), 
							array(
								"PRODUCT_ID" => $price_products_id,
								"CATALOG_GROUP_ID" => SMShops::getUserPrice()
							),
							false,
							false,
							array("ID", "PRODUCT_ID", "CATALOG_GROUP_ID", "PRICE")
						);
						while($arProductStore = $dbProductStore->Fetch())
						{
							$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["ID"] = $arProductStore["ID"];
							$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["PRICE"] = $arProductStore["PRICE"];
							$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["CATALOG_GROUP_ID"] = $arProductStore["CATALOG_GROUP_ID"];
						}
						foreach ($price_products_id as $product_id)
						{
							$arProductStoreFields = array(
								"PRODUCT_ID" => $arProductStoreUpdates[$product_id]["ELEMENT_ID"],
								"CATALOG_GROUP_ID" => SMShops::getUserPrice(),
								"PRICE" => $arProductStoreUpdates[$product_id]["SHOP_PRICE"],
								"CURRENCY" => "RUB",
							);
							if(!empty($arProductStoreUpdates[$product_id]["ID"]))
								$arProductStoreUpdates[$product_id]["ID"] = $oProductStore->Update($arProductStoreUpdates[$product_id]["ID"], $arProductStoreFields);
							else
								$arProductStoreUpdates[$product_id]["ID"] = $oProductStore->Add($arProductStoreFields);
							
						}
					}
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
			}*/

		}



		//prepare data for form
		if ($arParams["ID"] > 0)
		{
			$arResult["ELEMENT"] = array();

			$arResult["ELEMENT"] = $arElement = SMEGAISWaybill::getList(array("ID" => "ASC"), array("ID" => $arParams["ID"]))->Fetch();
			foreach($arElement as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}
			$waybill = simplexml_load_string($arElement["WAYBILL"]);
			$arResult["ELEMENT"]["TOTAL_SUMM"] = $arResult["ELEMENT"]["TOTAL_FACT"] = 0;
			$arResult["ELEMENT"]["NUMBER_DOCUMENT"] = $arResult["ELEMENT"]["NUMBER"];
			$arResult["ELEMENT"]["DATE_DOCUMENT"] = $arResult["ELEMENT"]["DATE"];
			$arResult["ELEMENT"]["CONTRACTOR_NAME"] = (string) $waybill->Document->WayBill->Header->Shipper->FullName;
			$alcprod = $alccodes = array();
			foreach ($waybill->Document->WayBill->Content->Position as $key => $position) 
				$alccodes[] = (string) $position->Product->AlcCode;
			if(!empty($alccodes))
			{
				$dbResultList = SMEGAISAlcCode::getList(array(), array("ALCCODE" => $alccodes));
				while($arFields = $dbResultList->Fetch())
					$alcprod[$arFields["ALCCODE"]] = $arFields["PRODUCT_ID"];
			}

			$save_elem_id = array();
			if(is_array($_REQUEST["PROPERTY"]["ELEMENT"]["ELEMENT_ELEMENT_ID"]))
				foreach($_REQUEST["PROPERTY"]["ELEMENT"]["ELEMENT_ELEMENT_ID"] as $elem_key => $elem_id) 
					if($elem_id > 0)
						$save_elem_id[$_REQUEST["PROPERTY"]["ELEMENT"]["ID"][$elem_key]] = $elem_id;

			$rsElement = SMDocs::getList(array("ID" => "ASC"), array("NUMBER_DOCUMENT" => $arResult["ELEMENT"]["NUMBER_DOCUMENT"]), false, false, array("DOC_ID"));
			if($arElement = $rsElement->Fetch())
			{
				$doc_date = strtotime($arResult["ELEMENT"]["DATE_DOCUMENT"]);
				$doc_curr = ConvertTimeStamp($doc_date, "SHORT");
				$doc_next = ConvertTimeStamp($doc_date + 24*60*60, "SHORT");
				$rsCElement = CCatalogDocs::getList(array("ID" => "ASC"), array(">=DATE_DOCUMENT" => $doc_curr, "<DATE_DOCUMENT" => $doc_next, "DOC_TYPE" => "A"), false, false, array("ID"));
				if($arCElement = $rsCElement->Fetch())
					$DOC_ID = $arCElement["ID"];
			}

			//if ($DOC_ID > 0 && !empty($arDocElements))
			{
				$elem_amount = array();
				$doc_elem_ids = array();
				$idtoelem = array();
				$idtodamount = array();
				$idtoamount = array();
				$dbElement = CCatalogStoreDocsElement::getList(array(), array("DOC_ID" => $DOC_ID), false, false, array("ID", "ELEMENT_ID", "AMOUNT"));
				while($tmpElement = $dbElement->Fetch())
				{
					$doc_elem_ids[] = $tmpElement["ID"];
					$idtoelem[$tmpElement["ID"]] = $tmpElement["ELEMENT_ID"];
					$idtoamount[$tmpElement["ID"]] = $tmpElement["AMOUNT"];
				}
				if(!empty($doc_elem_ids))
				{
					$dbElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("DOCS_ELEMENT_ID" => $doc_elem_ids));
					while($tmpElement = $dbElement->Fetch())
						$idtodamount[$tmpElement["DOCS_ELEMENT_ID"]] = $tmpElement["DOC_AMOUNT"];

					foreach($doc_elem_ids as $doc_elem_id) 
						$elem_amount[$idtoelem[$doc_elem_id]][$idtodamount[$doc_elem_id]] = $idtoamount[$doc_elem_id];
				}
			}

			foreach ($waybill->Document->WayBill->Content->Position as $key => $position) 
			{
				$elem_id = IntVal($save_elem_id[IntVal($position->Identity)]);
				$alccode = (string) $position->Product->AlcCode;
				$amount = floatval($position->Quantity);
				$doc_amount = floatval($position->Quantity);
				if($elem_id > 0)
				{
					$alcprod[$alccode] = $elem_id;
				}
				if($elem_amount[$alcprod[$alccode]][$doc_amount] > 0)
					$amount = $elem_amount[$alcprod[$alccode]][$doc_amount];
				$arPosition = array(
					"ELEMENT_ID" => IntVal($position->Identity),
					"ELEMENT_ELEMENT_ID" => $elem_id > 0 ? $elem_id : $alcprod[$alccode],
					"ELEMENT_ELEMENT_NAME" => (string) $position->Product->FullName,
					"ELEMENT_DOC_AMOUNT" => $doc_amount,
					"ELEMENT_AMOUNT" => $amount,
					"ELEMENT_PURCHASING_PRICE" => floatval($position->Price), 
				);
				$arPosition["ELEMENT_PURCHASING_SUMM"] = $arPosition["ELEMENT_DOC_AMOUNT"] * $arPosition["ELEMENT_PURCHASING_PRICE"];
				$arResult["ELEMENT"]["TOTAL_SUMM"] += $arPosition["ELEMENT_PURCHASING_SUMM"];
				$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["ENUM"][] = $arPosition;
			}
			$arResult["ELEMENT"]["TOTAL_FACT"] = $arResult["ELEMENT"]["TOTAL_SUMM"];

			if(!empty($alcprod))
			{
				$product_id = array();
				$arProducts = array();
				$rsElement = CIBlockElement::GetList(Array(), array("ID" => $alcprod), false, false, array("ID", "NAME"));
				while($arFields = $rsElement->Fetch())
				{
					$product_id[] = $arFields["ID"];
					$arProducts[$arFields["ID"]] = $arFields;
					$arProducts[$arFields["ID"]]["BARCODES"] = array();
				}
				if(!empty($product_id))
				{
					$rsBarCode = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $product_id), false, false, array("PRODUCT_ID", "BARCODE"));
					while($arBarCode = $rsBarCode->Fetch())		
						$arProducts[$arBarCode["PRODUCT_ID"]]["BARCODES"][] = $arBarCode["BARCODE"];
				}
				foreach ($arProducts as $arProduct) 
					$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["arResult"]["PROPERTY_LIST_FULL"]["ELEMENT_ELEMENT_ID"]["ENUM"][$arProduct["ID"]] = array("VALUE" => $arProduct["NAME"].(!empty($arProduct["BARCODES"]) ? " [".implode(", ", $arProduct["BARCODES"])."]" : ""));
			}

			//waybill act
			$arResult["ACT_ITEMS"] = array();
			$formbreginfo = simplexml_load_string($arResult["ELEMENT"]["~FORMBREGINFO"]);
			$WBRegId = $formbreginfo->Document->TTNInformBReg->Header->WBRegId;
			$ReplyID = array();
			$rsWB = SMEGAISWaybillAct::getList(array(), array("~XML" => "%<WBRegId>".$WBRegId."</WBRegId>%", "DOCUMENT" => "WayBillAct"), false, false, array("ID", "REPLY_ID"));
			while($arWB = $rsWB->Fetch())
				$ReplyID[] = $arWB["REPLY_ID"];
			if(!empty($ReplyID))
			{
				$rsWB = SMEGAISWaybillAct::getList(array("DATE" => "ASC"), array("REPLY_ID" => $ReplyID));
				while($arWB = $rsWB->Fetch())
				{
					$doc_xml = simplexml_load_string($arWB["XML"]);
					$arWB["NOTE"] = $arWB["DOCUMENT"] == "WayBillAct" ? (string) $doc_xml->Document->WayBillAct->Header->Note : ($arWB["DOCUMENT"] == "WAYBILL" ? (string) $doc_xml->Document->Ticket->OperationResult->OperationComment : (string) $doc_xml->Document->Ticket->Result->Comments);
					unset($arWB["XML"]);
					$arResult["ACT_ITEMS"][] = $arWB;
				}
			}

			$contractor = $waybill->Document->WayBill->Header->Shipper;
			if($arContractor = CCatalogContractor::getList(array("ID" => "ASC"), array("INN" => (string) $contractor->INN), false, false, array("ID"))->Fetch())
			{
				$arResult["ELEMENT"]["CONTRACTOR_ID"] = $arContractor["ID"];
			}
			else
			{
				$arContractor = array(
					"PERSON_TYPE" => 2,
					"PERSON_NAME" => (string) $contractor->ShortName,
					"COMPANY" => (string) $contractor->FullName,
					"INN" => (string) $contractor->INN,
					"KPP" => (string) $contractor->KPP,
					"ADDRESS" => (string) $contractor->address->description,
					"MODIFIED_BY" => $USER->GetId(),
				);
				$arContractor["ID"] = SMContractor::Add($arContractor);
				$arResult["ELEMENT"]["CONTRACTOR_ID"] = $arContractor["ID"];
			}


			//print_p($arResult);

			/*$arElement["CONTRACTOR_ID"] = array(
				array("VALUE" => $arElement["CONTRACTOR_ID"])
			);
			foreach($arElement as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}
			//SMDocs::add(array("DOC_ID" => $arParams["ID"], "NUMBER_DOCUMENT" => "123", "TOTAL_FACT" => "777"));
			$arElement = SMDocs::getList(array("ID" => "ASC"), array("DOC_ID" => $arParams["ID"]))->Fetch();
			unset($arElement["ID"], $arElement["DOC_ID"]);
			foreach($arElement as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}

			$arResult["ELEMENT"]["TOTAL_SUMM"] = 0;
			foreach($arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["ENUM"] as $ekey => $eval)
			{
				$arResult["ELEMENT"]["TOTAL_SUMM"] += $eval["ELEMENT_PURCHASING_SUMM"];
			}

			$arNum = array("ELEMENT" => array("ELEMENT_SHOP_PRICE", "ELEMENT_PURCHASING_PRICE", "ELEMENT_PURCHASING_SUMM"), "TOTAL_FACT", "TOTAL_SUMM");
			foreach($arNum as $key => $val)
			{
				if(is_array($val))
				{
					if($key == "ELEMENT" && is_array($arResult["PROPERTY_LIST_FULL"][$key]["ENUM"]))
					{
						foreach ($val as $vkey => $vval) 
						{
							foreach($arResult["PROPERTY_LIST_FULL"][$key]["ENUM"] as $ekey => $eval)
							{
								$arResult["PROPERTY_LIST_FULL"][$key]["ENUM"][$ekey][$vval] = number_format($eval[$vval], 2, ".", "");
							}
						}
					}
				}
				else
				{
					$arResult["ELEMENT"][$val] = number_format($arResult["ELEMENT"][$val], 2, ".", "");
				}
			}*/



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