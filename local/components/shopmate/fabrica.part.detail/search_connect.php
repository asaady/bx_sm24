<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
if (!empty($_REQUEST["ppid"]))
	echo \Yadadya\Shopmate\Components\Fabrica::getEnumList(array("PARENT_PROD_ID" => $_REQUEST["ppid"]), true);
else
	echo \Yadadya\Shopmate\Components\Fabrica::getEnumList(array("SEARCH" => $_REQUEST["q"], "CONNECT" => "Y"), true);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>