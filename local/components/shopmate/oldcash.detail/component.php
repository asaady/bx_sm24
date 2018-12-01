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
use Bitrix\Main\Localization\Loc;
$arParams["componentPath"] = $componentPath;
function SetInput($propList = array(), &$arParams)
{
	$arPropsListFull = array();
	foreach($propList as $prop)
	{
		switch($prop)
		{
			case "USER_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					//"ENUM" => array(),
					"LIST_TYPE" => "AJAX"
				);
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_user.php";
				if($arParams["ID"] > 0)
				{
					$USER_ID = 0;
					$dbElement = CSaleBasket::getList(array(), array("ORDER_ID" => $arParams["ID"]), false, false, array("USER_ID"));
					if($tmpElement = $dbElement->Fetch())
						$USER_ID = $tmpElement["USER_ID"];
				
					$dbPropResultList = CUser::GetList(($by="LAST_NAME"), ($order="asc"), array("ACTIVE" => "Y", "ID" => $USER_ID));
					while ($arPropResult = $dbPropResultList->Fetch())
						$arPropListFull["ENUM"][$arPropResult["ID"]]["VALUE"] = $arPropResult["LAST_NAME"]." ".$arPropResult["NAME"]." [".$arPropResult["LOGIN"]."]";
				}
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
			case "PRODUCT_PRODUCT_ID":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"LIST_TYPE" => "AJAX",
				);
				//$arPropListFull["URL"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", __DIR__."/search_element.php");
				$arPropListFull["URL"] = $arParams["componentPath"]."/search_element.php";
				$arPropListFull["INFO_URL"] = $arParams["componentPath"]."/search_element_info.php";
				break;
			case "PRODUCT_MEASURE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"DISABLED" => "Y",
				);
				$oMeasure = CCatalogMeasure::GetList();
				while($arMeasure = $oMeasure->Fetch())
					$arPropListFull["ENUM"][$arMeasure["ID"]]["VALUE"] = $arMeasure["SYMBOL_RUS"];
				break;
			case "PRODUCT":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "DOC_ELEMENT",
					"MULTIPLE" => "Y",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"],
					"ENUM" => array(),
				);
				if($arParams["ID"] > 0) $arPropListFull["DISABLED"] = "Y";
				$tmpPropList = array(
					"PRODUCT_ID",
					"AMOUNT", 
					"QUANTITY", 
					"MEASURE", 
					"PRICE", 
					"SUMM", 
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
					$dbElement = CSaleBasket::getList(array(), array("ORDER_ID" => $arParams["ID"]));
					while($tmpElement = $dbElement->Fetch())
					{
						$arElement = array();
						foreach($tmpElement as $keyElem => $arElem)
							$arElement[$prop."_".$keyElem] = $arElem;
						$arPropListFull["ENUM"][] = $arElement;
						$arDocsElementIDs[] = $tmpElement["ID"];
						$arEnumElementIDs[] = $tmpElement["PRODUCT_ID"];
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
							$arPropListFull["arResult"]["PROPERTY_LIST_FULL"][$prop."_"."PRODUCT_ID"]["ENUM"][$arProduct["ID"]] = array("VALUE" => $arProduct["NAME"].(!empty($arProduct["BARCODES"]) ? " [".implode(", ", $arProduct["BARCODES"])."]" : ""));
					}
					
					$tmpDocsElements = array();
					$dbElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("DOCS_PRODUCT_ID" => $arDocsElementIDs));
					while($tmpElement = $dbElement->Fetch())
					{
						$docElementId = $tmpElement["DOCS_PRODUCT_ID"];
						unset($tmpElement["ID"], $tmpElement["DOCS_PRODUCT_ID"]);
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
						$arPropListFull["ENUM"][$keyEnum][$prop."_SUMM"] = round($arEnum[$prop."_PRICE"] * $arEnum[$prop."_QUANTITY"], 2);
					}
				}
				break;
			case "PRICE_SUMM":
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

