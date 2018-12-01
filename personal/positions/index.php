<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Должности");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "PersonalPosition"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>