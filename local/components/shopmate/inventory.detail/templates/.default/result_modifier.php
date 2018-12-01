<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Products;

$productEnum = array();
$componentPath = $this->getComponent()->getPath();
if ($_REQUEST["load_products"] == "Y" || $arResult["ITEM"]["ACTIVE"] == "Y")
{
	if(!empty($arResult["ITEM"]))
	{
		$elementsID = array();
		foreach($arResult["ITEM"]["PRODUCTS"] as $arElement)
			if($arElement["PRODUCT_ID"] > 0)
				$elementsID[] = $arElement["PRODUCT_ID"];
		if(!empty($elementsID))
			$productEnum = Products::getEnumByID($elementsID);
	}
	$propListTemplate = array(
		"PRODUCTS" => Array(
			"CLASS_ROW" => "product_block",
			"PROPERTY_LIST" => array(
				"PRODUCT_ID" => array(
					"PROPERTY_TYPE" => "L",
					"ENUM" => $productEnum,
					"DISABLED" => $arParams["ID"] > 0 ? "Y" : "N",
					"READONLY" => $arParams["ID"] > 0 ? "Y" : "N",
					"LIST_TYPE" => $arParams["ID"] > 0 ? "" : "AJAX",
					"DATA" => $arParams["ID"] > 0 ? [] : array(
						"url" => $componentPath."/search_element.php",
						"info_url" => $componentPath."/search_element_info.php"
					),
				),
			)
		)
	);
}
else
{
	$arResult["ITEM"]["PRODUCTS"] = array();

	$propListTemplate = array(
		"PRODUCTS" => Array(
			"CLASS_ROW" => "product_block",
			"PROPERTY_LIST" => array(
				"PRODUCT_ID" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "AJAX",
					"ENUM" => $productEnum,
					"CLASS" => "scanner_detection",
					"DATA" => array(
						"url" => $componentPath."/search_element.php",
						"info_url" => $componentPath."/search_element_info.php"
					),
				),
				"AMOUNT_PREV" => array(
					"DATA" => array(
						"info_set" => "AMOUNT",
					),
				),
				/*"MEASURE" => array(
					"TITLE" => "",
					"DISABLED" => "Y",
					"DATA" => array(
						"info_set" => "MEASURE",
					),
				),*/
				"AMOUNT" => array(
					"DATA" => array(
						"info_set" => "AMOUNT",
						"calc_input" => "QUANTITY",
					),
				),
				"PRICE" => array(
					"DATA" => array(
						"info_set" => "PRICE",
						"calc_input" => "PRICE",
					),
				),
				"SUMM" => array(
					"DATA" => array(
						"calc_input" => "SUMM",
					),
				),
			),
		),
	);
}

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

if($arResult["ITEM"]["ACTIVE"] == "N")
	Template::propListDisable($arResult["PROPERTY_LIST"]);
?>