<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("catalog"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arGroups = array();
$rsGroups = CGroup::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"));
while ($arGroup = $rsGroups->Fetch())
{
	$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
		"PARAMS" => array(
			"NAME" => GetMessage("PARAMS"),
			"SORT" => "200"
		),
		"ACCESS" => array(
			"NAME" => GetMessage("ACCESS"),
			"SORT" => "400",
		),
	),

	"PARAMETERS" => array(
		"SEF_MODE" => Array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),

		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"GROUPS" => array(
			"PARENT" => "ACCESS",
			"NAME" => GetMessage("GROUPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arGroups,
		),
		"ALLOW_EDIT" => array(
			"PARENT" => "ACCESS",
			"NAME" => GetMessage("ALLOW_EDIT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),

		"ALLOW_DELETE" => array(
			"PARENT" => "ACCESS",
			"NAME" => GetMessage("ALLOW_DELETE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

$arComponentParameters["PARAMETERS"]["NAV_ON_PAGE"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("NAV_ON_PAGE"),
	"TYPE" => "TEXT",
	"DEFAULT" => "10",
);

$arComponentParameters["PARAMETERS"]["USER_MESSAGE_ADD"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("USER_MESSAGE_ADD"),
	"TYPE" => "TEXT",
);

$arComponentParameters["PARAMETERS"]["USER_MESSAGE_EDIT"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("USER_MESSAGE_EDIT"),
	"TYPE" => "TEXT",
);

$arComponentParameters["PARAMETERS"]["DEFAULT_INPUT_SIZE"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("DEFAULT_INPUT_SIZE"),
	"TYPE" => "TEXT",
	"DEFAULT" => 30,
);
?>