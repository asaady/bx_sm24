<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arFirstSort = array("IBLOCK_SECTION", "NAME", "CAT_BARCODE", "CAT_MEASURE", "CAT_AMOUNT", "CAT_PURCHASING_PRICE", "CAT_PRICE", "CAT_ARREARS", "CAT_PAYMENTS");
$arLastSort = array("DETAIL_TEXT");
$arEditSort = array("CAT_ARREARS", "CAT_PAYMENTS", "CAT_AMOUNT", "CAT_PRICE");
$arOtherSort = array();
foreach($arResult["PROPERTY_LIST"] as $prop)
	if(!in_array($prop, array_merge($arFirstSort, $arLastSort)))
		$arOtherSort[] = $prop;
$arResult["PROPERTY_LIST"] = array_merge($arFirstSort, $arOtherSort, $arLastSort);


$arCustomProperties = array();

foreach($arResult["PROPERTY_LIST"] as $prop)
	if(stripos($prop, "CAT_") === 0)
		$arCustomProperties[] = $prop;

if(!empty($arCustomProperties) && CModule::IncludeModule("catalog"))
	foreach($arCustomProperties as $prop)
	{
		switch($prop)
		{
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

$barcode_request = &$_REQUEST["PROPERTY"]["CAT_BARCODE"];
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

$measure_request = &$_REQUEST["PROPERTY"]["CAT_MEASURE"];
if($measure_request > 0) 
	$arResult["ELEMENT"]["CAT_MEASURE"][0]["VALUE"] = $measure_request;

foreach($arResult["PROPERTY_LIST_FULL"] as $keyProperty => $arProperty)
	if(!in_array($keyProperty, $arEditSort))
		$arResult["PROPERTY_LIST_FULL"][$keyProperty]["DISABLED"] = "Y";

$arParams = array_merge($arParams, Array(
	"ELEMENT_ID" => $arResult["ELEMENT"]["ID"],
	"ELEMENT_CODE" => $arResult["ELEMENT"]["CODE"],
	"SECTION_ID" => $arResult["ELEMENT"]["IBLOCK_SECTION_ID"],
	"TEMPLATE_RESULT" => $arResult
));
?>