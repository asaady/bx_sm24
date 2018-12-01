<?php
namespace Yadadya\Shopmate\Events;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Contractor
{
	//OnShopmateCRegisterAdd
	public static function OnShopmateRegisterAdd(\Bitrix\Main\Event $event)
	{
		$errors = array();
		$parameters = $event->getParameters();
		if ($parameters["VALUE"]["ID"] > 0)
		{
			$select = array_keys(\Yadadya\Shopmate\Components\Register::getPropList());
			$arData = \Yadadya\Shopmate\Components\Register::getList([
				"select" => array("USER_TYPE", "TAX_TYPE", "WORK_PHONE", "EMAIL", "WORK_COMPANY", "NAME", "INN"),
				"filter" => array("ID" => $parameters["VALUE"]["ID"])
			])->fetch();
			if ($arData["USER_TYPE"] == "contractor" && !empty($arData["INN"]))
			{
				$arContractor = \Yadadya\Shopmate\Components\Contractor::getList([
					"filter" => array("INN" => $arData["INN"])
				])->fetch();
				$data = [
					"PERSON_TYPE" => 2,
					"NDS" => $arData["TAX_TYPE"] == "usn" ? 0 : 18,
					"PHONE" => $arData["WORK_PHONE"],
					"EMAIL" => $arData["EMAIL"],
					"COMPANY" => $arData["WORK_COMPANY"],
					"PERSON_NAME" => $arData["NAME"],
					"INN" => $arData["INN"],
					"TAX_TYPE" => $arData["TAX_TYPE"],
				];
				if (empty($arContractor))
					$res = \Yadadya\Shopmate\Components\Contractor::add($data);
				else
					$res = \Yadadya\Shopmate\Components\Contractor::update($arContractor["ID"], $data);
				if(!$res->isSuccess())
					$errors = $res->getErrors();
			}
		}
		/*if ($parameters["VALUE"]["TYPE"] == "cash" && $parameters["VALUE"]["OUTGO"] == "contractor" && $parameters["VALUE"]["ITEM_TYPE"] > 0)
		{
			$res = \Yadadya\Shopmate\Internals\StoreDocsTable::GetList(array(
				"select" => array("ID", "TOTAL_FACT"),
				"filter" => array(
					"DOC_ID" => $parameters["VALUE"]["ITEM_TYPE"],
				)
			));
			$data = array("TOTAL_FACT" => $parameters["VALUE"]["PRICE"]);
			if($row = $res->fetch())
			{
				$data["TOTAL_FACT"] += $row["TOTAL_FACT"];
				$res = \Yadadya\Shopmate\Internals\StoreDocsTable::update($row["ID"], $data);
			}
			else
			{
				$data["DOC_ID"] = $parameters["VALUE"]["ITEM_TYPE"];
				$data["STORE_ID"] = Shopmate\Shops::getUserShop();
				$res = \Yadadya\Shopmate\Internals\StoreDocsTable::add($data);
			}
			if(!$res->isSuccess())
				$errors = $res->getErrors();
		}*/

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}
}