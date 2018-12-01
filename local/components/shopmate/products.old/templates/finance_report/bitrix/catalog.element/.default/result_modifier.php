<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult = array_merge($arParams["TEMPLATE_RESULT"], $arResult);
unset($arResult["TEMPLATE_RESULT"]);

$arResult["ELEMENT"]["CAT_MEASURE"][0]["VALUE"] = $arResult["CATALOG_MEASURE"];

$arResult["ELEMENT"]["CAT_PURCHASING_PRICE"] = $arResult["CATALOG_PURCHASING_PRICE"];

reset($arResult["PRICES"]);
$price_name = key($arResult["PRICES"]);
$arResult["ELEMENT"]["CAT_PRICE"] = $arResult["CATALOG_PRICE_".$arResult["PRICES"][$price_name]["PRICE_ID"]];

if(CModule::IncludeModule("yadadya.shopmate") && stripos($price_name, SMShops::$shop_prefix) !== false)
{
	$shopID = substr($price_name, strlen(SMShops::$shop_prefix));
	$arSelect = array(
		"ID",
		"TITLE",
		"ADDRESS",
		"PRODUCT_AMOUNT",
	);
	$rsProps = CCatalogStore::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $arResult["ELEMENT"]["ID"], "ACTIVE" => "Y", "ID" => $shopID), false, false, $arSelect);
	if($arProp = $rsProps->GetNext())
		$arResult["ELEMENT"]["CAT_AMOUNT"] = $arProp["PRODUCT_AMOUNT"];

	$rsProps = SMProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $arResult["ELEMENT"]["ID"]), false, false, array("ID", "PRODUCT_ID", "SHELF_LIFE"));
	if($arProp = $rsProps->GetNext())
		$arResult["ELEMENT"]["CAT_SHELF_LIFE"] = $arProp["SHELF_LIFE"];
}
if(empty($arResult["ELEMENT"]["CAT_AMOUNT"])) $arResult["ELEMENT"]["CAT_AMOUNT"] = $arResult["CATALOG_QUANTITY"];

$dbResultList = CCatalogStoreBarCode::getList(array("ID" => "ASC"), array("PRODUCT_ID" => $arResult["ELEMENT"]["ID"]));
while($arFields = $dbResultList->Fetch())
	$arResult["ELEMENT"]["CAT_BARCODE"][]["VALUE"] = $arFields["BARCODE"];

if(CModule::IncludeModule("yadadya.shopmate") && class_exists("SMEGAISAlcCode"))
{
	$dbResultList = SMEGAISAlcCode::getList(array("ID" => "ASC"), array("PRODUCT_ID" => $arResult["ELEMENT"]["ID"]));
	while($arFields = $dbResultList->Fetch())
		$arResult["ELEMENT"]["CAT_ALCCODE"][]["VALUE"] = $arFields["ALCCODE"];
}
?>