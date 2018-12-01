<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult["PROPERTY_LIST_FULL"] as $prop => $arProp)
{
	if($prop == "PRODUCT")
	{
		$arProp["ADDED_CLASS"] = "scanner_detection_add";
		foreach($arProp["arResult"]["PROPERTY_LIST_FULL"] as $pprop => $arPProp) 
		{
			if($pprop == "PRODUCT_PRODUCT_ID")
				$arPProp["CLASS"] = "scanner_detection";
			if($pprop == "PRODUCT_QUANTITY")
			{
				$arPProp["CLASS"] = "product_quantity keyboard keyboard_np";
				$arPProp["DEFAULT_VALUE"] = 1;
			}
			if($pprop == "PRODUCT_AMOUNT")
			{
				$arPProp["INFO_SET"] = "CAT_AMOUNT";
				$arPProp["DISABLED"] = "Y";
			}
			if($pprop == "PRODUCT_PRICE")
			{
				$arPProp["INFO_SET"] = "DISCOUNT_PRICE";
				$arPProp["DISABLED"] = "Y";
				$arPProp["CLASS"] = "product_price keyboard keyboard_np";
			}
			if($pprop == "PRODUCT_MEASURE")
			{
				$arPProp["INFO_SET"] = "CAT_MEASURE";
				$arPProp["CLASS"] = "width50 nopadding-left";
			}
			if($pprop == "PRODUCT_QUANTITY")
			{
				$arPProp["INFO_SET"] = "CAT_QUANTITY";
				$arPProp["CALC_INPUT"] = "QUANTITY";
			}
			if($pprop == "PRODUCT_PRICE")
				$arPProp["CALC_INPUT"] = "PRICE";
			if($pprop == "PRODUCT_SUMM")
			{
				$arPProp["CALC_INPUT"] = "SUMM";
				$arPProp["DISABLED"] = "Y";
				$arPProp["CLASS"] = "calc_summ__elem";
			}
			$arProp["arResult"]["PROPERTY_LIST_FULL"][$pprop] = $arPProp;
		}
	}
	if($prop == "USER_ID")
	{
		$arProp["CLASS"] = "price_user_id";
	}
	if($prop == "PRICE_SUMM")
	{
		$arProp["CLASS"] = "calc_summ__result";
	}

	$arResult["PROPERTY_LIST_FULL"][$prop] = $arProp;
}
?>