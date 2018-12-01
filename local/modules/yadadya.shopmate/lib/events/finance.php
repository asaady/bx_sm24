<?php
namespace Yadadya\Shopmate\Events;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Finance
{
	//OnShopmateCOverheadAdd
	public static function OnShopmateOverheadAdd(\Bitrix\Main\Event $event)
	{
		$prod = new \Yadadya\Shopmate\Components\Products;
		$errors = array();
		$parameters = $event->getParameters();
		self::OnOverheadAction($parameters["VALUE"]["ID"]);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}

	public static function OnOverheadAction($ITEM_ID)
	{
		if (\CModule::IncludeModule("catalog") && \CModule::IncludeModule("yadadya.shopmate"))
		{
			$arResult = array();
			if($arItem = \CCatalogDocs::getList(array("ID" => "ASC"), array("ID" => $ITEM_ID), false, false, array("ID", "DOC_TYPE", "PRODUCTS_STORE_TO", "SITE_ID", "CURRENCY", "STATUS", "TOTAL"))->Fetch())
				$arResult = array_merge($arResult, $arItem);
			if($arItem = \SMDocs::getList(array("ID" => "ASC"), array("DOC_ID" => $ITEM_ID), false, false, array("TOTAL_FACT", "NUMBER_DOCUMENT"))->Fetch())
				$arResult = array_merge($arResult, $arItem);
			if($arResult["ID"] > 0 && $arResult["DOC_TYPE"] == "A")
			{
				$payed_price = $arResult["STATUS"] == "Y" ? $arResult["TOTAL_FACT"] : 0;
				$credit_price = $arResult["STATUS"] == "Y" ? $arResult["TOTAL"] - $arResult["TOTAL_FACT"] : 0;
				\SMFinanceLog::Log($arResult["PRODUCTS_STORE_TO"], "OUT", "overhead", $arResult["ID"], "N", $payed_price, $arResult["CURRENCY"], $arResult["NUMBER_DOCUMENT"], $arResult["SITE_ID"]);
				\SMFinanceLog::Log($arResult["PRODUCTS_STORE_TO"], "OUT", "overhead", $arResult["ID"], "Y", $credit_price, $arResult["CURRENCY"], $arResult["NUMBER_DOCUMENT"], $arResult["SITE_ID"]);
			}
			elseif($arResult["ID"] <= 0 && $ITEM_ID > 0)
			{
				\SMFinanceLog::Log(\SMShops::getUserShop(), "OUT", "overhead", $ITEM_ID, "N", 0);
				\SMFinanceLog::Log(\SMShops::getUserShop(), "OUT", "overhead", $ITEM_ID, "Y", 0);
			}
		}
	}
}