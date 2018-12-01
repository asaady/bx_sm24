<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Товарооборот");
?>
<?$APPLICATION->IncludeComponent(
	"shopmate:products.old", 
	"finance_report", 
	array(
		"NAV_ON_PAGE" => "10",
		"USE_CAPTCHA" => "N",
		"USER_MESSAGE_ADD" => "Товар успешно добавлен",
		"USER_MESSAGE_EDIT" => "Товар усмешно изменен",
		"DEFAULT_INPUT_SIZE" => "50",
		"RESIZE_IMAGES" => "N",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "2",
		"PROPERTY_CODES" => array(
			0 => "NAME",
			1 => "IBLOCK_SECTION",
			2 => "DETAIL_TEXT",
			3 => "1",
			4 => "2",
		),
		"PROPERTY_CODES_REQUIRED" => array(
			0 => "NAME",
		),
		"GROUPS" => array(
		),
		"STATUS" => "ANY",
		"STATUS_NEW" => "N",
		"ALLOW_EDIT" => "Y",
		"ALLOW_DELETE" => "Y",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"MAX_USER_ENTRIES" => "100000",
		"MAX_LEVELS" => "100000",
		"LEVEL_LAST" => "N",
		"MAX_FILE_SIZE" => "0",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"SEF_MODE" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CUSTOM_TITLE_NAME" => "Название продукта",
		"CUSTOM_TITLE_TAGS" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "Категория товара",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "Описание товара",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
		"SEF_FOLDER" => "/finance/reports/products/",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CUSTOM_TITLE_CAT_BARCODE" => "Штрихкод",
		"CUSTOM_TITLE_CAT_MEASURE" => "В чем измеряется",
		"CUSTOM_TITLE_CAT_AMOUNT" => "Количество",
		"CUSTOM_TITLE_CAT_PRICE" => "Цена на продажу",
		"CUSTOM_TITLE_CAT_PURCHASING_PRICE" => "Закупочная цена",
		"CUSTOM_TITLE_CAT_ARREARS" => "Задолженность по товару",
		"CUSTOM_TITLE_CAT_PAYMENTS" => "Все выплаты по товару",
		"CUSTOM_TITLE_CAT_SHELF_LIFE" => "Срок годности",
		"CUSTOM_TITLE_CAT_ALCCODE" => "Код ЕГАИС",
		"CUSTOM_TITLE_COMMENT" => "Комментарий"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>