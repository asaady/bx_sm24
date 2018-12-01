<?php
namespace Yadadya\Shopmate\Events;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Overhead
{
	//OnShopmateCFinanceMoneyAdd
	public static function OnShopmateFinanceMoneyAdd(\Bitrix\Main\Event $event)
	{
		$errors = array();
		$parameters = $event->getParameters();
		if($parameters["VALUE"]["TYPE"] == "cash" && $parameters["VALUE"]["OUTGO"] == "contractor" && $parameters["VALUE"]["ITEM_TYPE"] > 0)
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
		}

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}

	//OnShopmateCUserOverheadAdd, OnShopmateCUserOverheadUpdate
	/*public function OnShopmateUserOverheadAction(\Bitrix\Main\Event $event)
	{
		$errors = array();
		$parameters = $event->getParameters();

		if ($parameters["VALUE"]["ID"] > 0)
		{
			$arData = \Yadadya\Shopmate\Components\UserOverhead::getList([
				"select" => ["OVERHEAD_ID", "XML"],
				"filter" => ["ID" => $parameters["VALUE"]["ID"]]
			])->fetch();
			
			if (empty($arData["OVERHEAD_ID"]) && !empty($arData["XML"]))
			{
				$xmlData = \Yadadya\Shopmate\UserOverhead::loadByID($arData["XML"]);
				$res = \Yadadya\Shopmate\Components\Overhead::getList([
					"select" => ["ID", "NUMBER_DOCUMENT", "DATE_DOCUMENT"],
					"filter" => [
						"NUMBER_DOCUMENT" => $xmlData["OVERHEAD"]["DOCUMENT_NUMBER"],
						"DATE_DOCUMENT" => $xmlData["OVERHEAD"]["DOCUMENT_DATE"],
				]
				]);
				if ($row = $res->fetch())
					$arData["OVERHEAD_ID"] = $row["ID"];

				if (empty($arData["OVERHEAD_ID"]))
				{
					$res = \Yadadya\Shopmate\Components\Overhead::add([
						"NUMBER_DOCUMENT" => $xmlData["OVERHEAD"]["DOCUMENT_NUMBER"],
						"DATE_DOCUMENT" => $xmlData["OVERHEAD"]["DOCUMENT_DATE"],
					]);
					if ($res->isSuccess())
						$arData["OVERHEAD_ID"] = $res->getId();
					else
						$errors = $res->getErrors();
				}

				if (!empty($arData["OVERHEAD_ID"]))
				{
					$res = \Yadadya\Shopmate\Internals\UserOverheadTable::update($parameters["VALUE"]["ID"], ["OVERHEAD_ID" => $arData["OVERHEAD_ID"]]);
					if(!$res->isSuccess())
						$errors = $res->getErrors();
				}

			}
		}

		// if(!empty($errors))
		// 	return new \Bitrix\Main\EventResult(
		// 		\Bitrix\Main\EventResult::ERROR,
		// 		$errors,
		// 		'yadadya.shopmate'
		// 	);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}*/
}