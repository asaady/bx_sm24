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

class FinanceReport
{
	/* ProdAmountTable */
	//OnBeforeStoreProductAdd
	function onAddAmount($arFields)
	{
		if(!empty($arFields["PRODUCT_ID"]) && !empty($arFields["STORE_ID"]) && !empty($arFields["AMOUNT"]))
			Shopmate\FinanceReport::updateProdAmount(array(
				"DATE" => new Type\DateTime($arFields["DATE"] ? $arFields["DATE"] : date("d.m.Y H:i:s", time()), "d.m.Y H:i:s"),
				"PRODUCT_ID" => $arFields["PRODUCT_ID"],
				"STORE_ID" => $arFields["STORE_ID"],
				"AMOUNT" => floatval($arFields["AMOUNT"])
			));
	}

	//OnBeforeStoreProductUpdate
	function onUpdateAmount($id, $arFields)
	{
		if(!empty($arFields["PRODUCT_ID"]) && !empty($arFields["STORE_ID"]) && !empty($arFields["AMOUNT"]))
		{
			/*if(!empty($arFields["DATE"]))
			{*/
				$rsAmount = \CCatalogStoreProduct::GetList(array(), array("ID" => $id), false, false, array("PRODUCT_ID", "STORE_ID", "AMOUNT"));
				if($arAmount = $rsAmount->Fetch())
					Shopmate\FinanceReport::updateProdAmount(array(
						"DATE" => new Type\DateTime($arFields["DATE"] ? $arFields["DATE"] : date("d.m.Y H:i:s", time()), "d.m.Y H:i:s"),
						"PRODUCT_ID" => $arFields["PRODUCT_ID"],
						"STORE_ID" => $arFields["STORE_ID"],
						"AMOUNT" => $arFields["AMOUNT"] - $arAmount["AMOUNT"]
					));
			/*}
			else
				Shopmate\FinanceReport::updateProdAmount(array(
					"PRODUCT_ID" => $arFields["PRODUCT_ID"],
					"STORE_ID" => $arFields["STORE_ID"],
					"QUANTITY" => $arFields["AMOUNT"]
				));*/
		}
	}

	//OnBeforeStoreProductDelete
	function onDeleteAmount($id)
	{
		$rsAmount = \CCatalogStoreProduct::GetList(array(), array("ID" => $id), false, false, array("PRODUCT_ID", "STORE_ID", "AMOUNT"));
		if($arAmount = $rsAmount->Fetch())
			Shopmate\FinanceReport::updateProdAmount(array(
				"DATE" => new Type\DateTime(),
				"PRODUCT_ID" => $arAmount["PRODUCT_ID"],
				"STORE_ID" => $arAmount["STORE_ID"],
				"AMOUNT" => 0 - $arAmount["AMOUNT"]
			));
	}
	/* !ProdAmountTable */

	/* ProdPriceTable */
	//OnPriceAdd, OnPriceUpdate
	function OnUpdatePrice($id, $arFields)
	{
		global $USER;
		if(empty($arFields["PRODUCT_ID"]) || empty($arFields["CATALOG_GROUP_ID"]) || !isset($arFields["PRICE"]))
		{
			$rsPrice = \CPrice::GetList(array(), array("ID" => $id), false, false, array("PRODUCT_ID", "CATALOG_GROUP_ID", "PRICE"));
			if($arPrice = $rsPrice->Fetch())
				$arFields = array_merge($arPrice, $arFields);
		}

		Shopmate\FinanceReport::updateProdPrice($arFields);
	}

	//OnProductAdd, OnProductUpdate
	function OnUpdatePurchasingPrice($id, $arFields)
	{
		global $USER;
		if(!isset($arFields["PURCHASING_PRICE"]))
		{
			$rsPrice = \CCatalogProduct::GetList(array(), array("ID" => $id), false, false, array("PURCHASING_PRICE"));
			if($arPrice = $rsPrice->Fetch())
				$arFields = array_merge($arPrice, $arFields);
		}

		Shopmate\FinanceReport::updateProdPrice(array(
			"PRODUCT_ID" => $id,
			"PRICE" => floatval($arFields["PURCHASING_PRICE"]),
			"STORE_ID" => \SMShops::getUserShop(),
			"CURRENCY" => $arFields["CURRENCY"],
		));
	}

