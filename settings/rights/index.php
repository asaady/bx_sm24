<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Права доступа");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "SettingsRight"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>