<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "shopmate",
		"NAME" => GetMessage("MODULE_NAME"),
		"CHILD" => array(
			"ID" => "products",
			"NAME" => GetMessage("BLOCK_NAME"),
			"SORT" => 20,
			"CHILD" => array( //group in block
				"ID" => "products_cmpx",
			),
		),
	),
);
?>