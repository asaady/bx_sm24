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
				break;
			case "ELEMENT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "DOC_ELEMENT",
					"MULTIPLE" => "Y",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"ENUM" => array()
				);
				$tmpPropList = array(
					//"BARCODE",
					"ELEMENT_ID", 
					"PURCHASING_PRICE",
					"MEASURE",
					"AMOUNT", 
					"SHOP_PRICE", 
					"DOC_AMOUNT",
					"PURCHASING_NDS", 
					"PURCHASING_SUMM", 
					"NDS_VALUE",
					"START_DATE", 
					//"SHELF_LIFE", 
					//"END_DATE", 
				);
				foreach($tmpPropList as $tmpProp)
					$arPropListFull["arResult"]["PROPERTY_LIST"][] = $prop."_".$tmpProp;
				foreach($arPropListFull["arResult"]["PROPERTY_LIST"] as $propertyID) 
					$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
				$arPropListFull["arResult"]["PROPERTY_LIST_FULL"] = SetInput($arPropListFull["arResult"]["PROPERTY_LIST"], $arParams);
				if($arParams["ID"] > 0)
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
				}
				break;
			case "TOTAL_SUMM":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"DISABLED" => "Y",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
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
			"CONTRACTOR_ID",
			"ELEMENT",
			"TOTAL_SUMM",
			"TOTAL_FACT",
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		if (check_bitrix_sessid() && ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "W") && (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"])))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;

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
					SMInventory::addPrimaryInventoryProductLog(
						array(
							"PRODUCT_ID" => $arNewElement["ELEMENT_ID"],
							"QUANTITY" => $arNewElement["AMOUNT"],
							"SHOP_ID" => SMShops::getUserShop()
							)
						);
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
					//update producs store amount
					/*$amount_products_id = array();
					foreach ($arProductStoreUpdates as $arProductStoreUpdate) 
						if($arProductStoreUpdate["DELTA_AMOUNT"] > 0 || $arProductStoreUpdate["DELTA_AMOUNT"] < 0)
							$amount_products_id[] = $arProductStoreUpdate["ELEMENT_ID"];
					if(!empty($amount_products_id))
					{
						$oProductStore = new CCatalogStoreProduct();
						$dbProductStore = $oProductStore->getList(
							array(), 
							array(
								"PRODUCT_ID" => $amount_products_id,
								"STORE_ID" => SMShops::getUserShop()
							),
							false,
							false,
							array("ID", "PRODUCT_ID", "STORE_ID", "AMOUNT")
						);
						while($arProductStore = $dbProductStore->Fetch())
						{
							$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["ID"] = $arProductStore["ID"];
							$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["AMOUNT"] = $arProductStore["AMOUNT"];
							$arProductStoreUpdates[$arProductStore["PRODUCT_ID"]]["STORE_ID"] = $arProductStore["STORE_ID"];
						}

						foreach ($amount_products_id as $product_id)
						{
							$arProductStoreFields = array(
								"PRODUCT_ID" => $arProductStoreUpdates[$product_id]["ELEMENT_ID"],
								"STORE_ID" => $arProductStoreUpdates[$product_id]["STORE_ID"],
								"AMOUNT" => $arProductStoreUpdates[$product_id]["AMOUNT"] + $arProductStoreUpdates[$product_id]["DELTA_AMOUNT"],
							);
							if(!empty($arProductStoreUpdates[$product_id]["ID"]))
								$arProductStoreUpdates[$product_id]["ID"] = $oProductStore->Update($arProductStoreUpdates[$product_id]["ID"], $arProductStoreFields);
							else
								$arProductStoreUpdates[$product_id]["ID"] = $oProductStore->Add($arProductStoreFields);
							
						}
					}*/

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
			}

		}



		//prepare data for form
		if ($arParams["ID"] > 0)
		{
			$arResult["ELEMENT"] = array();
			$arElement = CCatalogDocs::getList(array("ID" => "ASC"), array("ID" => $arParams["ID"]))->Fetch();
			$arElement["CONTRACTOR_ID"] = array(
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
				if($_REQUEST["print"] == "Y")
				{
					$rsProps = CCatalogProduct::GetList(array(), array("ID" => $eval["ELEMENT_ELEMENT_ID"]), false, false, array("MEASURE"));
					if($arProp = $rsProps->Fetch())
						$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["ENUM"][$ekey]["ELEMENT_MEASURE"] = $arProp["MEASURE"];

					$rsProps = CIBlockElement::GetList(array(), array("ID" => $eval["ELEMENT_ELEMENT_ID"]), false, false, array("PROPERTY_PACK"));
					if($arProp = $rsProps->Fetch())
						$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["ENUM"][$ekey]["ELEMENT_PACK"] = $arProp["PROPERTY_PACK_VALUE"];

					$arResult["ELEMENT"]["TOTAL_DOC_AMOUNT"] += $eval["ELEMENT_DOC_AMOUNT"];
					$arResult["ELEMENT"]["TOTAL_AMOUNT"] += $eval["ELEMENT_AMOUNT"];
					$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["ENUM"][$ekey]["ELEMENT_PURCHASING_SUMM_WITHOUT_NDS"] = $eval["ELEMENT_PURCHASING_SUMM_WITHOUT_NDS"] = $eval["ELEMENT_PURCHASING_SUMM"] * (100 - floatval($eval["ELEMENT_PURCHASING_NDS"])) / 100;
					$arResult["ELEMENT"]["TOTAL_PURCHASING_SUMM_WITHOUT_NDS"] += $eval["ELEMENT_PURCHASING_SUMM_WITHOUT_NDS"];
				}
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
			}

			$bShowForm = true;
		}
		else
		{
			$bShowForm = true;
		}

		$arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strIMessage"]);
		if($_REQUEST["print"] == "Y")
		{
			$APPLICATION->RestartBuffer();
			$this->IncludeComponentTemplate("print");
			die();
		}
		else
			$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}