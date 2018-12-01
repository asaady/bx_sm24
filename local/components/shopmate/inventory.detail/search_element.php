<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
// $arResult["total_count"] = 500;
// $arResult["incomplete_result"] = false;
$arResult["items"] = array();
$wpb = array("90", "23", "26");
if(!empty($_REQUEST["q"]) && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$SHOP_ID = SMShops::getUserShop();
	$PRICE_ID = SMShops::getUserPrice();
	$product_id = SMStoreBarcode::getProductsByBarcode($_REQUEST["q"]);
	$arFilter = Array("IBLOCK_ID" => 2);
	if(empty($product_id))
		$arFilter["NAME"] = str_combos($_REQUEST["q"]);
	else
		$arFilter[] = array(
			"LOGIC" => "OR",
			array("NAME" => str_combos($_REQUEST["q"])),
			array("ID" => $product_id),
		);
	$product_id = array();
	$arProducts = array();
	/*$undefinedProducts = SMStoreBarcode::getUndefinedBarcodes();
	if(!in_array($_REQUEST["q"], $undefinedProducts))
	{
		//$arFilter["!CATALOG_STORE_AMOUNT_".$SHOP_ID] = false;
		$arFilter["!CATALOG_PRICE_".$PRICE_ID] = false;
	}*/
	$rsElement = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>10), array("ID", "NAME"));
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
		$arResult["items"][] = array("id" => $arProduct["ID"], "text" => str_ireplace($_REQUEST["q"], "<b>".$_REQUEST["q"]."</b>", $arProduct["NAME"].(!empty($arProduct["BARCODES"]) ? " [".implode(", ", $arProduct["BARCODES"])."]" : "")));
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>