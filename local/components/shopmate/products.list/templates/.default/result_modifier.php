<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arResult["MEASURES"] = array();
$oMeasure = CCatalogMeasure::GetList();
while($arMeasure = $oMeasure->Fetch())
{
	$arResult["MEASURES"][$arMeasure["ID"]] = $arMeasure;
	if($arMeasure["IS_DEFAULT"] == "Y") $arResult["CURRENT_MEASURE"] = $arMeasure;
}

$row_id = false;

$arSections = \Yadadya\Shopmate\Components\Products::getSectionsEnum(1000);
function addProdToSect(&$sections, $sect_id, $prod_id)
{
	$sections[$sect_id]["ITEMS"][] = $prod_id;
	if (!empty($sections[$sect_id]["SECTION_ID"]))
		addProdToSect($sections, $sections[$sect_id]["SECTION_ID"], $prod_id);
}
foreach($arResult["ITEMS"] as $keyItem => $arItem) 
	if (!empty($arItem["SECT_ID"]))
		addProdToSect($arSections, $arItem["SECT_ID"], $arItem["ID"]);

foreach($arResult["ITEMS"] as $keyItem => $arItem) 
{
	if ($arItem["MEASURE"] > 0 && array_key_exists($arItem["MEASURE"], $arResult["MEASURES"]))
		$arItem["MEASURE"] = $arResult["MEASURES"][$arItem["MEASURE"]]["SYMBOL_RUS"];
	else
		$arItem["MEASURE"] = $arResult["CURRENT_MEASURE"]["SYMBOL_RUS"];

	$arItem["AMOUNT"] = empty ($arItem["AMOUNT"]) ? " - " : $arItem["AMOUNT"]." ".$arItem["MEASURE"];

	$arItem["PURCHASE_AMOUNT"] = empty ($arItem["PURCHASE_AMOUNT"]) ? " - " : $arItem["PURCHASE_AMOUNT"]." ".$arItem["MEASURE"];
	$arItem["SALE_QUANTITY"] = empty ($arItem["SALE_QUANTITY"]) ? " - " : $arItem["SALE_QUANTITY"]." ".$arItem["MEASURE"];
	$arItem["MARGINALITY"] = empty ($arItem["MARGINALITY"]) ? " - " : round($arItem["MARGINALITY"], 2)."%";

	$arItem["PURCHASING_PRICE"] = CurrencyFormat(floatval($arItem["PURCHASING_PRICE"]), !empty($arItem["PURCHASING_CURRENCY"]) ? $arItem["PURCHASING_CURRENCY"] : "RUB") . "/" . $arItem["MEASURE"];
	$arItem["PRICE"] = CurrencyFormat(floatval($arItem["PRICE"]), !empty($arItem["CURRENCY"]) ? $arItem["CURRENCY"] : "RUB") . "/" . $arItem["MEASURE"];


	if ($arItem["SECT_ID"] !== $row_id)
	{
		$row_id = $arItem["SECT_ID"];
		$arItem["ROW_TITLE"] = "";
		if (empty($arItem["SECT_ID"]))
			$arItem["ROW_TITLE"] = "<h2 data-row_title_id=\"0\">товары без категории</h2>";
		else
			foreach ($arSections as $sect_id => $arSection)
			{
				if (empty($arSection["DEPTH_LEVEL"]))
					$arSection["DEPTH_LEVEL"] = 1;
				if (in_array($arItem["ID"], $arSection["ITEMS"]))
					$arItem["ROW_TITLE"] .= "<h".$arSection["DEPTH_LEVEL"]." data-row_title_id=\"".$sect_id."\">" . str_repeat("- ", $arSection["DEPTH_LEVEL"]) . $arSection["VALUE"] . "</h".$arSection["DEPTH_LEVEL"].">";
				if ($sect_id == $arItem["SECT_ID"])
					break;
			}
	}

	$arResult["ITEMS"][$keyItem] = $arItem;
}

unset($arResult["SORTS"]["MEASURE"], $arResult["SORTS"]["PURCHASING_CURRENCY"], $arResult["SORTS"]["CURRENCY"], $arResult["SORTS"]["SECT_ID"], $arResult["SORTS"]["SECT_NAME"], $arResult["SORTS"]["SECT_LEFT_MARGIN"], $arResult["SORTS"]["SECT_DEPTH_LEVEL"]);

$arResult["VIEW_EDIT"] = "N";
$arResult["SORTS"]["ITEM_CHECK"] = [
	"FIELD" => "ITEM_CHECK",
	"NAME" => " ",
	"SORT" => "5000",
	"INPUT" => [
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "C",
		"USER_TYPE" => "checkbox",
		"ENUM" => array("Y" => ""),
	]
];
$arResult["SORTS"]["NAME"]["EDIT_LINK"] = "Y";
//print_p($_POST);
?>
<a class="btn btn-primary pull-right list__inventory_btn" href="#" style="display:none;"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span> Создать список на инвенторизацию</a>