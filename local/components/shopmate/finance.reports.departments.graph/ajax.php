<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.filter");

$APPLICATION->RestartBuffer();
if(empty($_REQUEST["PROPERTY"]["SUBSECTION"]))
	$section = $APPLICATION->IncludeComponent("shopmate:finance.reports.departments.graph", !empty($_REQUEST["template"]) ? $_REQUEST["template"] : "");
if(!empty($_REQUEST["PROPERTY"]["SUBSECTION"]) || empty($section))
{
	if(!empty($_REQUEST["PROPERTY"]["SUBSECTION"]))
	{
		$subsection = $_GET["PROPERTY"]["SUBSECTION"];
		unset($_GET["PROPERTY"]["SUBSECTION"]);
		$backUrl = $APPLICATION->GetCurPageParam();
		$_GET["PROPERTY"]["SUBSECTION"] = $subsection;
	}
	else
	{
		$backUrl = $APPLICATION->GetCurPageParam();
	}
	$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.list", !empty($_REQUEST["template"]) ? $_REQUEST["template"] : "graph", array("BACK_URL" => $backUrl, "NAV_ON_PAGE" => 5));
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();
?>