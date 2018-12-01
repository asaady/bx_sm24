<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Yadadya\Shopmate\Components\Template,
	Yadadya\Shopmate\Components\Products,
	Yadadya\Shopmate\BitrixInternals\ContractorTable,
	Bitrix\Iblock\SectionTable,
	Bitrix\Main\Localization\Loc;

$componentPath = $this->getComponent()->getPath();
$sectionEnum = $subsectionEnum = array();
$tmpSectionEnum = Products::getSectionsEnum(1000);
foreach ($tmpSectionEnum as $sectionId => $section)
	$sectionEnum[$sectionId] = str_repeat("- ", empty($section["DEPTH_LEVEL"]) ? 1 : $section["DEPTH_LEVEL"]).$section["VALUE"];
	/*if($section["DEPTH_LEVEL"] > 1)
		$subsectionEnum[$sectionId] = array("VALUE" => $section["VALUE"], "DATA" => array("parent" => $section["SECTION_ID"]));
	else
		$sectionEnum[$sectionId] = $section["VALUE"];*/

$contractorEnum = array();
if(!empty($arResult["ITEM"]["CONTRACTOR"]))
{
	$result = ContractorTable::GetList(array(
		"select" => array("ID", "PERSON_TYPE", "COMPANY", "PERSON_NAME", "PHONE"),
		"filter" => array("ID" => $arResult["ITEM"]["CONTRACTOR"])
	));
	while ($row = $result->fetch())
		$contractorEnum[$row["ID"]] = ($row["PERSON_TYPE"] == 2 ? $row["COMPANY"]." (".$row["PERSON_NAME"].")" : $row["PERSON_NAME"]).(strlen($row["PHONE"]) ? ", т. ".$row["PHONE"] : "");
}

$packEnum = array();
$rsPack = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"] > 0 ? $arParams["IBLOCK_ID"] : \Yadadya\Shopmate\Options::getCatalogID(), "CODE" => "PACK"));
while($arPack = $rsPack->GetNext())
	$packEnum[$arPack["ID"]] = array("VALUE" => $arPack["VALUE"]);

$productEnum = !empty($arResult["ITEM"]["PRODUCT"]) ? Products::getEnumList(array("ID" => $arResult["ITEM"]["PRODUCT"])) : array();

$propListTemplate = array(
	"SECTION" => Array(
		"PROPERTY_TYPE" => "L",
		"ENUM" => $sectionEnum
	),
	/*"SECTION" => Array(
		"PROPERTY_TYPE" => "L",
		"CLASS" => "sel_section",
		"ENUM" => $sectionEnum
	),
	"SUBSECTION" => Array(
		"PROPERTY_TYPE" => "L",
		"CLASS" => "sel_subsection",
		"ENUM" => $subsectionEnum
	),*/
	/*"OVERHEAD_DATE" => array(
		"CLASS" => "start_date"
	),*/
	"STORE_PRODUCT" => array(
		"ENUM" => array(
			"ALL" => Loc::getMessage("STORE_PRODUCT_ALL"),
			"BASE" => Loc::getMessage("STORE_PRODUCT_BASE"),
			"STORE" => Loc::getMessage("STORE_PRODUCT_STORE"),
		)
	),
	/*"PACK" => array(
		"PROPERTY_TYPE" => "L",
		"ENUM" => $packEnum
	),*/
	"PRODUCT" => array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $productEnum,
		"CLASS" => "w100p",
		"DATA" => array(
			"url" => $componentPath."/search_product.php",
		),
		"NEW_LINE" => "Y"
	),
	"CONTRACTOR" => array(
		"PROPERTY_TYPE" => "L",
		"LIST_TYPE" => "AJAX",
		"ENUM" => $contractorEnum,
		"CLASS" => "scanner_detection",
		"DATA" => array(
			"url" => $componentPath."/search_contractor.php",
		),
	),
	"PERISHABLE" => array(
		"ENUM" => array(
			1 => $arResult["PROPERTY_LIST"]["PERISHABLE"]["TITLE"],
		)
	)
);

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

$arResult["PROPERTY_LIST_GROUP"] = array(
	array("SECTION", "SUBSECTION", "OVERHEAD_DATE", "PACK"),
	array(),
	array("STORE_PRODUCT", "PRODUCT", "CONTRACTOR"),
	array("PERISHABLE"),
);
foreach (array_keys($arResult["PROPERTY_LIST"]) as $value)
{
	if(!(in_array($value, $arResult["PROPERTY_LIST_GROUP"][0]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][2]) || in_array($value, $arResult["PROPERTY_LIST_GROUP"][3])))
		$arResult["PROPERTY_LIST_GROUP"][1][] = $value;
}
?>