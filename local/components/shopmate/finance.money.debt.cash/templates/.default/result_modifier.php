<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/*$arResult["OUTGO"] = array(
	"report" => array(
		"ITEM_TYPE" => array(), //Yadadya\Shopmate\Internals\FinanceMoneyCatTable::getList()
		"ITEM_ID" => array(), //Bitrix\Main\FileTable::getList()
	),
	"contractor" => array(
		"ITEM_TYPE" => array(), //Yadadya\Shopmate\BitrixInternals\StoreDocsTable::getList()
		"ITEM_ID" => array(), //Yadadya\Shopmate\BitrixInternals\ContractorTable::getList()
	),
	"payroll" => array(
		"ITEM_TYPE" => array(), //Yadadya\Shopmate\Internals\FinanceMoneyCatTable::getList()
		"ITEM_ID" => array(), //Bitrix\Main\UserTable::getList()
	),
);*/

foreach ($arResult["ITEMS"] as $keyItem => $arItem)
{
	$arItem["REASON_URL"] = "/products/overhead/?edit=Y&CODE=".$arItem["ITEM_TYPE"];
	$arItem["MEMBERS_URL"] = "/contractors/?edit=Y&CODE=".$arItem["ITEM_ID"];

	$arResult["ITEMS"][$keyItem] = $arItem;
}
?>