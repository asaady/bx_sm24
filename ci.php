<?define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/x-javascript; charset=' . LANG_CHARSET);

\Bitrix\Main\Loader::includeModule("yadadya.shopmate");
include($_SERVER["DOCUMENT_ROOT"]."/local/modules/yadadya.shopmate/install/internals.php");
include($_SERVER["DOCUMENT_ROOT"]."/local/modules/yadadya.shopmate/install/events.php");
include($_SERVER["DOCUMENT_ROOT"]."/local/modules/yadadya.shopmate/install/agents.php");
include($_SERVER["DOCUMENT_ROOT"]."/local/modules/yadadya.shopmate/install/mess_events.php");
wikiDoc::updateWikiFromReadme();

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>