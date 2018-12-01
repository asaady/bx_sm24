<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Разделы прав доступа");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "SettingsChapter", "NAV_ON_PAGE" => false));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>