<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Инвентаризация");
?>
<?$APPLICATION->IncludeComponent("shopmate:inventory");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>