<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Products;



$componentPath = $this->getComponent()->getPath();
$propListTemplate = array(
	"DISCOUNT" => array(
		"CLASS" => "discount__value",
	),
	"PRODUCTS" => Array(
		"CLASS_ROW" => "discount__product",
		"PROPERTY_LIST" => array(
			"BASE_PRICE" => array(
				"CLASS" => "discount__base_price",
			),
			"PRICE" => array(
				"CLASS" => "discount__price",
			),
		),
	),
);

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);
?>