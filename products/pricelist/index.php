<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Прайслисты");
?>
<?$APPLICATION->IncludeComponent("shopmate:pricelist");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>