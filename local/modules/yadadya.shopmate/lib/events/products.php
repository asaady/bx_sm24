<?php
namespace Yadadya\Shopmate\Events;

use Bitrix\Main\DB;
use Bitrix\Main\Entity;
use Yadadya\Shopmate\BitrixInternals;
use Yadadya\Shopmate;
use Bitrix\Catalog;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Products
{
	// OnShopmateCProductsAdd, OnShopmateCProductsUpdate
	public static function OnProductAction(\Bitrix\Main\Event $event)
	{
		$parameters = $event->getParameters();
		$shop_id = Shopmate\Shops::getUserShop();
		$element_id = $parameters["VALUE"]["ID"];
		$element_isset = array();
		$errors = array();

		if (!empty($element_id))
		{
			$res = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::getList(array("select" => array("PRODUCT_ID"), "filter" => array("SHOP_ID" => $shop_id, "PRODUCT_ID" => $element_id)));
			if (!$res->fetch())
			{
				$res = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::add(array(
					"SHOP_ID" => $shop_id,
					'PRODUCT_ID' => $element_id
				));
				if(!$res->isSuccess())
					$errors = $res->getErrors();
			}
		}

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}

	// OnShopmateCInventoryAdd, OnShopmateCInventoryUpdate
	public static function OnInventoryAction(\Bitrix\Main\Event $event)
	{
		$parameters = $event->getParameters();
		$shop_id = Shopmate\Shops::getUserShop();
		$element_id = array();
		$element_isset = array();
		$errors = array();

		foreach ($parameters["VALUE"]["PRODUCTS"] as $element) 
			if($element["PRODUCT_ID"] > 0)
				$element_id[] = $element["PRODUCT_ID"];

		if (!empty($element_id))
		{
			$res = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::getList(array("select" => array("PRODUCT_ID"), "filter" => array("SHOP_ID" => $shop_id, "PRODUCT_ID" => $element_id)));
			while ($row = $res->fetch())
				$element_isset[] = $row["PRODUCT_ID"];
			foreach ($element_id as $el_id) 
				if (!in_array($el_id, $element_isset))
				{
					$res = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::add(array(
					    "SHOP_ID" => $shop_id,
						'PRODUCT_ID' => $el_id
					));
					if(!$res->isSuccess())
						$errors = $res->getErrors();
				}
		}

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}

	//OnShopmateCOverheadAdd, OnShopmateCOverheadUpdate
	public static function OnOverheadAction(\Bitrix\Main\Event $event)
	{
		$parameters = $event->getParameters();
		$shop_id = Shopmate\Shops::getUserShop();
		$element_id = array();
		$element_isset = array();
		$errors = array();

		foreach ($parameters["VALUE"]["ELEMENT"] as $element) 
			if($element["ELEMENT_ID"] > 0)
				$element_id[] = $element["ELEMENT_ID"];

		if (!empty($element_id))
		{
			$res = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::getList(array("select" => array("PRODUCT_ID"), "filter" => array("SHOP_ID" => $shop_id, "PRODUCT_ID" => $element_id)));
			while ($row = $res->fetch())
				$element_isset[] = $row["PRODUCT_ID"];
			foreach ($element_id as $el_id) 
				if (!in_array($el_id, $element_isset))
				{
					$res = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::add(array(
					    "SHOP_ID" => $shop_id,
						'PRODUCT_ID' => $el_id
					));
					if(!$res->isSuccess())
						$errors = $res->getErrors();
				}
		}

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}
	
	//OnProductAdd, OnProductUpdate
	public static function OnUpdateProduct($id, $arFields)
	{
		if($arFields["STORE_ID"] > 0)
		{
			$result = Catalog\ProductTable::GetList(array(
				"select" => array("PURCHASING_PRICE", "PURCHASING_CURRENCY"),
				"filter" => array("=ID" => $id)
			));
			if($row = $result->fetch())
			{
				Shopmate\Products::updateStoreProduct(array(
					"PRODUCT_ID" => $id,
					"STORE_ID" => $arFields["STORE_ID"],
					"PURCHASING_PRICE" => $row["PURCHASING_PRICE"],
					"PURCHASING_CURRENCY" => $row["PURCHASING_CURRENCY"],
				));
			}
		}
	}

	//OnProductAdd, OnProductUpdate
	public static function OnUpdateStoreDocsElement($id, $arFields)
	{
		$result = BitrixInternals\StoreDocsElementTable::GetList(array(
			"select" => array("ID", "ELEMENT_ID", "STORE_TO", "PURCHASING_PRICE", "PURCHASING_CURRENCY" => "DOC.CURRENCY", "END_DATE" => "SMDOCS_ELEMENT.END_DATE", "DOC_ID"),
			"filter" => array("@ELEMENT_ID" => new \Bitrix\Main\DB\SqlExpression("(SELECT `ELEMENT_ID` FROM `b_catalog_docs_element` WHERE `ID`=".intval($id).")")),
			"runtime" => array(
				new Entity\ReferenceField(
					'SMDOCS_ELEMENT',
					'Yadadya\Shopmate\Internals\StoreDocsElement',
					array('=ref.DOCS_ELEMENT_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				),
				new Entity\ReferenceField(
					'DOC',
					'Yadadya\Shopmate\BitrixInternals\StoreDocs',
					array(
						'this.ID' => new \Bitrix\Main\DB\SqlExpression("`yadadya_shopmate_bitrixinternals_store_docs_element`.`DOC_ID`"),
						'ref.DOC_TYPE' => new DB\SqlExpression('?s', 'A'),
						'ref.STATUS' => new DB\SqlExpression('?s', 'Y'),
						),
					array('join_type' => 'LEFT')
				)
			),
			"order" => array("DOC.DATE_DOCUMENT" => "DESC"),
			"limit" => 1
		));
		if($row = $result->fetch())
		{
			if($row["ID"] == $id)
			{
				Shopmate\Products::updateStoreProduct(array(
					"PRODUCT_ID" => $row["ELEMENT_ID"],
					"STORE_ID" => $row["STORE_TO"],
					"PURCHASING_PRICE" => $row["PURCHASING_PRICE"],
					"PURCHASING_CURRENCY" => $row["PURCHASING_CURRENCY"],
					"END_DATE" => $row["END_DATE"],
				));
			}
		}
	}

	//OnShopmateCOverheadAdd
	public static function OnShopmateOverheadAdd(\Bitrix\Main\Event $event)
	{
		$prod = new \Yadadya\Shopmate\Components\Products;
		$errors = array();
		$parameters = $event->getParameters();
		//$value = $parameters["VALUE"];
		$value = \Yadadya\Shopmate\Components\Overhead::getById($parameters["VALUE"]["ID"]);

		/*$shopEmails = array();
		$STORE_ID = \Yadadya\Shopmate\Shops::getUserStore();
		if ($STORE_ID)
		{
			$res = \Yadadya\Shopmate\BitrixInternals\StoreTable::getList(array("select" => array("EMAIL"),"filter" => array("ID" => $STORE_ID)));
			while ($row = $res->fetch())
				if (!empty($row["EMAIL"]))
					$shopEmails[] = $row["EMAIL"];
		}*/

		if (is_array($value["ELEMENT"]))
			foreach ($value["ELEMENT"] as $element)
				if ($element["ELEMENT_ID"] > 0)
				{
					$prodBefore = $prod->getByID($element["ELEMENT_ID"]);
					$updateData = array();
					if ($element["PURCHASING_PRICE"] > 0)
						$updateData["PURCHASING_PRICE"] = $element["PURCHASING_PRICE"];
					if ($element["SHOP_PRICE"] > 0)
						$updateData["PRICE"] = $element["SHOP_PRICE"];

					if (!empty($updateData))
					{
						$res = $prod->update($element["ELEMENT_ID"], $updateData);
						if(!$res->isSuccess())
							$errors = array_merge($errors, $res->getErrors());
					}

					/*if (!empty($shopEmails) && $prodBefore["SHELF_LIFE"] > 0)
					{
						$start_date = !empty($element["START_DATE"]) ? $element["START_DATE"] : new \Bitrix\Main\Type\DateTime();

						$start_date->setTime($prodBefore["SHELF_LIFE"]*24,0);
						$end_date = $start_date->toString();

						$start_date->setTime(-1*$prodBefore["SHELF_LIFE"]*0.3*24,0);
						$pre_end_date = $start_date->toString();

						$dataSend = array(
							"EVENT_NAME" => "PRODUCT_END_DATE",
							"LID" => SITE_ID,
								"C_FIELDS" => array(
									"STORE_EMAIL" => implode(",", $shopEmails),
									"PRODUCT_NAME" => $prodBefore["NAME"],
									"PRODUCT_ID" => $prodBefore["ID"],
									"PRODUCT_END_DATE" => $end_date,
								),
							"DATE_SEND" => $pre_end_date,
						);
						sendWithTime($dataSend);
						$dataSend["DATE_SEND"] = $end_date;
						sendWithTime($dataSend);
					}*/
				}
		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}

	//OnShopmateCFabricaPartAdd
	public static function OnFabricaPartAdd(\Bitrix\Main\Event $event)
	{

		$parameters = $event->getParameters();
		$value = \Yadadya\Shopmate\Components\FabricaPart::getById($parameters["VALUE"]["ID"]);
		if ($value["PRODUCT"][0]["TYPE"] == 1)
		{
			foreach ($value["PRODUCT"] as $product) 
				if ($product["TYPE"] == 1 && $product["PRODUCT_ID"] > 0)
				{
					\Yadadya\Shopmate\Products::updateProductsAmount(array(
						array(
							"PRODUCT_ID" => $product["PRODUCT_ID"],
							"QUANTITY" => $product["AMOUNT"],
							"DATE" => $value["DATE"]
						)
					));
				}
			foreach ($value["CONNECT"] as $product) 
				if ($product["PRODUCT_ID"] > 0)
				{
					\Yadadya\Shopmate\Products::updateProductsAmount(array(
						array(
							"PRODUCT_ID" => $product["PRODUCT_ID"],
							"QUANTITY" => -1 * $product["AMOUNT"],
							"DATE" => $value["DATE"]
						)
					));
				}
		}
		elseif ($value["PRODUCT"][0]["TYPE"] == 2)
		{
			foreach ($value["PRODUCT"] as $product) 
				if ($product["TYPE"] == 2 && $product["PRODUCT_ID"] > 0)
				{
					\Yadadya\Shopmate\Products::updateProductsAmount(array(
						array(
							"PRODUCT_ID" => $product["PRODUCT_ID"],
							"QUANTITY" => -1 * $product["AMOUNT"],
							"DATE" => $value["DATE"]
						)
					));
				}
			foreach ($value["CONNECT"] as $product) 
				if ($product["PRODUCT_ID"] > 0)
				{
					\Yadadya\Shopmate\Products::updateProductsAmount(array(
						array(
							"PRODUCT_ID" => $product["PRODUCT_ID"],
							"QUANTITY" => $product["AMOUNT"],
							"DATE" => $value["DATE"]
						)
					));
				}
		}

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}

	//OnShopmateCFabricaAdd, OnShopmateCFabricaUpdate
	public static function OnFabricaAction(\Bitrix\Main\Event $event)
	{
		$errors = array();
		$parameters = $event->getParameters();
		$value = \Yadadya\Shopmate\Components\Fabrica::getById($parameters["VALUE"]["ID"]);
		$prod = new \Yadadya\Shopmate\Components\Products;
		$fprod = new \Yadadya\Shopmate\Internals\FabricaProductTable;

		$oMeasure = \CCatalogMeasure::GetList(array(), array("IS_DEFAULT" => "Y"));
		if($arMeasure = $oMeasure->Fetch())
			$curMeasure = $arMeasure["ID"];

		foreach ($value["ITEMS"] as $item) 
			if ($item["PRODUCT_ID"] <= 0)
			{
				$res = $prod->add(array("NAME" => $item["NAME"], "MEASURE" => $item["MEASURE"] > 0 ? $item["MEASURE"] : $curMeasure));
				if($res->isSuccess())
					$fprod->update($item["ID"], array("PRODUCT_ID" => $res->getId()));
				else
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
}