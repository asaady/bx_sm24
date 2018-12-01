<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult["PROPERTY_LIST_FULL"] as $prop => $arProp)
{
	if($prop == "SECTION")
	{
		$arProp["CLASS"] = "sel_section";
	}
	if($prop == "SUBSECTION")
		$arProp["CLASS"] = "sel_subsection";

	$arResult["PROPERTY_LIST_FULL"][$prop] = $arProp;
}
$arResult["PROPERTY_LIST_GROUP"] = array(
	array("SECTION", "SUBSECTION", "LAST_OVERHEAD", "PACK"),
	array(),
	array("STORE_PRODUCT", "PRODUCT_SEARCH", "CONTRACTOR"),
	array("PERISHABLE"),
);
foreach ($arResult["PROPERTY_LIST"] as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][1]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}
?>