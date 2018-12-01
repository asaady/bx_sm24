<?php
namespace Yadadya\Shopmate;

use \Yadadya\Shopmate\Internals;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Inventory
{
	public static function DoSaveInventory(array $arInventory = array(), $inventory_id = 0)
	{
		if($inventory_id <= 0)
		{
			$inventory = self::getActiveInventory($arInventory["STORE_ID"]);
			$inventory_id = $inventory["ID"];
		}

		self::updateInventory($inventory_id, $arInventory);

		$exist_products = \Yadadya\Shopmate\Components\Inventory::getByID($inventory_id);

		if($inventory_id > 0 && !empty($arInventory["PRODUCTS"]) && is_array($arInventory["PRODUCTS"]))
		{
			$spID = array();
			$product_ids = array();
			foreach($exist_products["PRODUCTS"] as $product)
			{
				$product_ids[] = $product["PRODUCT_ID"];
			}

			foreach($arInventory["PRODUCTS"] as $product) 
			{
				if (in_array($product["PRODUCT_ID"], $product_ids)) continue;
					
				$product_add = array(
					"INVENTORY_ID" => $inventory_id,
					"PRODUCT_ID" => $product["PRODUCT_ID"],
					"AMOUNT" => $product["AMOUNT"],
					"DIFFERENCE" => isset($product["DIFFERENCE"]) ? $product["DIFFERENCE"] : $product["AMOUNT_PREV"] - $product["AMOUNT"],
					"COMMENT" => $product["COMMENT"]
				);
				
				$res = self::addInventoryProduct($product_add);
				if(is_object($res) && $res->isSuccess())
					$spID[$product_add["PRODUCT_ID"]][] = $product_add["AMOUNT"];
			}

			$rsProps = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => array_keys($spID), "STORE_ID" => $arInventory["STORE_ID"]), false, false, array("ID", "PRODUCT_ID"));
			while($arFields = $rsProps->GetNext())
			{
				foreach($spID[$arFields["PRODUCT_ID"]] as $amount)
					\CCatalogStoreProduct::Update($arFields["ID"], array("AMOUNT" => $amount, "DATE" => date("d.m.Y H:i:s", time())));
				unset($spID[$arFields["PRODUCT_ID"]]);
			}
			foreach($spID as $prod_id => $amounts)
				foreach($amounts as $amount)
					\CCatalogStoreProduct::Add(array(
						"PRODUCT_ID" => $prod_id,
						"STORE_ID" => $arInventory["STORE_ID"],
						"AMOUNT" => $amount, 
						"DATE" => date("d.m.Y H:i:s", time())
					));
		}
		return $inventory_id;
	}

	public static function addInventoryProduct(array $product)
	{
		if($product["INVENTORY_ID"] > 0 && $product["PRODUCT_ID"] > 0)
		{
			$product["DATE"] = new \Bitrix\Main\Type\DateTime();
			return Internals\InventoryProductTable::add($product);
		}
		return false;
	}	

	public static function updateInventory($invId = 0, array $data)
	{
		if($invId > 0)
		{
			if(!empty($data))
			{
				$data = array("COMMENT" => $data["COMMENT"], "ACTIVE" => $data["ACTIVE"] == "N" ? "N" : "Y");
				Internals\InventoryTable::update($invId, $data);
			}
		}
	}

	public static function closeInventory($store_id = 0, $invId = 0)
	{
		if($invId > 0)
		{
			Internals\InventoryTable::update($invId, array("ACTIVE" => "N"));
		}
		elseif($active = self::searchActiveInventory($store_id))
		{
			Internals\InventoryTable::update($active["ID"], array("ACTIVE" => "N"));
		}
	}

	public static function getActiveInventory($store_id = 0)
	{
		if($store_id > 0)
		{
			if($active = self::searchActiveInventory($store_id))
			{
				return $active;
			}
			else
			{
				if(self::searchPrimaryInventory($store_id))
				{
					return self::addInventory($store_id);
				}
				else
				{
					return self::addInventory($store_id, true);
				}
			}
		}
		else
			throw new \Bitrix\Main\SystemException('You must specify the store.');
	}

	protected static function addInventory($store_id = 0, $primary = false)
	{
		$inventory = array(
			"DATE" => new \Bitrix\Main\Type\DateTime(),
			"STORE_ID" => $store_id,
			"PRIMARY" => $primary ? "Y" : "N",
			"ACTIVE" => "Y"
		);
		$result = Internals\InventoryTable::add($inventory);
		if($result->isSuccess())
		{
			$inventory["ID"] = $result->getId();
			return $inventory;
		}
		else
			return false;
	}

	public static function searchActiveInventory($store_id = 0)
	{
		if($store_id > 0)
		{
			$result = Internals\InventoryTable::GetList(array("filter" => array("STORE_ID" => $store_id, "ACTIVE" => "Y")));
			if($row = $result->fetch())
				return $row;
			else
				return false;
		}
		else
			throw new \Bitrix\Main\SystemException('You must specify the store.');
	}

	public static function searchPrimaryInventory($store_id = 0)
	{
		if($store_id > 0)
		{
			$result = Internals\InventoryTable::GetList(array("filter" => array("STORE_ID" => $store_id, "PRIMARY" => "Y")));
			if($row = $result->fetch())
				return $row;
			else
				return false;
		}
		else
			throw new \Bitrix\Main\SystemException('You must specify the store.');
	}
}