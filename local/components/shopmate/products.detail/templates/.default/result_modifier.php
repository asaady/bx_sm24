<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Products;

$componentPath = $this->getComponent()->getPath();
$propListTemplate = array(
	"SHELF_LIFE" => array(
		/*"CLASS" => "start_date",
		"USER_TYPE" => "DateTime"*/
		"CLASS" => "shelf_life"
	),
	"ID" => Array(
		"PROPERTY_TYPE" => "H",
		// "CLASS" => "pricelist_product"
	),
	"PRICE" => Array(
		// "CLASS" => "pricelist_price",
		// "DATA" => array(
		// 	"update" => "/prodprice/?edit=Y&ajax=y",
		// ),
	),
);

$res = \Yadadya\Shopmate\Components\Pricelist::getList(array("select" => array("ID")));
if ($res->getSelectedRowsCount() > 1)
{
	$propListTemplate["ID"]["CLASS"] = "pricelist_product";
	$propListTemplate["PRICE"]["CLASS"] = "pricelist_price";
	$propListTemplate["PRICE"]["DATA"]["update"] = "/prodprice/?edit=Y&ajax=y";
}

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

/*if($arResult["ELEMENT"]["ACTIVE"] == "N")
	Template::propListDisable($arResult["PROPERTY_LIST"]);*/
?>