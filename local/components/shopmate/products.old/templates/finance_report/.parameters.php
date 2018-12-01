<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
	"CACHE_FILTER" => array(
		"PARENT" => "CACHE_SETTINGS",
		"NAME" => GetMessage("IBLOCK_CACHE_FILTER"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"CACHE_GROUPS" => array(
		"PARENT" => "CACHE_SETTINGS",
		"NAME" => GetMessage("CP_BC_CACHE_GROUPS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);

$titles = array(
	"CAT_BARCODE" => "* штрихкод *", 
	"CAT_ALCCODE" => "* код ЕГАИС *", 
	"CAT_MEASURE" => "* единица измерения *",
	"CAT_AMOUNT" => "* количество товара *",
	"CAT_PURCHASING_PRICE" => "* закупочная цена *",
	"CAT_PRICE" => "* цена магазина *",
	"CAT_SHELF_LIFE" => "* срок годности *",
	"CAT_ARREARS" => "* задолженность по товару *",
	"CAT_PAYMENTS" => "* все выплаты по товару *",
	"СOMMENT" => "* комментарий *",
);
foreach ($titles as $key => $title)
{
	$arTemplateParameters["CUSTOM_TITLE_".$key] = array(
		"PARENT" => "TITLES",
		"NAME" => $title,
		"TYPE" => "STRING",
	);
}

?>
