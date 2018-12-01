<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult["PROPERTY_LIST_FULL"] as $prop => $arProp)
{
	if($prop == "DEPARTMENT")
	{
		$arProp["CLASS"] = "sel_section";
		$arProp["DATA"]["sect"] = "department";
	}
	if($prop == "SECTION")
	{
		$arProp["CLASS"] = "sel_section sel_subsection";
		$arProp["DATA"]["sect"] = "section";
		$arProp["DATA"]["subsect"] = "department";
	}
	if($prop == "SUBSECTION")
	{
		$arProp["CLASS"] = "sel_subsection";
		$arProp["DATA"]["subsect"] = "section";
	}
	if($prop == "DATE_TO" || "DATE_FROM")
		$arProp["CLASS"] = "start_date";

	$arResult["PROPERTY_LIST_FULL"][$prop] = $arProp;
}
$arResult["PROPERTY_LIST_GROUP"] = array(
	array(/*"DEPARTMENT", */"SECTION"/*, "SUBSECTION"*/, "DATE_FROM", "DATE_TO", "SEARCH"),
	array(/*"DATE_FROM", "DATE_TO", "SEARCH"*/),
	array(),
);
foreach ($arResult["PROPERTY_LIST"] as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][1]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}
?>