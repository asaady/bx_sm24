<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
foreach ($arResult["ITEMS"] as $keyItem => $arItem) 
{
	$arItem["PRICE"] = PriceFormat($arItem["PRICE"]);
	$arItem["SUM_NOPAID"] = PriceFormat($arItem["SUM_NOPAID"]);
	if (empty($arItem["ACCOUNT_NUMBER"]))
		$arItem["ACCOUNT_NUMBER"] = $arItem["ID"];
	$arResult["ITEMS"][$keyItem] = $arItem;
}

use Yadadya\Shopmate\Components\Cash;
$cash = new Cash;
$parametrs = array(
	"select" => array("AVG_SUMM", "CNT"),
	"filter" => $cash->getFilter($cash->checkFields($_REQUEST, Cash::getFilterList()))
);
if ($overhead = $cash->getList($parametrs)->fetch())
{
	$arResult["ITEM"]["ID"] = "N = ".PriceFormat($overhead["CNT"]);
	$arResult["ITEM"]["PRICE"] = "∑/N = ".PriceFormat($overhead["AVG_SUMM"]);
}