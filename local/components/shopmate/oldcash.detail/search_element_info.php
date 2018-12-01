<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
$arResult = array();
$ID = IntVal($_REQUEST["pid"]);
$USER_ID = IntVal($_REQUEST["uid"]);
$BARCODE = $_REQUEST["term"];
$BASKET_ID = IntVal($_REQUEST["bid"]);
$wpb = array("90", "23", "26");
if($ID > 0 && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$SHOP_ID = SMShops::getUserShop();
	$undefinedProducts = SMStoreBarcode::getUndefinedProducts();
	if(in_array($ID, $undefinedProducts))
	{
		$rsProps = CCatalogStoreProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $ID, "STORE_ID" => $SHOP_ID), false, false, array("AMOUNT"));
		if($arProp = $rsProps->GetNext())
			$CAT_AMOUNT = $arProp["AMOUNT"];
		if($CAT_AMOUNT <= 0)
		{
			CCatalogStoreProduct::UpdateFromForm(array(
				"PRODUCT_ID" => $ID,
				"STORE_ID" => $SHOP_ID,
				"AMOUNT" => 10000,
			));
		}

		if($BASKET_ID <= 0)
		{
			$arResult["PRICE"] = 0;
			$arResult["DISCOUNT_PRICE"] = 0;
			//$arResult["CAT_AMOUNT"] = 0;
		}
	}
	else
	{
		$rsProps = CCatalogStoreProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $ID, "STORE_ID" => $SHOP_ID), false, false, array("AMOUNT"));
		if($arProp = $rsProps->GetNext())
			$arResult["CAT_AMOUNT"] = $arProp["AMOUNT"];

		$rsProps = SMFabricaProduct::GetList(array("ID" => "ASC"), array("PRODUCT_ID" => $ID, "STORE_ID" => $SHOP_ID), false, false, array("PROD_QUANTITY"));
		if($arProp = $rsProps->Fetch())
			$arResult["CAT_AMOUNT"] += $arProp["PROD_QUANTITY"];

		$rsProps = CCatalogProduct::GetList(array(), array("ID" => $ID), false, false, array("MEASURE"));
		if($arProp = $rsProps->Fetch())
			$arResult["CAT_MEASURE"] += $arProp["MEASURE"];

		if($BASKET_ID <= 0)
		{
			if($USER_ID <= 0)
				$USER_ID = SMUser::SimpleBuyerAdd();

			$IBLOCK_ID =  CIBlockElement::GetIBlockByID($ID);

			$arUserGroups = array();

			$res = CUser::GetUserGroupList(empty($USER_ID) ? false : $USER_ID);
			while ($arGroup = $res->Fetch())
				$arUserGroups[] = $arGroup["GROUP_ID"];

			//$arPriceGroups = CCatalogGroup::GetGroupsPerms($arUserGroups, array());

			$arFilter = array("PRODUCT_ID" => $ID);
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
				$arDiscounts = CCatalogDiscount::GetDiscount($ID, $IBLOCK_ID, $arPrice["CATALOG_GROUP_ID"], $arUserGroups, "N", SITE_ID);
				CCatalogDiscountSave::Enable();
				$arPrice["DISCOUNT_PRICE"] = empty($arDiscounts) ? $arPrice['PRICE'] : CCatalogProduct::CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);

				if($arPrice["DISCOUNT_PRICE"] > 0 && ($arResult["DISCOUNT_PRICE"] <= 0 || $arPrice["DISCOUNT_PRICE"] < $arResult["DISCOUNT_PRICE"]))
				{
					$arResult["PRICE"] = $arPrice["PRICE"];
					$arResult["DISCOUNT_PRICE"] = $arPrice["DISCOUNT_PRICE"];
				}
			}

			if(in_array(substr($BARCODE, 0, 2), $wpb) && strlen($BARCODE) == 13 && ($arResult["CAT_MEASURE"] == 3 || $arResult["CAT_MEASURE"] == 4))
			{
				$BARCODE_WEIGHT = substr_replace($BARCODE, "00000_", -6);
				$weight = floatval(substr($BARCODE, -6, -1));
				if($arResult["CAT_MEASURE"] == 4) $weight = $weight / 1000;
				$arResult["CAT_QUANTITY"] = $weight;
				/*$rsBarCode = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $ID, "BARCODE" => $BARCODE_WEIGHT), false, false, array("BARCODE"));
				if($arBarCode = $rsBarCode->Fetch())	
					if($arBarCode["BARCODE"] == $BARCODE_WEIGHT)
						$arResult["CAT_QUANTITY"] = $weight;*/
			}
			if($arResult["CAT_AMOUNT"] <= 0)
			{
				CCatalogStoreProduct::UpdateFromForm(array(
					"PRODUCT_ID" => $ID,
					"STORE_ID" => $SHOP_ID,
					"AMOUNT" => 10000,
				));
				$arResult["CAT_AMOUNT"] = 10000;
			}
		}
	}
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>