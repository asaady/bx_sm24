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
		$arResult["PRICE"] = 0;
		$arResult["DISCOUNT_PRICE"] = 0;
	}
	else
	{
		$res = \Yadadya\Shopmate\Components\Products::getList(array(
			"select" => array("ID"/*, "AMOUNT", "MEASURE", "PURCHASING_PRICE"*/, "PRICE"),
			"filter" => array("ID" => $ID)
		));
		if ($row = $res->fetch())
			$arResult = $row;
	}
}
echo json_encode($arResult);
//print_r($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>