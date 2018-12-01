<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Продажа собственнику");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "CashSelf"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>