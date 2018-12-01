<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Import prices");
if(!$USER->IsAdmin()) die();
$file = $_SERVER["DOCUMENT_ROOT"]."/upload/prices.csv";
$arResult = array();

if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    	$arResult[$data[5]] = $data[3];
    }
    fclose($handle);
}

if(!empty($arResult) && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$products = array();
	$rsBarCode = CCatalogStoreBarCode::getList(array(), array(), false, false, array("PRODUCT_ID", "BARCODE"));
	while($arBarCode = $rsBarCode->Fetch())
		$products[$arBarCode["PRODUCT_ID"]][] = $arBarCode["BARCODE"];
	
	foreach ($products as $product_id => $barcodes) 
	{
		foreach($barcodes as $barcode)
		{
			if($arResult[$barcode] > 0)
			{
				$arFields = Array(
				    "PRODUCT_ID" => $product_id,
				    "CATALOG_GROUP_ID" => 19,
				    "PRICE" => $arResult[$barcode],
				    "CURRENCY" => "RUB"
				);
				CPrice::Add($arFields);
			}
		}
	}
}
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>