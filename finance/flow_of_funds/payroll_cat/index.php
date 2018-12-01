<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Категории ДДС \"Начисления\"");?>
<?$APPLICATION->IncludeComponent(
	"shopmate:finance.money.cat", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"NAV_ON_PAGE" => "10",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "2",
		"OUTGO" => "payroll"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>