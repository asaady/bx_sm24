<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Продажи");?>
<?$APPLICATION->IncludeComponent("shopmate:finance.report.sales");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>