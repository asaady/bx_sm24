<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Продажи отделов");
?>
<?$APPLICATION->IncludeComponent(
	"shopmate:finance.reports.departments", 
	"cooperative", 
	array(
		"SEF_MODE" => "N",
		"GROUPS" => array(
			0 => "1",
		),
		"ALLOW_EDIT" => "Y",
		"ALLOW_DELETE" => "Y",
		"NAV_ON_PAGE" => "10",
		"USER_MESSAGE_ADD" => "",
		"USER_MESSAGE_EDIT" => "",
		"DEFAULT_INPUT_SIZE" => "30",
		"SEF_FOLDER" => "/"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>