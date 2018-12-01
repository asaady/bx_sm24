<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult["PROPERTY_LIST_FULL"] as $prop => $arProp)
{
	if($prop == "ELEMENT")
	{
		$arProp["ADDED_CLASS"] = "scanner_detection_add";
		foreach($arProp["arResult"]["PROPERTY_LIST_FULL"] as $pprop => $arPProp) 
		{
			if($pprop == "ELEMENT_ELEMENT_ID")
			{
				$arPProp["CLASS"] = "scanner_detection";
				$arPProp["CSS"] = "width: 200px; min-width: 200px; display: block;";
			}
			if($pprop == "ELEMENT_AMOUNT")
			{
				//$arPProp["INFO_SET"] = "CAT_QUANTITY";
				$arPProp["CLASS"] = "product_quantity";
				$arPProp["DEFAULT_VALUE"] = 1;
				$arPProp["CALC_INPUT"] = "PURCHASING_AMOUNT";
			}
			if($pprop == "ELEMENT_MEASURE")
			{
				$arPProp["INFO_SET"] = "CAT_MEASURE";
				$arPProp["CLASS"] = "width50 nopadding-left";
			}
			if($pprop == "ELEMENT_PURCHASING_PRICE")
			{
				$arPProp["CLASS"] = "purchasing_price";
				$arPProp["INFO_SET"] = "PURCHASING_PRICE";
				$arPProp["CALC_INPUT"] = "PURCHASING_PRICE";
			}
			if($pprop == "ELEMENT_SHOP_PRICE")
			{
				$arPProp["CLASS"] = "shop_price";
				$arPProp["INFO_SET"] = "PRICE";
			}
			if($pprop == "ELEMENT_START_DATE")
			{
				$arPProp["CLASS"] = "start_date";
				$arPProp["INFO_SET"] = "START_DATE";
			}
			if($pprop == "ELEMENT_PURCHASING_NDS")
			{
				$arPProp["CLASS"] .= "width100";
				$arPProp["INFO_SET"] = "NDS";
				$arPProp["CALC_INPUT"] = "PURCHASING_NDS";
			}
			if($pprop == "ELEMENT_PURCHASING_SUMM")
			{
				$arPProp["CLASS"] = "calc_summ__elem";
				$arPProp["CALC_INPUT"] = "PURCHASING_SUMM";
			}
			$arProp["arResult"]["PROPERTY_LIST_FULL"][$pprop] = $arPProp;
		}
	}

	if($prop == "CONTRACTOR_ID")
		$arProp["CLASS"] = "width100p price_user_id";
	if($prop == "CONTRACTOR_NAME")
		$arProp["CLASS"] = "width100p price_user_id";
	if($prop == "TOTAL_FACT")
		$arProp["CLASS"] = "input-lg";
	if($prop == "TOTAL_SUMM")
		$arProp["CLASS"] = "input-lg calc_summ__result";

	$arResult["PROPERTY_LIST_FULL"][$prop] = $arProp;
}
$arResult["PROPERTY_LIST_GROUP"] = array(
	array("NUMBER_DOCUMENT", "DATE_DOCUMENT", "CONTRACTOR_ID"/*, "CONTRACTOR_NAME"*/),
	array("ELEMENT"),
	array(),
);
foreach ($arResult["PROPERTY_LIST"] as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][1]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}
$shop = SMShops::getUserShops(SMShops::getUserShop());
$arParams["CUSTOM_TITLE_ELEMENT_ELEMENT_ID"] = str_replace("#SHOP_NAME#", $shop[0]["NAME"], $arParams["CUSTOM_TITLE_ELEMENT_ELEMENT_ID"]);
?>