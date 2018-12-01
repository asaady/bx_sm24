<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach($arResult["PROPERTY_LIST_FULL"] as $prop => $arProp)
{
	if($prop == "PERSON_TYPE")
		$arProp["REFRESH"] = "Y";
	if(in_array($prop, array("WORK_COMPANY", "INN", "BIK", "OGRN")))
		$arProp["REFRESH_ID"] = 2;
	if($prop == "PERSON_NAME")
	{
		$arProp["REFRESH_TITLE"] = "ФИО";
		$arProp["REFRESH_TITLE_ID"] = 1;
	}
	if($prop == "ADDRESS")
	{
		$arProp["REFRESH_TITLE"] = "Адрес";
		$arProp["REFRESH_TITLE_ID"] = 1;
	}

	$arResult["PROPERTY_LIST_FULL"][$prop] = $arProp;
}
?>