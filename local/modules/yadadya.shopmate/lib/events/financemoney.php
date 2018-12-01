<?php
namespace Yadadya\Shopmate\Events;

use Yadadya\Shopmate\Internals;
use Yadadya\Shopmate;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceMoney
{
	//OnBeforeShopmateCOverheadAdd, OnBeforeShopmateCOverheadUpdate
	public static function OnBeforeShopmateOverheadAction(\Bitrix\Main\Event $event)
	{
		global $arFinanceMoneyOverhead;
		$parameters = $event->getParameters();
		if($parameters["VALUE"]["ID"] > 0)
			$arFinanceMoneyOverhead = \Yadadya\Shopmate\Components\Overhead::getList(array(
				"select" => array("ID", "CONTRACTOR_ID", "TOTAL_SUMM", "TOTAL_FACT"),
				"filter" => array("ID" => $parameters["VALUE"]["ID"])
			))->fetch();
		else $arFinanceMoneyOverhead = array();
	}

	//OnShopmateCOverheadAdd, OnShopmateCOverheadUpdate
	public static function OnShopmateOverheadAction(\Bitrix\Main\Event $event)
	{
		global $arFinanceMoneyOverhead;
		$parameters = $event->getParameters();
		$PRICE = $parameters["VALUE"]["TOTAL_FACT"] - $arFinanceMoneyOverhead["TOTAL_FACT"];
		if($PRICE != 0)
		{
			$data = array(
				"TYPE" => "cash",
				"OUTGO" => "contractor",
				"ITEM_TYPE" => $parameters["VALUE"]["ID"],
				"ITEM_ID" => $parameters["VALUE"]["CONTRACTOR_ID"],
				"PRICE" => $PRICE,
			);
			$result = \Yadadya\Shopmate\Components\FinanceMoney::add($data);
		}
	}

	//OnBeforeShopmateCCashAdd, OnBeforeShopmateCCashUpdate
	public static function OnBeforeShopmateCashAction(\Bitrix\Main\Event $event)
	{
		global $arFinanceMoneyCash;
		$parameters = $event->getParameters();
		if($parameters["VALUE"]["ID"] > 0)
			$arFinanceMoneyCash = \Yadadya\Shopmate\Components\Cash::getList(array(
				"select" => array("ID", "USER_ID", "PRICE", "SUM_PAID"),
				"filter" => array("ID" => $parameters["VALUE"]["ID"])
			))->fetch();
		else $arFinanceMoneyCash = array();
	}

	//OnShopmateCCashAdd, OnShopmateCCashUpdate
	public static function OnShopmateCashAction(\Bitrix\Main\Event $event)
	{
		global $arFinanceMoneyCash;
		$parameters = $event->getParameters();
		if (empty($parameters["VALUE"]["SUM_PAID"]))
		{
			$newFinanceMoneyCash = \Yadadya\Shopmate\Components\Cash::getList(array(
				"select" => array("ID", "USER_ID", "PRICE", "SUM_PAID"),
				"filter" => array("ID" => $parameters["VALUE"]["ID"])
			))->fetch();
			$parameters["VALUE"]["SUM_PAID"] = $newFinanceMoneyCash["SUM_PAID"];
		}
		$PRICE = $parameters["VALUE"]["SUM_PAID"] - $arFinanceMoneyCash["SUM_PAID"];
		if($PRICE != 0)
		{
			$data = array(
				"TYPE" => "cash", //наличные
				"OUTGO" => strpos($event->getEventType(), "CashSelf") !== false ? "cashself" : "cash", //касса
				"ITEM_TYPE" => $parameters["VALUE"]["ID"],
				"ITEM_ID" => $parameters["VALUE"]["USER_ID"],
				"PRICE" => $PRICE,
			);
			$result = \Yadadya\Shopmate\Components\FinanceMoney::add($data);
		}
	}
}