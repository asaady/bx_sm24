<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?foreach($arParams["PROPERTY_CODES"] as $property_code)
{
	if(is_numeric($property_code)) $arParams["PROPERTY_CODE"][] = $property_code;
	else $arParams["SECTION_USER_FIELDS"][] = $property_code;
}
if(!empty($arParams["PROPERTY_CODE"]) && CModule::IncludeModule("iblock"))
{
	$properties = CIBlockProperty::GetList(Array("IBLOCK_ID" => $arParams["IBLOCK_ID"]));
	while($prop_fields = $properties->GetNext())
		if(($prop_key = array_search($prop_fields["ID"], $arParams["PROPERTY_CODE"])) !== false)
			$arParams["PROPERTY_CODE"][$prop_key] = $prop_fields["CODE"];
}

$userPrice = false;
if(CModule::IncludeModule("yadadya.shopmate"))
	$userPrice = SMShops::getUserPriceName();

if(stripos($this->GetFile(), "list.php") !== false)
	$arParams = array_merge(Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_USER_FIELDS" => $arParams["SECTION_USER_FIELDS"],
		"ELEMENT_SORT_FIELD" => "timestamp_x",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arrProductsFilter",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SET_STATUS_404" => "Y",
		"PAGE_ELEMENT_COUNT" => $arParams["NAV_ON_PAGE"],
		"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
		"PRICE_CODE" => array($userPrice),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_NOTES" => "",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"CONVERT_CURRENCY" => "N",
	), $arParams);
elseif(stripos($this->GetFile(), "detail.php") !== false)
	$arParams = array_merge(Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
		"PRICE_CODE" => array($userPrice),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => "Y",
		"CONVERT_CURRENCY" => "N"
	), $arParams);
$arParams["GROUPS"] = $USER->GetUserGroupArray();

if(stripos($this->GetFile(), "detail.php") !== false && (!empty($_POST["iblock_submit"]) || !empty($_POST["iblock_apply"])) && (empty($arParams["ID"]) || !empty($_REQUEST["PROPERTY"]["COMMENT"][0])))
{
	$arParams["ID"] = intval($_REQUEST["CODE"]);
	foreach ($_REQUEST["PROPERTY_OLD"] as $propertyID => $propertyValue) 
		if(in_array($propertyID, array("IBLOCK_SECTION", "CAT_MEASURE")) || intval($propertyID) > 0)
			$_REQUEST["PROPERTY_OLD"][$propertyID] = $propertyValue[0];

	$arSave = SMTodo::prepChanges($_REQUEST["PROPERTY"], $_REQUEST["PROPERTY_OLD"]);
	if(!empty($arSave["CHANGES"]) || !empty($arSave["DELETES"]))
		$TodoID = SMTodoLog::Log(SMShops::getUserShop(), "product", $arParams["ID"] > 0 ? $arParams["ID"] : 0, serialize($arSave), $USER->IsAdmin() || SM::GetUserPermission("products") >= "X" ? "Y" : "N");
}
?>