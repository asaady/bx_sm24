<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);

use Yadadya\Shopmate\Components\Products;

$ID = IntVal($_REQUEST["pid"]);
$USER_ID = IntVal($_REQUEST["uid"]);
$BARCODE = $_REQUEST["term"];
$BASKET_ID = IntVal($_REQUEST["bid"]);

$arResult = array();

if($ID > 0 && CModule::IncludeModule("yadadya.shopmate"))
{
	$SHOP_ID = SMShops::getUserShop();
	//$undefinedProducts = SMStoreBarcode::getUndefinedProducts();

	$result = Products::GetList(array(
		"select" => array("ID", "NAME", "AMOUNT", "MEASURE", "PURCHASING_PRICE", "PURCHASING_CURRENCY", "PRICE", "CURRENCY"),
		"filter" => array("ID" => $ID)
	));
	if($row = $result->fetch())
		$arResult = $row;
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>