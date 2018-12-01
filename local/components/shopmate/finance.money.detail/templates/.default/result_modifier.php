<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Yadadya\Shopmate\Components\Template,
	Bitrix\Main\Localization\Loc,
	Yadadya\Shopmate\Components\FinanceMoney,
	Yadadya\Shopmate\Components\Contractor,
	Yadadya\Shopmate\Components\Personal,
	Yadadya\Shopmate\Components\Overhead,
	Yadadya\Shopmate\Components\Products;
use Yadadya\Shopmate\BitrixInternals;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

if(!empty($_REQUEST["successMessage"]) && !empty($_REQUEST["CODE"]))
	LocalRedirect($APPLICATION->GetCurPageParam("", array("CODE")));

if($_REQUEST["add_price"] == "Y")
{
	$propListTemplate = array(
		"OUTGO" => array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "deposit"
		),
		"PRICE" => array(
			"TITLE" => Loc::getMessage("PRICE_DEPOSIT_TITLE"),
		),
		"DESCRIPTION" => array(
			"TITLE" => Loc::getMessage("DESCRIPTION_DEPOSIT_TITLE"),
		),
	);
	$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

	$arResult["PROPERTY_LIST_GROUP"] = array(
		array("TYPE", "OUTGO", "PRICE", "DESCRIPTION")
	);
}
else
{

	$personalEnum = ($arResult["ITEM"]["OUTGO"] == "payroll" && !empty($arResult["ITEM"]["ITEM_ID"])) ? Personal::getEnumList(array("ID" => $arResult["ITEM"]["ITEM_ID"])) : array();
	Personal::getEnumList(array("ID" => $arResult["ITEM"]["ITEM_ID"]));

	$contractorEnum = $overheadEnum = array();
	CBitrixComponent::includeComponentClass("shopmate:finance.money.debt");
	$debt = new CFinanceMoneyDebtComponent();
	$result = $debt->getList();
	while ($row = $result->fetch())
	{
		$overheadEnum[$row["ID"]] = array("VALUE" => $row["REASON"], "DATA" => array("parent" => $row["ITEM_ID"], "price" => $row["SUMM"]));
		if (!array_key_exists($row["ITEM_ID"], $contractorEnum))
			$contractorEnum[$row["ITEM_ID"]] = $row["MEMBERS"];
	}

	$componentPath = $this->getComponent()->getPath();
	$propListTemplate = array(
		"OUTGO" => array(
			"CLASS_LABEL" => "btn btn-default btn-bordered"
		),
	);

	$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);

	$outgoTemplate = array(
		"report" => array(
			"ITEM_TYPE" => array(
				"TITLE" => Loc::getMessage("ITEM_TYPE_REPORT_TITLE"),
				"PROPERTY_TYPE" => "L",
				"ENUM" => FinanceMoney::getReportCategoryList()
			),
			"PRICE" => array(
				"TITLE" => Loc::getMessage("PRICE_REPORT_TITLE"),
			),
			"ITEM_ID" => array(
				"TITLE" => Loc::getMessage("ITEM_ID_REPORT_TITLE"),
				"PROPERTY_TYPE" => "F",
			),
		),
		"contractor" => array(
			"ITEM_ID" => array(
				"TITLE" => Loc::getMessage("ITEM_ID_CONTRACTOR_TITLE"),
				"PROPERTY_TYPE" => "L",
				"CLASS" => "sel_section",
				//"LIST_TYPE" => "AJAX",
				"ENUM" => $contractorEnum,
				/*"DATA" => array(
					"url" => $componentPath."/search_contractor.php",
				),*/
			),
			"PRICE" => array(
				"TITLE" => Loc::getMessage("PRICE_CONTRACTOR_TITLE"),
				"CLASS" => "sel_subsection price_ins__paste",
			),
			"ITEM_TYPE" => array(
				"TITLE" => Loc::getMessage("ITEM_TYPE_CONTRACTOR_TITLE"),
				"PROPERTY_TYPE" => "L",
				"CLASS" => "sel_subsection price_ins__copy",
				//"LIST_TYPE" => "AJAX",
				"ENUM" => $overheadEnum,
				/*"DATA" => array(
					"url" => $componentPath."/search_overhead.php",
				),*/
			),
		),
		"payroll" => array(
			"ITEM_TYPE" => array(
				"TITLE" => Loc::getMessage("ITEM_TYPE_PAYROLL_TITLE"),
				"PROPERTY_TYPE" => "L",
				"ENUM" => FinanceMoney::getPayrollCategoryList()
			),
			"ITEM_ID" => array(
				"TITLE" => Loc::getMessage("ITEM_ID_PAYROLL_TITLE"),
				"PROPERTY_TYPE" => "L",
				"LIST_TYPE" => "AJAX",
				"ENUM" => $personalEnum,
				"DATA" => array(
					"url" => $componentPath."/search_personal.php",
				),
			),
			"PRICE" => array(
				"TITLE" => Loc::getMessage("PRICE_PAYROLL_TITLE"),
			),
		),
	);
	foreach ($outgoTemplate as $outgo => $template)
		$arResult["PROP_LIST"][$outgo] = Template::propListMerge($arResult["PROPERTY_LIST"], $template);

	$arResult["PROPERTY_LIST_GROUP"] = array(
		array("TYPE", "OUTGO"),
		array(
			"report" => array("ITEM_TYPE", "PRICE", "ITEM_ID"),
			"contractor" => array("ITEM_ID", "PRICE", "ITEM_TYPE"),
			"payroll" => array("ITEM_TYPE", "ITEM_ID", "PRICE", "PRICE_GRAY"),
		)
	);
}
?>