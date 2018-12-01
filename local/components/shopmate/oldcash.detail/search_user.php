<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
// $arResult["total_count"] = 500;
// $arResult["incomplete_result"] = false;
$arResult["items"] = array();
if(!empty($_REQUEST["q"]) && CModule::IncludeModule("catalog"))
{
	$arFilter = Array(
		"KEYWORDS" => $_REQUEST["q"],
	);

	$rsElements = CUser::getList(($by="name"), ($order="desc"), $arFilter);

	while ($arElement = $rsElements->NavNext(false))
	{
		$user_phones = array();
		$phone_fields = array("PERSONAL_PHONE", "PERSONAL_MOBILE", "WORK_PHONE");
		foreach($phone_fields as $phone_field)
			if(!empty($arElement[$phone_field]))
				$user_phones[] = $arElement[$phone_field];
		$phone = implode(", ", $user_phones);
		$name = $arElement["LAST_NAME"]." ".$arElement["NAME"]." ".$arElement["SECOND_NAME"]."[".$arElement["EMAIL"]."]".(!empty($phone) ? ", т. ".$phone : "");
		$arResult["items"][] = array("id" => $arElement["ID"], "text" => str_ireplace($_REQUEST["q"], "<b>".$_REQUEST["q"]."</b>", $name));
	}
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>