<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Contractor,
	Yadadya\Shopmate\Components\Products,
	Yadadya\Shopmate\Components\Store;

$propListTemplate = array();
$componentPath = $this->getComponent()->getPath();

if ($arResult["PROPERTY_LIST"]["STORE_ID"]["PROPERTY_TYPE"] != "H")
{
	$storeEnum = !empty($arResult["ITEM"]["STORE_ID"]) ? Store::getEnumList(array("ID" => $arResult["ITEM"]["STORE_ID"])) : array();
	$propListTemplate["STORE_ID"] = array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $storeEnum,
		"DATA" => array(
			"url" => $componentPath."/search_store.php",
		)
	);
}

if (empty($arResult["ITEM"]["XML"]) && isset($arResult["PROPERTY_LIST"]["XML"]))
{
	$propListTemplate["XML"] = array(
		"REQUIRED" => "Y",
	);
}

if (isset($arResult["PROPERTY_LIST"]["CONTRACTOR_ID"]))
{
	$contractorEnum = !empty($arResult["ITEM"]["CONTRACTOR_ID"]) ? Contractor::getEnumList(array("ID" => $arResult["ITEM"]["CONTRACTOR_ID"])) : array();
	$propListTemplate["CONTRACTOR_ID"] = array(
		"PROPERTY_TYPE" => "L",
		//"LIST_TYPE" => "AJAX",
		"ENUM" => $contractorEnum,
		"CLASS" => "w100p",
		/*"DATA" => array(
			"url" => $componentPath."/search_contractor.php",
		),*/
	);
}

if (isset($arResult["PROPERTY_LIST"]["ELEMENT"]))
{
	$productEnum = array();
	if(!empty($arResult["ITEM"]))
	{
		$elementsID = array();
		foreach($arResult["ITEM"]["ELEMENT"] as $arElement)
			if($arElement["ELEMENT_ID"] > 0)
				$elementsID[] = $arElement["ELEMENT_ID"];
		if(!empty($elementsID))
			$productEnum = Products::getEnumList(array("ID" => $elementsID));
	}

	$propListTemplate["ELEMENT"] = array(
		"CLASS_ROW" => "product_block",
		"PROPERTY_LIST" => array(
			"ELEMENT_ID" => array(
				"PROPERTY_TYPE" => "L",
				"LIST_TYPE" => "AJAX",
				"ENUM" => $productEnum,
				"CLASS" => "scanner_detection",
				"DATA" => array(
					"url" => $componentPath."/search_element.php",
					"info_url" => $componentPath."/search_element_info.php",
					"style" => "width: 200px; min-width: 200px; display: block;",
				),
			),
			"PURCHASING_PRICE" => array(
				"CLASS" => "purchasing_price",
				"DATA" => array(
					"info_set" => "PURCHASING_PRICE",
					"calc_input" => "PURCHASING_PRICE",
				),
			),
			"MEASURE" => array(
				"CLASS" => "width50 nopadding-left",
				"DATA" => array(
					"info_set" => "CAT_MEASURE",
				),
			),
			"SHOP_PRICE" => array(
				"CLASS" => "shop_price",
				"DATA" => array(
					"info_set" => "PRICE",
				),
			),
			"DOC_AMOUNT" => array(
				"CLASS" => "product_quantity",
				"DATA" => array(
					"info_set" => "CAT_QUANTITY",
					"calc_input" => "PURCHASING_AMOUNT",
				),
			),
			"PURCHASING_NDS" => array(
				"CLASS" => "purchasing_nds_selecter width100",
				"DATA" => array(
					"info_set" => "NDS",
					"calc_input" => "PURCHASING_NDS",
				),
			),
			"PURCHASING_SUMM" => array(
				"CLASS" => "calc_summ__elem",
				"DATA" => array(
					"calc_input" => "PURCHASING_SUMM",
				),
			),
			"NDS_VALUE" => array(
				"CLASS" => "nds_value",
			),
			"START_DATE" => array(
				"HIDE_ICON" => "Y",
				"CLASS" => "start_date",
				"PLACEHOLDER" => "DD.MM.YYYYY",
				"DATA" => array(
					"info_set" => "START_DATE",
				),
			),
		),
	);
}
if (isset($arResult["PROPERTY_LIST"]["TOTAL_SUMM"]))
{
	$propListTemplate["TOTAL_SUMM"] = array(
		"CLASS" => "calc_summ__result input-lg",
		"DATA" => array(
			"calc_input" => "PURCHASING_SUMM",
		),
	);
}

/*$productEnum = array();
if(!empty($arResult["ITEM"]))
{
	$elementsID = array();
	foreach($arResult["ITEM"]["ELEMENT"] as $arElement)
		if($arElement["ELEMENT_ID"] > 0)
			$elementsID[] = $arElement["ELEMENT_ID"];
	if(!empty($elementsID))
		$productEnum = Products::getEnumList(array("ID" => $elementsID));
}*/


	/*"CONTRACTOR_ID" => array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $contractorEnum,
		"CLASS" => "w100p",
		"DATA" => array(
			"url" => $componentPath."/search_contractor.php",
		),
	),
	"ELEMENT" => Array(
		"CLASS_ROW" => "product_block",
		"PROPERTY_LIST" => array(
			"ELEMENT_ID" => array(
				"PROPERTY_TYPE" => "L",
				"LIST_TYPE" => "AJAX",
				"ENUM" => $productEnum,
				"CLASS" => "scanner_detection",
				"DATA" => array(
					"url" => $componentPath."/search_element.php",
					"info_url" => $componentPath."/search_element_info.php",
					"style" => "width: 200px; min-width: 200px; display: block;",
				),
			),*/


$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);
?>