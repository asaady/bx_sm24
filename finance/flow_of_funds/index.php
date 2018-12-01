<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Движение денежных средств");?>
<?$APPLICATION->IncludeComponent(
	"shopmate:finance.money", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"NAV_ON_PAGE" => "10",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "2"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>