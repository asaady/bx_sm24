<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Разделы");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "Section"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>