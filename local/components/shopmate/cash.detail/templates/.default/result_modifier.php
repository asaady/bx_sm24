<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 
 
use Yadadya\Shopmate\Components\Template, 
	Yadadya\Shopmate\Components\Client, 
	Yadadya\Shopmate\Components\Products; 
 
$productEnum = array(); 
if(!empty($arResult["ITEM"])) 
{ 
	$elementsID = array(); 
	foreach($arResult["ITEM"]["PRODUCT"] as $arElement) 
		if($arElement["PRODUCT_ID"] > 0) 
			$elementsID[] = $arElement["PRODUCT_ID"]; 
	if(!empty($elementsID)) 
		$productEnum = Products::getEnumList(array("ID" => $elementsID)); 
} 
 
$userEnum = !empty($arResult["ITEM"]["USER_ID"]) ? Client::getEnumList(array("ID" => $arResult["ITEM"]["USER_ID"])) : array(); 
 
$componentPath = $this->getComponent()->getPath(); 
$propListTemplate = array( 
	"USER_ID" => array( 
		"PROPERTY_TYPE" => "L", 
		"LIST_TYPE" => "AJAX", 
		"ENUM" => $userEnum, 
		"CLASS" => "price_user_id", 
		"DATA" => array( 
			"url" => $componentPath."/search_user.php", 
		), 
	), 
	"SUM_NOPAID" => array( 
		"PROPERTY_TYPE" => $arParams["ID"] > 0 ? "N" : "H" 
	), 
	"PRODUCT" => Array( 
		"CLASS_ROW" => "product_block scanner_detection_add", 
		"PROPERTY_LIST" => array( 
			"PRODUCT_ID" => array( 
				"PROPERTY_TYPE" => "L", 
				"LIST_TYPE" => "AJAX", 
				"ENUM" => $productEnum, 
				"CLASS" => "scanner_detection", 
				"DATA" => array( 
					"url" => $componentPath."/search_product.php", 
					"info_url" => $componentPath."/search_product_info.php", 
					"style" => "width: 200px; min-width: 200px; display: block;", 
				), 
			), 
			"QUANTITY" => array( 
				"CLASS" => "product_quantity keyboard keyboard_np", 
				"DEFAULT_VALUE" => 1, 
				"DATA" => array( 
					"info_set" => "CAT_QUANTITY", 
					"calc_input" => "QUANTITY", 
				), 
			), 
			"AMOUNT" => array( 
				"CLASS" => "info_slave",
				"DATA" => array( 
					"info_set" => "CAT_AMOUNT", 
				), 
			), 
			"PRICE" => array( 
				"CLASS" => "product_price keyboard keyboard_np info_slave", 
				"DATA" => array( 
					"info_set" => "DISCOUNT_PRICE", 
					"calc_input" => "PRICE", 
				), 
			), 
			"SUMM" => array( 
				"CLASS" => "calc_summ__elem", 
				"DATA" => array( 
					"calc_input" => "SUMM", 
				), 
			), 
		), 
	), 
	"PRICE" => array( 
		"PROPERTY_TYPE" => "H", 
		"CLASS" => "calc_summ__result input-lg", 
		"DATA" => array( 
			"calc_input" => "PURCHASING_SUMM", 
		), 
	), 
); 
 
$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate); 
 
if ($arParams["ID"] > 0 && $_REQUEST["print"] == "Y") 
{ 
	foreach ($arResult["ITEM"]["PRODUCT"] as $keyProd => $arProduct)  
	{ 
		$arProduct["SUMM"] = PriceFormat($arProduct["PRICE"] * $arProduct["QUANTITY"]); 
		$arProduct["PRICE"] = PriceFormat($arProduct["PRICE"]); 
		$arResult["ITEM"]["PRODUCT"][$keyProd] = $arProduct; 
	} 
	$arResult["ITEM"]["PRICE"] = PriceFormat($arResult["ITEM"]["PRICE"]); 
	$arResult["ITEM"]["SUM_NOPAID"] = PriceFormat($arResult["ITEM"]["SUM_NOPAID"]); 
 
	unset($arResult["PROPERTY_LIST"]["PRODUCT"]["PROPERTY_LIST"]["AMOUNT"]); 
	unset($arResult["PROPERTY_LIST"]["PRODUCT"]["PROPERTY_LIST"]["ID"]); 
	$arResult["PROPERTY_LIST"]["PRODUCT"]["DISABLED"] = "Y"; 
	$arResult["PROPERTY_LIST"]["PRICE"]["PROPERTY_TYPE"] = "N"; 
	$arResult["PROPERTY_LIST"]["PAID"]["PROPERTY_TYPE"] = "H"; 
	foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty)  
		$arResult["PROPERTY_LIST"][$propertyID]["PRINT"] = "Y"; 
 
	$APPLICATION->RestartBuffer(); 
	include("print.php"); 
	die(); 
} 
?>