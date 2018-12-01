<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult["PROPERTY_LIST_FULL"] as $prop => $arProp)
{
	if($prop == "SECTION")
		$arProp["CLASS"] = "sel_section";
	if($prop == "SUBSECTION")
		$arProp["CLASS"] = "sel_subsection";
	if($prop == "STORE_PRODUCT")
		$arProp["LIST_TYPE"] = "C";
	if($prop == "LAST_OVERHEAD_FROM" || "LAST_OVERHEAD_TO")
		$arProp["CLASS"] = "start_date";

	$arResult["PROPERTY_LIST_FULL"][$prop] = $arProp;
}
$arResult["PROPERTY_LIST_GROUP"] = array(
	array("SECTION", "SUBSECTION", "LAST_OVERHEAD_FROM", "LAST_OVERHEAD_TO"),
	array(),
	array("STORE_PRODUCT", "PRODUCT_SEARCH", "CONTRACTOR"),
	array("PERISHABLE", "EXPIRATION"),
);
foreach ($arResult["PROPERTY_LIST"] as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][1]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}
?>