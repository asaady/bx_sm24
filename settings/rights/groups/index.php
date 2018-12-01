<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Роли прав доступа");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "SettingsGroup", "NAV_ON_PAGE" => false));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>