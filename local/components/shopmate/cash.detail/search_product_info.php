<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
echo \Yadadya\Shopmate\Components\Products::getInfo(array(
	"ID" => $_REQUEST["pid"],
	"USER_ID" => $_REQUEST["uid"],
	"BARCODE" => $_REQUEST["term"],
	"BASKET_ID" => $_REQUEST["bid"],
), true);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>