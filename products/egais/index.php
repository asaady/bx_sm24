<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Алкогольная продукция");
?><?$APPLICATION->IncludeComponent("bitrix:main.include", "products", Array(
	"AREA_FILE_SHOW" => "page",	// Показывать включаемую область
		"AREA_FILE_SUFFIX" => "inc",	// Суффикс имени файла включаемой области
		"EDIT_TEMPLATE" => "",	// Шаблон области по умолчанию
	),
	null,
	array("HIDE_ICONS" => "Y")
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>