<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
echo \Yadadya\Shopmate\Components\Products::getEnumList(array("SEARCH" => $_REQUEST["q"], "SMPRODUCT.DNC_TYPE_CODE" => array(0, 1, 2, 4)), true);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>