<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("catalog"))
	return;

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

$arAscDesc = array(
	"asc" => GetMessage("SORT_ASC"),
	"desc" => GetMessage("SORT_DESC"),
);
$arTransaction = array(
	"overhead" => GetMessage("TRANSACTION_OVERHEAD"),
);
$arComponentParameters["PARAMETERS"] = array_merge(
	$arComponentParameters["PARAMETERS"],
	array(
		"TRANSACTION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TRANSACTION"),
			"TYPE" => "LIST",
			"VALUES" => $arTransaction,
		),
		"ITEM_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ITEM_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["CODE"]}',
		),
		"SORT_FIELD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => array("ID", "TIMESTAMP_X", "TRANSACTION", "ITEM_ID"),
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "ID",
		),
		"SORT_ORDER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
		),
		"FILTER_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("FILTER_NAME_IN"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
	)
);
?>