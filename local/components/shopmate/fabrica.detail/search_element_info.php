<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
$arResult = array();
$ID = IntVal($_REQUEST["pid"]);
$USER_ID = IntVal($_REQUEST["uid"]);
if($ID > 0 && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$rsProps = CCatalogProduct::GetList(array('SORT' => 'ASC'), array("ID" => $ID), false, false, array("MEASURE"));
	if($arProp = $rsProps->GetNext())
		$arResult["CAT_MEASURE"] = $arProp["MEASURE"];
	if(empty($arResult["CAT_MEASURE"]))
	{
		$oMeasure = CCatalogMeasure::GetList(array(), array("IS_DEFAULT" => "Y"));
		if($arMeasure = $oMeasure->Fetch())
			$arResult["CAT_MEASURE"] = $arMeasure["ID"];
	}
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>