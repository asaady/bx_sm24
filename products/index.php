<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Товары");
?>
<?$APPLICATION->IncludeComponent("shopmate:products");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>