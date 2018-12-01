<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Цены продуктов");
?><?$APPLICATION->IncludeComponent("shopmate:pricelist.product");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>