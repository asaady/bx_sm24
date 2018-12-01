<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Fabrica;

$productEnum = $connectEnum = array();
if(!empty($arResult["ITEM"]))
{
	$elementsID = array();
	foreach($arResult["ITEM"]["PRODUCT"] as $arElement)
		if($arElement["FABRICA_PROD_ID"] > 0)
			$elementsID[] = $arElement["FABRICA_PROD_ID"];
	if(!empty($elementsID))
		$productEnum = Fabrica::getEnumList(array("ID" => $elementsID));

	$elementsID = array();
	foreach($arResult["ITEM"]["CONNECT"] as $arElement)
		if($arElement["FABRICA_PROD_ID"] > 0)
			$elementsID[] = $arElement["FABRICA_PROD_ID"];
	if(!empty($elementsID))
		$connectEnum = Fabrica::getEnumList(array("ID" => $elementsID));
}

$componentPath = $this->getComponent()->getPath();
$propListTemplate = array(
	"PRODUCT" => Array(
		"CLASS" => "fabrica_part_products",
		"CLASS_ROW" => "load_info",
		"DATA" => array(
			"connect_url" => $componentPath."/search_connect.php",
		),
		"PROPERTY_LIST" => array(
			"FABRICA_PROD_ID" => array(
				"PROPERTY_TYPE" => "L",
				"LIST_TYPE" => "AJAX",
				"ENUM" => $productEnum,
				"CLASS" => "fabrica_part_product",
				"MULTIPLE" => "N",
				"DATA" => array(
					"url" => $componentPath."/search_element.php" . ($_REQUEST["merge"] == "Y" ? "?type=1" : ($_REQUEST["split"] == "Y" ? "?type=2" : "")),
					"info_url" => $componentPath."/search_element_info.php",
					"style" => "width: 200px; min-width: 200px; display: block;",
				),
			),
			"MEASURE" => array(
				"CLASS" => "width50 nopadding-left",
				"DATA" => array(
					"info_set" => "MEASURE",
				),
			),
		),
	),
	"CONNECT" => Array(
		"CLASS" => "fabrica_part_connects",
		"CLASS_ROW" => "load_info",
		"PROPERTY_LIST" => array(
			"FABRICA_PROD_ID" => array(
				"PROPERTY_TYPE" => "L",
				"ENUM" => $connectEnum,
				"READONLY" => "Y",
				"CLASS" => "fabrica_part_connect",
				"DATA" => array(
					//"url" => $componentPath."/search_element.php",
					"info_url" => $componentPath."/search_element_info.php",
					"style" => "width: 200px; min-width: 200px; display: block;",
				),
			),
			"MEASURE" => array(
				"CLASS" => "width50 nopadding-left",
				"DATA" => array(
					"info_set" => "MEASURE",
				),
			),
		),
	),
);

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fabrica/fabricapart.js");
?>