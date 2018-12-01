<?
$component_name = "shopmate:inventory";
if($_REQUEST['new_primary'] == "Y")
	$component_name = "shopmate:inventory.primary";
$APPLICATION->IncludeComponent(
	$component_name, 
	".default", 
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
		"SEF_FOLDER" => "/",
		"PRODUCTION_TYPES" => array(
			0 => "мясо",
			1 => "молоко",
			2 => "упаковка",
		)
	),
	false
);?>