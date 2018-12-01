<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $USER;
$arFirstSort = array("IBLOCK_SECTION", "NAME", "CAT_BARCODE", "CAT_MEASURE", "CAT_AMOUNT", "CAT_PURCHASING_PRICE", "CAT_PRICE", "CAT_SHELF_LIFE", "CAT_ARREARS", "CAT_PAYMENTS");
$arLastSort = array("DETAIL_TEXT");
$arEditSort = array("CAT_ARREARS", "CAT_PAYMENTS", "CAT_AMOUNT", "CAT_PRICE");
if(CModule::IncludeModule("yadadya.shopmate") && class_exists("SMEGAISAlcCode")) $arFirstSort[] = "CAT_ALCCODE";
$arOtherSort = array();
foreach($arResult["PROPERTY_LIST"] as $prop)
	if(!in_array($prop, array_merge($arFirstSort, $arLastSort)))
		$arOtherSort[] = $prop;
$arResult["PROPERTY_LIST"] = array_merge($arFirstSort, $arOtherSort, $arLastSort);


$arCustomProperties = array();

foreach($arResult["PROPERTY_LIST"] as $prop)
	if(stripos($prop, "CAT_") === 0)
		$arCustomProperties[] = $prop;

if(!empty($arParams["ID"]))
	$arLastSort[] = $arEditSort[] = $arResult["PROPERTY_LIST"][] = $arCustomProperties[] = "COMMENT";

if(!empty($arCustomProperties) && CModule::IncludeModule("catalog"))
	foreach($arCustomProperties as $prop)
	{
		switch($prop)
		{
			case "CAT_ALCCODE":
			case "CAT_BARCODE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "Y",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
				break;
			case "CAT_MEASURE":
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "L",
					"MULTIPLE" => "N",
					"ENUM" => array()
				);
				$dbResultList = CCatalogMeasure::getList(array("ID" => "ASC"));
				while($arMeasure = $dbResultList->Fetch())
					$arPropListFull["ENUM"][$arMeasure["ID"]]["VALUE"] = $arMeasure["MEASURE_TITLE"];
				break;
			default:
				$arPropListFull = Array(
					"PROPERTY_TYPE" => "S",
					"MULTIPLE" => "N",
					"COL_COUNT" => $arParams["DEFAULT_INPUT_SIZE"]
				);
		}
		$arResult["PROPERTY_LIST_FULL"][$prop] = $arPropListFull;
	}

$arResult["PROPERTY_LIST_FULL"]["IBLOCK_SECTION"]["MULTIPLE"] = "N";

$barcode_request = $_REQUEST["PROPERTY"]["CAT_BARCODE"];
if(!empty($barcode_request))
{
	if(is_array($barcode_request))
	{
		foreach($barcode_request as $bcode)
			if(!empty($bcode))
				$arResult["ELEMENT"]["CAT_BARCODE"][]["VALUE"] = $bcode;
	}
	else $arResult["ELEMENT"]["CAT_BARCODE"][]["VALUE"] = $barcode_request;
}
$alccode_request = $_REQUEST["PROPERTY"]["CAT_ALCCODE"];
if(!empty($alccode_request))
{
	if(is_array($alccode_request))
	{
		foreach($alccode_request as $acode)
			if(!empty($acode))
				$arResult["ELEMENT"]["CAT_ALCCODE"][]["VALUE"] = $acode;
	}
	else $arResult["ELEMENT"]["CAT_ALCCODE"][]["VALUE"] = $alccode_request;
}

$measure_request = $_REQUEST["PROPERTY"]["CAT_MEASURE"];
if($measure_request > 0) 
	$arResult["ELEMENT"]["CAT_MEASURE"][0]["VALUE"] = $measure_request;

$shelf_life_request = $_REQUEST["PROPERTY"]["CAT_SHELF_LIFE"][0];
if($shelf_life_request > 0) 
	$arResult["ELEMENT"]["CAT_SHELF_LIFE"][0]["VALUE"] = $shelf_life_request;

foreach($arResult["PROPERTY_LIST_FULL"] as $keyProperty => $arProperty)
	if(!in_array($keyProperty, $arEditSort) && !$USER->IsAdmin() && !in_array($arResult["ELEMENT"]["CREATED_BY"], CGroup::GetGroupUser(SMShops::getUserGroup())) && !empty($arParams["ID"]))
		$arResult["PROPERTY_LIST_FULL"][$keyProperty]["DISABLED"] = "Y";

$arParams = array_merge($arParams, Array(
	"ELEMENT_ID" => $arResult["ELEMENT"]["ID"],
	"ELEMENT_CODE" => $arResult["ELEMENT"]["CODE"],
	"SECTION_ID" => $arResult["ELEMENT"]["IBLOCK_SECTION_ID"],
	"TEMPLATE_RESULT" => $arResult
));
?>