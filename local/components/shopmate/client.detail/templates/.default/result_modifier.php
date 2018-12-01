<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
	Yadadya\Shopmate\Components\Template;

$componentPath = $this->getComponent()->getPath();
$propListTemplate = array(
	"PERSON_TYPE" => array(
		"REFRESH" => "Y",
		"CLASS" => "frm_rfrsh",
	),
	"WORK_COMPANY" => array(
		"REFRESH_ID" => 2,
		"DATA" => array(
			"info_set" => "WORK_COMPANY",
		),
		"CLASS" => "info_slave"
	),
	"INN" => array(
		"REFRESH_ID" => 2,
		"DATA" => array(
			"info_url" => $componentPath."/search_inn_info.php",
		),
	),
	"BIK" => array(
		"REFRESH_ID" => "2",
	),
	"OGRN" => array(
		"REFRESH_ID" => "2",
		"DATA" => array(
			"info_set" => "OGRN",
		),
		"CLASS" => "info_slave"
	),
	"NAME" => array(
		"REFRESH_TITLE" => Loc::getMessage("NAME_1_TITLE"),
		"REFRESH_TITLE_ID" => 1,
		"DATA" => array(
			"info_set" => "NAME",
		),
		"CLASS" => "info_slave"
	),
	"WORK_STREET" => array(
		"REFRESH_TITLE" => Loc::getMessage("ADDRESS_1_TITLE"),
		"REFRESH_TITLE_ID" => 1,
		"DATA" => array(
			"info_set" => "WORK_STREET",
		),
		"CLASS" => "info_slave"
	),

);

$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);
?>