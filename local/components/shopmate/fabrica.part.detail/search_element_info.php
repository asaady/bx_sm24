<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
$arResult = array();
$ID = IntVal($_REQUEST["pid"]);
$USER_ID = IntVal($_REQUEST["uid"]);
if($ID > 0 && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$res = \Yadadya\Shopmate\Internals\FabricaProductConnectTable::getList(array(
		"select" => array("CAT_MEASURE" => "CONNECT.PRODUCT.MEASURE"), 
		"filter" => array("CONNECT_ID" => $ID),
		"runtime" => array("CONNECT" => new \Bitrix\Main\Entity\ReferenceField(
				'CONNECT',
				'Yadadya\Shopmate\Internals\FabricaProduct',
				array('=this.CONNECT_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			)
		)
	));
	if($row = $res->fetch())
		$arResult["MEASURE"] = $row["CAT_MEASURE"];

	if(empty($arResult["MEASURE"]))
	{
		$oMeasure = CCatalogMeasure::GetList(array(), array("IS_DEFAULT" => "Y"));
		if($arMeasure = $oMeasure->Fetch())
			$arResult["MEASURE"] = $arMeasure["ID"];
	}
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>