$deviceReady = $APPLICATION->get_cookie("DEVCE_READY");
if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale") && CModule::IncludeModule("yadadya.shopmate") && ($deviceReady == "Y" || SMCashDevice::isCashTest()))
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
			"PRODUCT",
			"USER_ID",
			"PRICE_SUMM",
		);
		foreach($arResult["PROPERTY_LIST"] as $propertyID) 
			$arParams["CUSTOM_TITLE_".$propertyID] = !empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("CUSTOM_TITLE_".$propertyID);
		$arResult["PROPERTY_LIST_FULL"] = SetInput($arResult["PROPERTY_LIST"], $arParams);

		$arResult["PAY_SYSTEM"] = array();
		$arFilter = array(
			"ACTIVE" => "Y",
			//"PERSON_TYPE_ID" => $arUserResult["PERSON_TYPE_ID"],
			"PSA_HAVE_PAYMENT" => "Y"
		);
		$dbPaySystem = CSalePaySystem::GetList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			$arFilter
		);
		while ($arPaySystem = $dbPaySystem->Fetch())
		{
			$arPaySystem["NAME"] = htmlspecialcharsEx($arPaySystem["NAME"]);
			$arResult["PAY_SYSTEM"][$arPaySystem["ID"]] = $arPaySystem;
		}

		if (check_bitrix_sessid() && ($USER->IsAdmin() || SM::GetUserPermission("cash") >= "W") && (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"]) || !empty($_REQUEST["pay_system"]) || !empty($_REQUEST["order_cancel"])))
		{
			$arSave = SMTodo::prepChanges($_REQUEST["PROPERTY"], $_REQUEST["PROPERTY_OLD"]);
			//print_p($arSave);
			//die();
			if(!empty($arSave))
				$TodoID = SMTodoLog::Log(SMShops::getUserShop(), "cash", $arParams["ID"] > 0 ? $arParams["ID"] : 0, serialize($arSave), $USER->IsAdmin() || SM::GetUserPermission("cash") >= "X" ? "Y" : "N");
		}

		if (check_bitrix_sessid() && ($USER->IsAdmin() || SM::GetUserPermission("cash") >= "X") && (!empty($_REQUEST["submit"]) || !empty($_REQUEST["apply"]) || !empty($_REQUEST["pay_system"]) || !empty($_REQUEST["order_cancel"])))
		{
			$SEF_URL = $_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"];
			$arResult["SEF_URL"] = $SEF_URL;

			$arDocElements = array();

			$arProperties = $_REQUEST["PROPERTY"];
			$arNum = array("PRODUCT" => array("PRODUCT_QUANTITY", "PRODUCT_PRICE"));
			$arProperties = floatCase($arProperties, $arNum);

			if(is_array($arProperties["PRODUCT"]["PRODUCT_QUANTITY"]))
				foreach ($arProperties["PRODUCT"]["PRODUCT_QUANTITY"] as $key => $quantity) 
					$arProperties["PRODUCT"]["PRODUCT_QUANTITY"][$key] = floor($quantity * 1000) / 1000;

			$arLastFix = array("PRICE" => 0);

			if(!empty($arParams["ID"]))
				$arLastFix = $arLastOrder = CSaleOrder::GetByID($arParams["ID"]);

			$arDocElements = array();
			$PRODUCT_IDS = array();
			foreach($arProperties as $prop => $arProperty)
				if($prop == "PRODUCT")
				{
					$elemsKeys = array_keys($arProperty[$prop."_"."PRODUCT_ID"]);
					$elemsPropKeys = array_keys($arProperty);
					foreach($elemsKeys as $elemKey)
					{
						$arDocElement = array();
						foreach($elemsPropKeys as $elemsPropKey)
						{
							$elemProp = stripos($elemsPropKey, $prop."_") === 0 ? substr($elemsPropKey, strlen($prop."_")) : $elemsPropKey;
							$arDocElement[$elemProp] = $arProperty[$elemsPropKey][$elemKey];
						}
						if($arDocElement["PRODUCT_ID"] > 0) 
						{
							$arDocElements[] = $arDocElement;
							if(!in_array($arDocElement["PRODUCT_ID"], $PRODUCT_IDS)) $PRODUCT_IDS[] = $arDocElement["PRODUCT_ID"];
						}
					}
				}
				else
					$arUpdateValues[$prop] = is_array($arProperty) ? $arProperty[0] : $arProperty;

			if(empty($arUpdateValues["USER_ID"])) 
					$arUpdateValues["USER_ID"] = SMUser::SimpleBuyerAdd();

			$arOrder = array(
				"ORDER_PRICE" => 0,
				"CURRENCY" => "RUB",
				"BASKET_ITEMS" => Array(),
				"SITE_ID" => SITE_ID,
				"LID" => SITE_ID,
				"USER_ID" => $arUpdateValues["USER_ID"],
				"PERSON_TYPE_ID" => 1,
				"PRICE" => 0,
			);
			if(!empty($_REQUEST["pay_system"])) $arOrder["PAY_SYSTEM_ID"] = $_REQUEST["pay_system"];

			if(!empty($PRODUCT_IDS))
			{
				if($arParams["ID"] > 0)
				{
					$arBasketItems = array();
					$dbBasketItems = CSaleBasket::GetList(array("ID" => "DESC"), array("ORDER_ID" => $arParams["ID"]));
					while ($arBasketItem = $dbBasketItems->Fetch())
						$arBasketItems[$arBasketItem["ID"]] = $arBasketItem;
					foreach($arDocElements as $arDocElement)
					{
						$arBasketItem = $arBasketItems[$arDocElement["ID"]];
						$arBasketItem["QUANTITY"] = $arDocElement["QUANTITY"] > $arBasketItem["QUANTITY"] ? $arBasketItem["QUANTITY"] : $arDocElement["QUANTITY"];
						$arOrder["BASKET_ITEMS"][] = $arBasketItem;
						$arOrder["PRICE"] += round($arBasketItem["PRICE"] * $arBasketItem["QUANTITY"], 2);
					}
					$arOrder["BASKET_ITEMS"][] = $arBasketItem;
				}
				else
				{
					$arElements = array();
					$res = CIBlockElement::GetList(Array(), array("ID" => $PRODUCT_IDS), false, false, Array("ID", "NAME"));
					while($arElement = $res->Fetch())
						$arElements[$arElement["ID"]] = $arElement;  

					$arPrices = array();

					$arUserGroups = array();

					$res = CUser::GetUserGroupList(empty($arUpdateValues["USER_ID"]) ? false : $arUpdateValues["USER_ID"]);
					while ($arGroup = $res->Fetch())
						$arUserGroups[] = $arGroup["GROUP_ID"];

					//$arPriceGroups = CCatalogGroup::GetGroupsPerms($arUserGroups, array());

					$arFilter = array("PRODUCT_ID" => $PRODUCT_IDS);
					$arFilter["CATALOG_GROUP_ID"] = SMShops::getUserPrice();

					$dbPrice = CPrice::GetListEx(
						array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
						$arFilter,
						false,
						false,
						array("ID", "PRODUCT_ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
					);

					while ($arPrice = $dbPrice->Fetch())
					{
						CCatalogDiscountSave::Disable();
						$arDiscounts = CCatalogDiscount::GetDiscount($arPrice["PRODUCT_ID"], 2, $arPrice["CATALOG_GROUP_ID"], $arUserGroups, "N", SITE_ID);
						CCatalogDiscountSave::Enable();
						$arPrice["DISCOUNT_PRICE"] = empty($arDiscounts) ? $arPrice['PRICE'] : CCatalogProduct::CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);

						if($arPrice["DISCOUNT_PRICE"] > 0 && ($arPrices[$arPrice["PRODUCT_ID"]]["DISCOUNT_PRICE"] <= 0 || $arPrice["DISCOUNT_PRICE"] < $arPrices[$arPrice["PRODUCT_ID"]]["DISCOUNT_PRICE"]))
							$arPrices[$arPrice["PRODUCT_ID"]] = $arPrice;
					}

					$arAmount = array();
					$rsProps = CCatalogStoreProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $PRODUCT_IDS, "STORE_ID" => SMShops::getUserShop()), false, false, array("PRODUCT_ID", "AMOUNT"));
					if($arProp = $rsProps->GetNext())
						$arAmount[$arProp["PRODUCT_ID"]] = $arProp["AMOUNT"];
					foreach($arDocElements as $arDocElement) 
					{
						if($arDocElement["QUANTITY"] > $arAmount[$arProp["PRODUCT_ID"]])
							$arResult["ERRORS"][] = Loc::getMessage("DDCT_DEDUCTION_QUANTITY_STORE_ERROR", array("#PRODUCT_NAME#" => $arElements[$arDocElement["PRODUCT_ID"]]["NAME"], "#PRODUCT_ID#" => $arDocElement["PRODUCT_ID"], "#STORE_ID#" => SMShops::getUserShop()));
					}

					$undefinedProducts = SMStoreBarcode::getUndefinedProducts();

					foreach($arDocElements as $arDocElement) 
					{
						$arBasketItem = array(
							"PRODUCT_PROVIDER_CLASS" => "CCatalogProductProvider",
							"CURRENCY" => $arPrices[$arDocElement["PRODUCT_ID"]]["CURRENCY"],
							"DISCOUNT_PRICE" => $arPrices[$arDocElement["PRODUCT_ID"]]["PRICE"] - $arPrices[$arDocElement["PRODUCT_ID"]]["DISCOUNT_PRICE"],
							"MODULE" => "catalog",
							"NAME" => $arElements[$arDocElement["PRODUCT_ID"]]["NAME"],
							"PRICE_DEFAULT" => $arPrices[$arDocElement["PRODUCT_ID"]]["DISCOUNT_PRICE"],
							"PRODUCT_ID" => $arDocElement["PRODUCT_ID"],
							"CUSTOM_PRICE" => "N",
							"QUANTITY" => $arDocElement["QUANTITY"],
							"PRICE" => $arPrices[$arDocElement["PRODUCT_ID"]]["DISCOUNT_PRICE"],
							"QUANTITY_DEFAULT" => $arDocElement["QUANTITY"],
							"LID" => SITE_ID,
							"CAN_BUY" => "Y",
							"PRODUCT_PRICE_ID" => $arPrices[$arDocElement["PRODUCT_ID"]]["ID"],
							"BASE_PRICE" => $arPrices[$arDocElement["PRODUCT_ID"]]["PRICE"],
							"SUBSCRIBE" => "N",
							"ID" => $arDocElement["ID"]
						);
						if($arDocElement["ID"] > 0)
						{
							$arBasketItem["BASKET_ID"] = $arDocElement["ID"];
							$arBasketItem["RESERVED"] = "Y";
						}
						else
							$arBasketItem["NEW_PRODUCT"] = "NEW_PRODUCT";

						if(in_array($arDocElement["PRODUCT_ID"], $undefinedProducts))
						{
							$arBasketItem["CUSTOM_PRICE"] = "Y";
							$arBasketItem["PRICE_DEFAULT"] = $arBasketItem["PRICE"] = $arBasketItem["BASE_PRICE"] = $arDocElement["PRICE"];
							$arBasketItem["DISCOUNT_PRICE"] = 0;
							unset($arBasketItem["PRODUCT_PRICE_ID"]);
						}
						$arOrder["BASKET_ITEMS"][] = $arBasketItem;
						$arOrder["PRICE"] += round($arBasketItem["PRICE"] * $arBasketItem["QUANTITY"], 2);
					}
				}
				//$arOrder["PRICE"] = ceil($arOrder["PRICE"] * 100) / 100;
				$arOrder["ORDER_PRICE"] = $arOrder["PRICE"];
			}
			else
			{
				$arResult["ERRORS"][] = "Нет товаров";
			}

			$arAdditionalFields = array("STORE_ID" => SMShops::getUserShop());

			if($arParams["ID"] <= 0) $arAdditionalFields["STATUS_ID"] = "N";

			$ID = $arParams["ID"];

			$arErrors = array();

			if (empty($arResult["ERRORS"]))
			{
				if($ID > 0) CSaleOrder::DeductOrder($ID, "N");

				$ID = $tmpID = CSaleOrder::DoSaveOrder($arOrder, $arAdditionalFields, $ID, $arErrors);
				//CSaleOrder::DeductOrder($ID, "Y");

				if($arParams["ID"] <= 0)
				{
					$dbBasketItems = CSaleBasket::GetList(array("ID" => "DESC"), array("ORDER_ID" => $tmpID), false, false, array("ID", "QUANTITY"));
					while ($arBasketItem = $dbBasketItems->Fetch())
					{
						$arStoreBarcodeFields = array(
							"BASKET_ID"   => $arBasketItem["ID"],
							"BARCODE"     => "",
							"STORE_ID"    => SMShops::getUserShop(),
							"QUANTITY"    => $arBasketItem["QUANTITY"],
							"CREATED_BY"  => $USER->GetId(),
							"MODIFIED_BY" => $USER->GetId()
						);

						CSaleStoreBarcode::Add($arStoreBarcodeFields);
					}
				}
				if($arOrder["DEDUCTED"] == "Y" || $arOrder["CANCELED"] != "Y") 
				{
					$basket_ids = array();
					$dbBasketList = CSaleBasket::GetList(array(), array("ORDER_ID" => $ID), false, false, array('ID'));
					while ($arBasket = $dbBasketList->Fetch())
						$basket_ids[] = $arBasket["ID"];
					if(!empty($basket_ids))
					{
						$arSavedStoreBarcodeData = array();
						$dbStoreBarcode = CSaleStoreBarcode::GetList(
							array(),
							array("BASKET_ID" => $basket_ids),
							false,
							false,
							array("ID", "BASKET_ID", "BARCODE", "QUANTITY", "STORE_ID")
						);
						while ($arStoreBarcode = $dbStoreBarcode->Fetch())
						{
							$arSavedStoreBarcodeData[$arStoreBarcode["BASKET_ID"]][] = $arStoreBarcode;
						}
						CSaleOrder::DeductOrder($ID, "Y", "", false, $arSavedStoreBarcodeData);
					}
				}

				$arParams["ID"] = (int)$tmpID;

				if($arLastFix["PRICE"] > $arOrder["PRICE"] && $arLastFix["CANCELED"] != "Y" || !empty($_REQUEST["order_cancel"]))
				{
					$arCashback = array(
						"Price" => !empty($_REQUEST["order_cancel"]) ? $arLastFix["PRICE"] :  $arLastFix["PRICE"] - $arOrder["PRICE"],
						"AuthorizationCode" => $arLastFix["PAY_VOUCHER_NUM"],
						"RRN" => $arLastFix["REASON_CANCELED"],
					);
					SMTodoLog::Log(SMShops::getUserShop(), "cash_back", $arParams["ID"], serialize($arCashback), "N");
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
		/*elseif (check_bitrix_sessid() && (!empty($_REQUEST["order_cancel"])))
		{

		}*/



		//prepare data for form
		if ($arParams["ID"] > 0)
		{
			$arResult["ORDER"] = CSaleOrder::GetByID($arParams["ID"]);
			//print_p($arResult["ORDER"]);
			// print_p($arResult);
			// print_p(123);

			if(!empty($_REQUEST["strIMessage"]) && !empty($arResult["ORDER"]["PAY_SYSTEM_ID"]))
			{
				if(isset($_REQUEST["cancel_blank"]))
				{
					$APPLICATION->RestartBuffer();
					
					// ob_start();
					$dbPaySysAction = CSalePaySystemAction::GetList(
						array(),
						array(
							"PAY_SYSTEM_ID" => $arResult["ORDER"]["PAY_SYSTEM_ID"],
							"PERSON_TYPE_ID" => "1" //change!
						),
						false,
						false,
						array("ACTION_FILE", "PARAMS", "ENCODING")
					);

					if ($arPaySysAction = $dbPaySysAction->Fetch())
					{
						if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
						{
							CSalePaySystemAction::InitParamArrays($arResult["ORDER"], $arResult["ORDER"]["PAY_SYSTEM_ID"], $arPaySysAction["PARAMS"]);

							$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];
							$pathToAction = rtrim(str_replace("\\", "/", $pathToAction), "/");

							if (file_exists($pathToAction))
							{
								if (is_dir($pathToAction))
								{
									if (file_exists($pathToAction."/cancel.php"))
										include($pathToAction."/cancel.php");
								}
								else
								{
									include($pathToAction);
								}
							}

							if(strlen($arPaySysAction["ENCODING"]) > 0)
							{
								define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
								AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
								function ChangeEncoding($content)
								{
									global $APPLICATION;
									header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
									$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
									$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
								}
							}
						}
					}
					die();
				}
				elseif(isset($_REQUEST["pay_system_blank"]) && $_SESSION["ORDER_PAYMENT"]["ORDER_ID"] == $arResult["ORDER"]["ID"])
				{
					$APPLICATION->RestartBuffer();

					// ob_start();
					$dbPaySysAction = CSalePaySystemAction::GetList(
						array(),
						array(
							"PAY_SYSTEM_ID" => $arResult["ORDER"]["PAY_SYSTEM_ID"],
							"PERSON_TYPE_ID" => "1" //change!
						),
						false,
						false,
						array("ACTION_FILE", "PARAMS", "ENCODING")
					);

					if ($arPaySysAction = $dbPaySysAction->Fetch())
					{
						if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
						{
							CSalePaySystemAction::InitParamArrays($arResult["ORDER"], $arResult["ORDER"]["PAY_SYSTEM_ID"], $arPaySysAction["PARAMS"]);

							$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];
							$pathToAction = rtrim(str_replace("\\", "/", $pathToAction), "/");

							if (file_exists($pathToAction))
							{
								if (is_dir($pathToAction))
								{
									if (file_exists($pathToAction."/payment.php"))
										include($pathToAction."/payment.php");
								}
								else
								{
									include($pathToAction);
								}
							}
							if(strlen($arPaySysAction["ENCODING"]) > 0)
							{
								define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
								AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
								function ChangeEncoding($content)
								{
									global $APPLICATION;
									header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
									$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
									$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
								}
							}
						}
					}
					die();
				}
				elseif($arResult["ORDER"]["PAYED"] != "Y" && $arResult["ORDER"]["CANCELED"] != "Y")
				{
					$_SESSION["ORDER_PAYMENT"] = array(
						"ORDER_ID" => $arResult["ORDER"]["ID"],
						"PAY_SYSTEM_ID" => $arResult["ORDER"]["PAY_SYSTEM_ID"],
					);
					$arResult["OPEN_PAYMENT"] = "Y";
				}
				else
				{
					$cashback = 0;
					$AuthorizationCode = "";
					$RRN = "";
					$oTodo = SMTodoLog::GetList(array(), array("SECTION_ID" => "cash_back", "ITEM_ID" => $arResult["ORDER"]["ID"], "ACTIVE" => "N", "STORE_ID" => SMShops::getUserShop()), false, false, array("DESCRIPTION"));
					while($arTodo = $oTodo->Fetch())
					{
						$arCashback = unserialize($arTodo["DESCRIPTION"]);
						if($arCashback["Price"] > 0)
						{
							$cashback += $arCashback["Price"];
							if(empty($AuthorizationCode) && !empty($arCashback["AuthorizationCode"]))
								$AuthorizationCode = $arCashback["AuthorizationCode"];
							if(empty($RRN) && !empty($arCashback["RRN"]))
								$RRN = $arCashback["RRN"];
						}
					}
					if($cashback > 0)
					{
						$_SESSION["ORDER_CANCEL"] = array(
							"ORDER_ID" => $arResult["ORDER"]["ID"],
							"CASHBACK" => $cashback,
							"AuthorizationCode" => $AuthorizationCode,
							"RRN" => $RRN,
						);
						$arResult["OPEN_CANCEL"] = "Y";
					}
				}
			}
			$arResult["ORDER"] = CSaleOrder::GetByID($arParams["ID"]);
			$arResult["ELEMENT"] = array();
			$arElement = CSaleOrder::getList(array("ID" => "ASC"), array("ID" => $arParams["ID"]))->Fetch();
			$arElement["USER_ID"] = array(
				array("VALUE" => $arElement["USER_ID"])
			);
			foreach($arElement as $key => $value)
			{
				$arResult["ELEMENT"]["~".$key] = $value;
				if(!is_array($value) && !is_object($value))
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				else
					$arResult["ELEMENT"][$key] = $value;
			}
			
			$arResult["ELEMENT"]["PRICE_SUMM"] = $arResult["ELEMENT"]["PRICE"];

			$arNum = array("PRODUCT" => array("PRODUCT_PRICE", "PRODUCT_SUMM"), "PRICE_SUMM");
			foreach($arNum as $key => $val)
			{
				if(is_array($val))
				{
					if($key == "PRODUCT" && is_array($arResult["PROPERTY_LIST_FULL"][$key]["ENUM"]))
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

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}