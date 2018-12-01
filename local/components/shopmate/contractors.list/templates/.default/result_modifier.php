<?
use Bitrix\Main\Localization\Loc;
foreach ($arResult["ITEMS"] as $key => $arItem) 
{
	$arResult["ITEMS"][$key]["DEBT"] = $arItem["DEBT"] > 0 ? PriceFormat(-1*$arItem["DEBT"]) : Loc::getMessage("DEBT_NO");
}