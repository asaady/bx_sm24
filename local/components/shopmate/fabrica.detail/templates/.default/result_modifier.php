<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Products;

$arResult["ITEM"]["ITEMS"] = (array) $arResult["ITEM"]["ITEMS"];
array_unshift($arResult["ITEM"]["ITEMS"], array("SAMPLE" => "Y", "ID" => "sample_id"));

$productEnum = array();
if(!empty($arResult["ITEM"]["ITEMS"]))
{
	$elementsID = array();
	foreach($arResult["ITEM"]["ITEMS"] as $arElement)
		if($arElement["PRODUCT_ID"] > 0)
			$elementsID[] = $arElement["PRODUCT_ID"];
	if(!empty($elementsID))
		$productEnum = Products::getEnumList(array("ID" => $elementsID));
}

$componentPath = $this->getComponent()->getPath();
$propListTemplate = array(
	"ITEMS" => Array(
		"PROPERTY_LIST" => array(
			"ID" => array(
				"PROPERTY_TYPE" => "H",
				"CLASS" => "item_id"
			),
			"PRODUCT_ID" => array(
				"PROPERTY_TYPE" => "L",
				"ENUM" => $productEnum,
				"CLASS" => "product_select item_search",
				"DATA" => array(
					"url" => $componentPath."/search_element.php",
					"info_url" => $componentPath."/search_element_info.php",
				),
				"PLACEHOLDER" => "Поиск товара",
			),
			"NAME" => array(
				"PLACEHOLDER" => "Придумайте название", 
				"CLASS" => "item_name"
			),
			"MEASURE" => array(
				"CLASS" => "item_measure info_slave",
				"DATA" => array(
					"info_set" => "CAT_MEASURE",
				),
			),
			"TYPE" => array(
				"CLASS_LABEL" => "btn btn-default btn-bordered",
				"CLASS" => "item_type",
				"TEMPLATE" => "#INPUT#"
			),
			"FAULT_RATIO" => array(
				"DATA" => array(
					"toggle" => "tooltip",
					"placement" => "top",
				),
				"ATTR" => array(
					"title" => "Допустимая погрешность соотношений Ингридиентов в %"
				)
			),
			"MEASURE_FROM" => array(
				"CLASS" => "width50 measure_translate__input",
				"DEFAULT_VALUE" => 1,
			),
			"MEASURE_TO" => array(
				"CLASS" => "width50 measure_translate__input",
				"DEFAULT_VALUE" => 1,
			),
			"CONNECT" => array(
				"PROPERTY_LIST" => array(
					"PRODUCT_ID" => array(
						"PROPERTY_TYPE" => "L",
						"ENUM" => $productEnum,
						"CLASS" => "product_select item_connect__search ajax_select_add",
						"DATA" => array(
							"url" => $componentPath."/search_element.php",
							"info_url" => $componentPath."/search_element_info.php",
						),
						"PLACEHOLDER" => "Поиск товара",
					),
					"NAME" => array(
						"CLASS" => "item_connect__name",
						"PLACEHOLDER" => "Придумайте название"
					),
					"MEASURE" => array(
						"CLASS" => "width100 measure_input",
					),
					"MEASURE_FROM" => array(
						"CLASS" => "width50 measure_translate__input",
						"DEFAULT_VALUE" => 1,
					),
					"MEASURE_TO" => array(
						"CLASS" => "width50 measure_translate__input",
						"DEFAULT_VALUE" => 1,
					),
					"AMOUNT_RATIO" => array(
					),
					"ID" => array(
						"CLASS" => "item_connect__id",
					),
				)
			),
		),
	),
);

$dataTypeEnum = array(0 => "type_simple", 1 => "type_merge", 2 => "type_split");
foreach ($arResult["PROPERTY_LIST"]["ITEMS"]["PROPERTY_LIST"]["TYPE"]["ENUM"] as $key => $value) 
	$propListTemplate["ITEMS"]["PROPERTY_LIST"]["TYPE"]["ENUM"][$key] = array("VALUE" => $value, "DATA" => array("type" => $dataTypeEnum[$key]));

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);