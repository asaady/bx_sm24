<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
// $arResult["total_count"] = 500;
// $arResult["incomplete_result"] = false;
$arResult["items"] = array();
if(!empty($_REQUEST["q"]) && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arFilter = Array(
		"~COMPANY" => "%".$_REQUEST["q"]."%",
		"~PERSON_NAME" => "%".$_REQUEST["q"]."%",
		"~PHONE" => "%".$_REQUEST["q"]."%",
	);

	$rsElements = SMContractor::getListOR(array("NAME" => "ASC"), $arFilter);

	while ($arElement = $rsElements->NavNext(false))
	{
		$name = ($arElement["PERSON_TYPE"] == 2 ? $arElement["COMPANY"]." (".$arElement["PERSON_NAME"].")" : $arElement["PERSON_NAME"]).(strlen($arElement["PHONE"]) ? ", т. ".$arElement["PHONE"] : "");
		$arResult["items"][] = array("id" => $arElement["ID"], "text" => str_ireplace($_REQUEST["q"], "<b>".$_REQUEST["q"]."</b>", $name));
	}
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>