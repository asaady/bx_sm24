<?php
namespace Yadadya\Shopmate;

use \Yadadya\Shopmate\BitrixInternals,
	\Bitrix\Main\Type;

class Cash
{
	function DoSaveOrder($arOrder, $orderId = 0)
	{
		global $DB;
		if(empty($arOrder["DATE"]))
		{
			if(!empty($arOrder["DATE_INSERT"]))
				$arOrder["DATE"] = $arOrder["DATE_INSERT"];
			elseif(!empty($arOrder["DATE_PAYED"]))
				$arOrder["DATE"] = $arOrder["DATE_PAYED"];
			elseif(!empty($arOrder["DATE_STATUS"]))
				$arOrder["DATE"] = $arOrder["DATE_STATUS"];
			else
				$arOrder["DATE"] = date("d.m.Y H:i:s", time());
		}
		$arOrder["DATE"] = date("d.m.Y H:i:s", strtotime($arOrder["DATE"]));
		if($orderId <= 0 && !empty($arOrder["ACCOUNT_NUMBER"]))
		{
        	$result = BitrixInternals\OrderTable::getList(array(
				"select" => array("ID"),
				"filter" => array(
					"ACCOUNT_NUMBER" => $arOrder["ACCOUNT_NUMBER"],
					"DATE_INSERT" => $arOrder["DATE"],
					/*array(
						"LOGIC" => "OR",
						"DATE_PAYED" => $arOrder["DATE"],
						"DATE_STATUS" => $arOrder["DATE"],
						"DATE_INSERT" => $arOrder["DATE"],
						"DATE_UPDATE" => $arOrder["DATE"],
					)*/
				),
				"limit" => 1
			));
			if($row = $result->fetch())
				$orderId = $row["ID"];
		}
		$order = array(
			"LID" => $arOrder["LID"] ? $arOrder["LID"] : SITE_ID,
			"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"] ? $arOrder["PERSON_TYPE_ID"] : 1,
			"PAYED" => $arOrder["PAYED"] ? $arOrder["PAYED"] : "N",
			"DATE_PAYED" => new Type\DateTime($arOrder["DATE"], "d.m.Y H:i:s"),
			"CANCELED" => $arOrder["CANCELED"] ? $arOrder["CANCELED"] : "N",
			"DATE_CANCELED" => new Type\DateTime($arOrder["DATE_CANCELED"] ? $arOrder["DATE_CANCELED"] : date("d.m.Y H:i:s", time()), "d.m.Y H:i:s"),
			"DATE_STATUS" => new Type\DateTime($arOrder["DATE_STATUS"] ? $arOrder["DATE_STATUS"] : date("d.m.Y H:i:s", time()), "d.m.Y H:i:s"),
			"DEDUCTED" => $arOrder["DEDUCTED"] ? $arOrder["DEDUCTED"] : "Y",
			"PRICE" => $arOrder["PRICE"] ? $arOrder["PRICE"] : 0,
			"SUM_PAID" => $arOrder["PAYED"] == "Y" ? $arOrder["PRICE"] : floatval($arOrder["SUM_PAID"]),
			"CURRENCY" => $arOrder["CURRENCY"] ? $arOrder["CURRENCY"] : "RUB",
			"USER_ID" => $arOrder["USER_ID"] ? $arOrder["USER_ID"] : 1,
			"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"] ? $arOrder["PAY_SYSTEM_ID"] : 0,
			"DATE_INSERT" => new Type\DateTime($arOrder["DATE"], "d.m.Y H:i:s"),
			"DATE_UPDATE" => new Type\DateTime($arOrder["DATE"], "d.m.Y H:i:s"),
			"STORE_ID" => $arOrder["STORE_ID"] ? $arOrder["STORE_ID"] : 0,
			"ACCOUNT_NUMBER" => $arOrder["ACCOUNT_NUMBER"] ? $arOrder["ACCOUNT_NUMBER"] : $orderId,
		);

		if (empty($order["ACCOUNT_NUMBER"]))
			unset($order["ACCOUNT_NUMBER"]);

		if($order["PRICE"] == 0 && is_array($arOrder["BASKET_ITEMS"]))
			foreach ($arOrder["BASKET_ITEMS"] as $bitem) 
				if ($bitem["PRODUCT_ID"] > 0)
					$order["PRICE"] += $bitem["PRICE"];

		if($order["PRICE"] > 0 && is_array($arOrder["BASKET_ITEMS"]))
		{
			$lastDeducted = "Y";
			if($orderId > 0)
			{
				$result = BitrixInternals\OrderTable::getList(array(
					"select" => array("DEDUCTED"),
					"filter" => array("ID" => $orderId),
					"limit" => 1
				));
				if($row = $result->fetch())
					$lastDeducted = $row["DEDUCTED"] == "Y" ? "Y" : "N";

				BitrixInternals\OrderTable::update($orderId, $order);
			}
			else
				$orderId = BitrixInternals\OrderTable::add($order)->getId();

			//order items
			$lastBasket = array();
			$oBasket = new BitrixInternals\BasketTable();
			$smBasket = new Internals\BasketTable();
			$rsBasket = $oBasket->getList(array(
				"select" => array("*", "SM_ID" => "SMBASKET.ID"),
				"filter" => array("ORDER_ID" => $orderId),
				"runtime" => array(new \Bitrix\Main\Entity\ReferenceField(
					'SMBASKET',
					'Yadadya\Shopmate\Internals\Basket',
					array('=ref.ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				))
			));
			while($arBasket = $rsBasket->fetch())
				$lastBasket[] = $arBasket;

			//back basket quantity on store
			if(!empty($lastBasket) && $lastDeducted == "Y")
			{
				$returnQuantity = $lastBasket;
				$spID = array();
				$backAmount = array();
				foreach ($returnQuantity as $arBasket) 
				{
					$spID[] = $arBasket["PRODUCT_ID"];
					$backAmount[$arBasket["PRODUCT_ID"]] += $arBasket["QUANTITY"];
				}
				$rsProps = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $spID, "STORE_ID" => $arOrder["STORE_ID"]), false, false, array("ID", "AMOUNT", "PRODUCT_ID"));
				while($arFields = $rsProps->GetNext())
				{
					foreach($returnQuantity as $keyItem => $arItem)
						if($arFields["PRODUCT_ID"] == $arItem["PRODUCT_ID"])
						{
							\CCatalogStoreProduct::Update($arFields["ID"], array("AMOUNT" => $arFields["AMOUNT"] + $arItem["QUANTITY"], "DATE" => $arOrder["DATE"]));
							unset($returnQuantity[$keyItem]);			
							break;
						}
				}
				foreach($returnQuantity as $arItem)
					\CCatalogStoreProduct::Add(array(
						"PRODUCT_ID" => $arItem["PRODUCT_ID"],
						"STORE_ID" => $arOrder["STORE_ID"],
						"AMOUNT" => $arItem["QUANTITY"], 
						"DATE" => $arOrder["DATE"]
					));
			}
			//!back basket quantity on store

			$postBasket = array();
			foreach($arOrder["BASKET_ITEMS"] as $arItem)
			{
				$postItem = array();
				foreach ($lastBasket as $keyLast => $lastItem) 
					if ($lastItem["ID"] == $arItem["ID"])
					{
						$postItem = $lastItem;
						unset($lastBasket[$keyLast], $postItem["SM_ID"]);
						break;
					}

				$postItem["ID"] = $arItem["ID"];
				$postItem["ORDER_ID"] = $orderId;
				$postItem["PRODUCT_ID"] = $arItem["PRODUCT_ID"];
				$postItem["QUANTITY"] = $arItem["QUANTITY"];
				$postItem["PRICE"] = $arItem["PRICE"];
				$postItem["DISCOUNT_PRICE"] = !empty($arItem["DISCOUNT_PRICE"]) ? $arItem["DISCOUNT_PRICE"] : $arItem["PRICE"];
				$postItem["DATE_INSERT"] = $order["DATE_INSERT"];
				$postItem["DATE_UPDATE"] = $order["DATE_UPDATE"];
				$postItem["LID"] = $order["LID"];
				$postItem["CURRENCY"] = $order["CURRENCY"];
				$postItem["FUSER_ID"] = $order["USER_ID"];

				if ($postItem["PRODUCT_ID"] > 0)
					$postBasket[] = $postItem;
			}
			$arOrder["BASKET_ITEMS"] = $postBasket;

			$prodsPurchase = array();
			$prodsId = array();
			foreach ($postBasket as $postItem) 
				$prodsId[] = $postItem["PRODUCT_ID"];
			if (!empty($prodsId))
			{
				$res = Internals\StoreProductTable::getList(array("filter" => array("PRODUCT_ID" => $prodsId, "STORE_ID" => $arOrder["STORE_ID"])));
				while ($row = $res->fetch())
					$prodsPurchase[$row["PRODUCT_ID"]] = $row;
			}
			
			foreach ($postBasket as $postItem) 
			{
				if(intval($postItem["ID"]) > 0)
					$oBasket->update($postItem["ID"], $postItem);
				else
				{
					$postItem["ID"] = $oBasket->add($postItem)->getId();
					$smBasket->add(array(
						"ID" => $postItem["ID"],
						"PURCHASING_PRICE" => $prodsPurchase[$postItem["PRODUCT_ID"]]["PURCHASING_PRICE"],
						"PURCHASING_CURRENCY" => $prodsPurchase[$postItem["PRODUCT_ID"]]["PURCHASING_CURRENCY"],
						"QUANTITY_FIRST" => $postItem["QUANTITY"],
					));
				}
			}

			if(!empty($lastBasket))
				foreach($lastBasket as $arItem) 
				{
					$oBasket->delete($arItem["ID"]);
					if (!empty($arItem["SM_ID"]))
						$smBasket->delete($arItem["SM_ID"]);
				}
				
			if ($order["DEDUCTED"] == "Y")
			{
				$spID = array();
				foreach($arOrder["BASKET_ITEMS"] as $arItem)
					$spID[] = $arItem["PRODUCT_ID"];
				$rsProps = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $spID, "STORE_ID" => $arOrder["STORE_ID"]), false, false, array("ID", "AMOUNT", "PRODUCT_ID"));
				while($arFields = $rsProps->GetNext())
				{
					foreach($arOrder["BASKET_ITEMS"] as $keyItem => $arItem)
						if($arFields["PRODUCT_ID"] == $arItem["PRODUCT_ID"])
						{
							\CCatalogStoreProduct::Update($arFields["ID"], array("AMOUNT" => $arFields["AMOUNT"] - $arItem["QUANTITY"], "DATE" => $arOrder["DATE"]));
							unset($arOrder["BASKET_ITEMS"][$keyItem]);			
							break;
						}
				}
				foreach($arOrder["BASKET_ITEMS"] as $arItem)
					\CCatalogStoreProduct::Add(array(
						"PRODUCT_ID" => $arItem["PRODUCT_ID"],
						"STORE_ID" => $arOrder["STORE_ID"],
						"AMOUNT" => -1 * $arItem["QUANTITY"],
						"DATE" => $arOrder["DATE"]
					));
			}
			//!order items
			return $orderId;
		}
	}
}