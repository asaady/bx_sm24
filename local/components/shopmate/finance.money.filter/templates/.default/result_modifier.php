<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arResult["FILTER"] = array(
	array(
		"CONDITION" => array("TYPE" => "cash"),
		"TITLE" => "Наличные"
	),
	array(
		"CONDITION" => array("TYPE" => "clearing"),
		"TITLE" => "Безналичные"
	),
	array(
		"CONDITION" => array("OUTGO" => "payroll"),
		"TITLE" => "Затраты"
	),
	array(
		"CONDITION" => array("OUTGO" => "contractor"),
		"TITLE" => "Задолженности"
	),
	array(
		"CONDITION" => array("OUTGO" => "client"),
		"TITLE" => "Задолженности клиентов"
	)
);
foreach ($arResult["ITEM"] as $key => $val)
	if (empty($val))
		unset($arResult["ITEM"][$key]);

foreach ($arResult["FILTER"] as $filter)
	if(empty(array_diff_assoc($arResult["ITEM"], $filter["CONDITION"])))
	{
		global $APPLICATION;
		$APPLICATION->setTitle($filter["TITLE"]);
		break;
	}
?>