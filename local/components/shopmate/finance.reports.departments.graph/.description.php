<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "Y",
	"SORT" => 20,
	"PATH" => array(
		"ID" => "shopmate",
		"NAME" => GetMessage("MODULE_NAME"),
		"CHILD" => array(
			"ID" => "finance",
			"NAME" => GetMessage("BLOCK_NAME"),
			"SORT" => 30,
			"CHILD" => array( //group in block
				"ID" => "finance_reports_departments",
			),
		),
	),
);
?>