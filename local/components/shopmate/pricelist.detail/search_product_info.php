<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
echo \Yadadya\Shopmate\Components\Products::getInfo(
	array(
		"ID" => $_REQUEST["pid"],
		//"BASKET_ID" => $_REQUEST["bid"],
	), 
	true,
	array("ID", "PURCHASING_PRICE", "PRICE", "CAT_MEASURE"));
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>