	//OnShopmateCOverheadAdd
	function OnShopmateOverheadAdd(\Bitrix\Main\Event $event)
	{
		$errors = array();
		$parameters = $event->getParameters();
		$value = \Yadadya\Shopmate\Components\Overhead::getById($parameters["VALUE"]["ID"]);
		if (is_array($value["ELEMENT"]))
			foreach ($value["ELEMENT"] as $element)
				if($element["ELEMENT_ID"] > 0)
				{
					$res = Shopmate\FinanceReport::updateProdPrice(array(
						"DATE" => $value["DATE_DOCUMENT"],
						"PRODUCT_ID" => $element["ELEMENT_ID"],
						"STORE_ID" => $element["STORE_TO"],
						"CATALOG_GROUP_ID" => 0,
						"PRICE" => floatval($element["PURCHASING_PRICE"]),
						"DESCRIPTION" => "overhead ".$value["ID"]
					));
					if(is_object($res) && !$res->isSuccess())
						$errors = array_merge($errors, $res->getErrors());

					$res = Shopmate\FinanceReport::updateProdPrice(array(
						"DATE" => $value["DATE_DOCUMENT"],
						"PRODUCT_ID" => $element["ELEMENT_ID"],
						"STORE_ID" => $element["STORE_TO"],
						"CATALOG_GROUP_ID" => \Yadadya\Shopmate\Shops::getCatalogGroupByStore($element["STORE_TO"]),
						"PRICE" => floatval($element["SHOP_PRICE"]),
						"DESCRIPTION" => "overhead ".$value["ID"]
					));
					if(is_object($res) && !$res->isSuccess())
						$errors = array_merge($errors, $res->getErrors());
				}

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}
	/* !ProdPriceTable */

	/* ReportTasks */
	//OnCatalogStoreAdd, OnCatalogStoreUpdate, OnCatalogStoreDelete
	public static function updateCronAgent($store)
	{
		$time = new Type\DateTime();
		$time->add(new \DateInterval('PT5M'));
		\CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::cron(".$store.", \"SALE\");", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
		\CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::cron(".$store.", \"PURCHASE\");", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
		\CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::cron(".$store.", \"PROFIT\");", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
	}

	//BasketOnAfterAdd, BasketOnAfterUpdate, BasketOnAfterDelete, OnBasketAdd, OnBasketUpdate, OnBasketDelete
	function OnUpdateBasket($primary, $arFields)
	{
		$id = is_array($primary) ? $primary["ID"] : $primary;
		if($id > 0)
		{
			$result = \Bitrix\Sale\Internals\BasketTable::GetList(array(
				"select" => array("ID", "PRODUCT_ID", "DATE_PAYED" => "ORDER.DATE_PAYED", "STORE_ID" => "ORDER.STORE_ID"),
				"filter" => array("ID" => $id)
			));
			if($row = $result->fetch())
			{
				$date = $row["DATE_PAYED"];
				$result = Internals\ProdPriceTable::GetList(array(
					"select" => array("PRICE", "CURRENCY"),
					"filter" => array(
						"<=DATE" => $row["DATE_PAYED"] ? $row["DATE_PAYED"] : new Type\DateTime(),
						"STORE_ID" => $row["STORE_ID"],
						"PRODUCT_ID" => $row["PRODUCT_ID"],
						"CATALOG_GROUP_ID" => 0
					)
				));
				if($row = $result->fetch())
				{
					$data = array("PURCHASING_PRICE" => $row["PRICE"], "PURCHASING_CURRENCY" => $row["CURRENCY"]);

					$result = \Yadadya\Shopmate\Internals\BasketTable::GetList(array(
						"select" => array("ID"),
						"filter" => array("ID" => $id)
					));
					if($row = $result->fetch())
					{
						\Yadadya\Shopmate\Internals\BasketTable::update($row["ID"], $data);
					}
					else
					{
						$data["ID"] = $id;
						$res = \Yadadya\Shopmate\Internals\BasketTable::add($data);
					}
				}
				
			}
			else
				\Yadadya\Shopmate\Internals\BasketTable::delete($id);
		}
	}

	//OnStoreProductAdd, OnStoreProductUpdate
	public static function OnStoreProductAction($id, $arFields)
	{
		if(empty($arFields["DATE"]))
			$arFields["DATE"] = date("d.m.Y H:i:s", time());
		$rsProps = \CCatalogStoreProduct::GetList(array(),array("ID" => $id), false, false, array("PRODUCT_ID", "STORE_ID", "AMOUNT"));
		if($lastFields = $rsProps->GetNext())
		{
			$arFields["PRODUCT_ID"] = empty($arFields["PRODUCT_ID"]) ? $lastFields["PRODUCT_ID"] : $arFields["PRODUCT_ID"];
			$arFields["STORE_ID"] = empty($arFields["STORE_ID"]) ? $lastFields["STORE_ID"] : $arFields["STORE_ID"];
			if($arFields["PRODUCT_ID"] != $lastFields["PRODUCT_ID"] || $arFields["STORE_ID"] != $lastFields["STORE_ID"])
			{
				Shopmate\FinanceReport::addTask(array(
					"DATE" => $arFields["DATE"],
					"PRODUCT_ID" => $lastFields["PRODUCT_ID"],
					"STORE_ID" => $lastFields["STORE_ID"]
				));
			}
			Shopmate\FinanceReport::addTask(array(
				"DATE" => $arFields["DATE"],
				"PRODUCT_ID" => $arFields["PRODUCT_ID"],
				"STORE_ID" => $arFields["STORE_ID"]
			));
		}
	}
	/* !ReportTasks */
}