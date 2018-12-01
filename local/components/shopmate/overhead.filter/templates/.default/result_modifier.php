<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Contractor,
	Yadadya\Shopmate\Components\Products,
	Yadadya\Shopmate\BitrixInternals\ContractorTable,
	Bitrix\Iblock\SectionTable,
	Bitrix\Main\Localization\Loc;

$componentPath = $this->getComponent()->getPath();

$contractorEnum = !empty($arResult["ITEM"]["CONTRACTOR"]) ? Contractor::getEnumList(array("ID" => $arResult["ITEM"]["CONTRACTOR"])) : array();

$productEnum = !empty($arResult["ITEM"]["PRODUCT"]) ? Products::getEnumList(array("ID" => $arResult["ITEM"]["PRODUCT"])) : array();

$propListTemplate = array(
	"CONTRACTOR" => array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $contractorEnum,
		"CLASS" => "w100p",
		"DATA" => array(
			"url" => $componentPath."/search_contractor.php",
		),
	),
	"PRODUCT" => array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $productEnum,
		"CLASS" => "w100p",
		"DATA" => array(
			"url" => $componentPath."/search_product.php",
		),
	),
	"DATE_FROM" => array(
		"CLASS" => "start_date"
	),
	"DATE_TO" => array(
		"CLASS" => "start_date"
	),
	"PERISHABLE" => array(
		"ENUM" => array(
			1 => $arResult["PROPERTY_LIST"]["PERISHABLE"]["TITLE"],
		)
	),
	"EXPIRATION" => array(
		"ENUM" => array(
			1 => $arResult["PROPERTY_LIST"]["EXPIRATION"]["TITLE"],
		)
	)
);

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

$arResult["PROPERTY_LIST_GROUP"] = array(
	array("CONTRACTOR", "PRODUCT", "DATE_FROM", "DATE_TO"),
	array(),
	array("PERISHABLE", "EXPIRATION"),
);
foreach (array_keys($arResult["PROPERTY_LIST"]) as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}
?>