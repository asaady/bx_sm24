<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$sorts = array(
	"NAME" => "Наименование",
	"SALE" => "Продажи за период",
	"PURCHASE" => "Закупки за период",
	"PROFIT" => "Фактическая прибыль",
	"PRICE_PROFIT" => "Прайсовая прибыль",
);
$arResult["SORTS"] = array();
foreach($sorts as $field => $name)
{
	$arResult["SORTS"][] = array(
		"ID" => $field,
		"NAME" => $name,
		"URL" => $APPLICATION->GetCurPageParam("SORT=".$field.(strtoupper($_REQUEST["ORDER"]) != "DESC" ? "&ORDER=DESC" : ""), array("SORT", "ORDER"))
	);
}
?>