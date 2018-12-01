<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Contractor,
	Yadadya\Shopmate\Components\Products;

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

$contractorEnum = !empty($arResult["ITEM"]["CONTRACTOR_ID"]) ? Contractor::getEnumList(array("ID" => $arResult["ITEM"]["CONTRACTOR_ID"])) : array();

$componentPath = $this->getComponent()->getPath();
$propListTemplate = array(
	"DATE_DOCUMENT" => array(
		"PLACEHOLDER" => "DD.MM.YYYY",
		"CLASS" => "start_date doc_date"
	),
	"CONTRACTOR_ID" => array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $contractorEnum,
		"CLASS" => "w100p price_user_id contractor_id",
		"DATA" => array(
			"url" => $componentPath."/search_contractor.php",
			"info_url" => $componentPath."/search_contractor_info.php",
			"add" => "/contractors/?edit=Y&ajax=y",
		),
	),
	"ELEMENT" => Array(
		"CLASS" => "table_products",
		"CLASS_ROW" => "product_block pricelist",
		"PROPERTY_LIST" => array(
			"ELEMENT_ID" => array(
				"PROPERTY_TYPE" => "L",
				"LIST_TYPE" => "AJAX",
				"ENUM" => $productEnum,
				"CLASS" => "scanner_detection element_id",
				"DATA" => array(
					"url" => $componentPath."/search_product.php",
					"info_url" => $componentPath."/search_product_info.php",
					"style" => "width: 200px; min-width: 200px; display: block;",
				),
			),
			"PURCHASING_PRICE" => array(
				"CLASS" => "width100 purchasing_price",
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
				"CLASS" => "shop_price pricelist_price",
				"DATA" => array(
					"info_set" => "PRICE",
					"update" => "/prodprice/?edit=Y&ajax=y",
				),
			),
			"AMOUNT" => array(
				"CLASS" => "product_amount",
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
				"CLASS" => "calc_summ__elem width100",
				"DATA" => array(
					"calc_input" => "PURCHASING_SUMM",
				),
			),
			"NDS_VALUE" => array(
				"CLASS" => "nds_value",
			),
			"START_DATE" => array(
				"HIDE_ICON" => "N",
				"CLASS" => "start_date",
				"PLACEHOLDER" => "DD.MM.YYYY",
				"DATA" => array(
					"info_set" => "START_DATE",
				),
			),
		),
	),
	"TOTAL_SUMM" => array(
		"CLASS" => "calc_summ__result input-lg",
		"DATA" => array(
			"calc_input" => "PURCHASING_SUMM",
		),
	),
);
$res = \Yadadya\Shopmate\Components\Pricelist::getList(array("select" => array("ID")));
if ($res->getSelectedRowsCount() > 1)
{
	$propListTemplate["ELEMENT"]["PROPERTY_LIST"]["ELEMENT_ID"]["CLASS"] .= " pricelist_product";
	$propListTemplate["ELEMENT"]["PROPERTY_LIST"]["SHOP_PRICE"]["CLASS"] .= " pricelist_price";
	$propListTemplate["ELEMENT"]["PROPERTY_LIST"]["SHOP_PRICE"]["DATA"]["update"] = "/prodprice/?edit=Y&ajax=y";
}

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

$arResult["PROPERTY_LIST_GROUP"] = array(
	array("NUMBER_DOCUMENT", "DATE_DOCUMENT", "CONTRACTOR_ID"),
	array("ELEMENT"),
	array("TOTAL_FACT"),
);
foreach (array_keys($arResult["PROPERTY_LIST"]) as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][1]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}

/*$cancel_btn = [];
foreach ($arResult["BUTTONS"] as $key => $value) 
	if ($value["NAME"] == "apply")
		unset($arResult["BUTTONS"][$key]);
	elseif ($value["NAME"] == "cancel")
	{
		$cancel_btn = $value;
		unset($arResult["BUTTONS"][$key]);
	}*/

/*if ($arParams["ID"] > 0 && $_REQUEST["print"] == "Y") 
{ 
	$APPLICATION->RestartBuffer(); 
	include("print.php"); 
	die(); 
}*/
?>