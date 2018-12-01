<?php
namespace Yadadya\Shopmate;

use Yadadya\Shopmate\Internals;

use Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class Products
{
	public function getList(array $parameters = array())
	{
		$store_id = empty($parameters["filter"]["STORE_ID"]) ? Shopmate\Shops::getUserShop() : $parameters["filter"]["STORE_ID"];
		unset($parameters["filter"]["STORE_ID"]);

		$parameters["runtime"][] = new Entity\ReferenceField(
			'SMPRODUCT',
			'Yadadya\Shopmate\Internals\Product',
			array('=ref.PRODUCT_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'CPRODUCT',
			'Bitrix\Catalog\Product',
			array('=ref.ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'CSTORE_PRODUCT',
			'Yadadya\Shopmate\BitrixInternals\StoreProduct',
			array('=ref.PRODUCT_ID' => 'this.ID', 'ref.STORE_ID' => new DB\SqlExpression('?i', $store_id)),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'SMSTORE_PRODUCT',
			'Yadadya\Shopmate\Internals\StoreProduct',
			array('=ref.PRODUCT_ID' => 'this.ID', 'ref.STORE_ID' => new DB\SqlExpression('?i', $store_id)),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'CGROUP',
			'Bitrix\Catalog\Group',
			array('=ref.XML_ID' => new DB\SqlExpression('CONCAT("SHOP_ID_", ?i)', $store_id)),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'CPRICE',
			'Bitrix\Catalog\Price',
			array(
				'=ref.PRODUCT_ID' => 'this.ID', 
				'=ref.CATALOG_GROUP_ID' => new DB\SqlExpression('`iblock_element_cgroup`.`ID`'),
				'!=ref.QUANTITY_FROM' => new DB\SqlExpression('1'),
				'!=ref.QUANTITY_TO' => new DB\SqlExpression('1'),
			),
			array('join_type' => 'LEFT')
		);

		$parameters["filter"]["IBLOCK_ID"] = \Yadadya\Shopmate\Options::getCatalogID();

		if (empty($parameters["filter"]["ID"]))
		{
			$result = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::getList(array(
			    'select' => array('SHOP_ID', 'PRODUCT_ID'),
			    'filter' => array('SHOP_ID' => $store_id)
			));

			while ($row = $result->fetch())
			    $ids[] = $row['PRODUCT_ID'];

			$undefinedProducts = \Yadadya\Shopmate\Products::getUndefinedProducts();
			foreach ($undefinedProducts as $undefinedProduct)
				$ids[] = $undefinedProduct;
			
			$parameters["filter"][]["ID"] = $ids;
		}
		
		/*$referenceFields = array("AMOUNT" => "CSTORE_PRODUCT.AMOUNT", "MEASURE" => "CPRODUCT.MEASURE", "PURCHASING_PRICE" => "SMSTORE_PRODUCT.PURCHASING_PRICE", "PURCHASING_CURRENCY" => "SMSTORE_PRODUCT.PURCHASING_CURRENCY", "CATALOG_GROUP_ID" => "CGROUP.ID", "PRICE" => "CPRICE.PRICE", "CURRENCY" => "CPRICE.CURRENCY", "END_DATE" => "SMSTORE_PRODUCT.END_DATE");

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);*/

		return Iblock\ElementTable::getList($parameters);
	}

	public static function updateStoreProduct(array $data = array())
	{
		if($data["PRODUCT_ID"] > 0 && $data["STORE_ID"] > 0)
		{
			unset($data["ID"]);
			$result = Internals\StoreProductTable::GetList(array(
				"filter" => array("PRODUCT_ID" => $data["PRODUCT_ID"], "STORE_ID" => $data["STORE_ID"])
			));
			if($row = $result->fetch())
			{
				$save = false;
				foreach($row as $field => $value)
					if(!empty($data[$field]))
					{
						$row[$field] = $data[$field];
						$save = true;
					}
				if($save)
				{
					$id = $row["ID"];
					unset($row["ID"]);
					Internals\StoreProductTable::update($id, $row);
				}
			}
			else
			{
				$row = array();
				$fields = array_keys(Internals\StoreProductTable::getEntity()->getFields());
				foreach($fields as $field)
					if(!empty($data[$field]))
						$row[$field] = $data[$field];
				Internals\StoreProductTable::add($row);
			}
		}
	}

	public static function updateProductsAmount(array $products = array(), $store = 0)
	{
		if(empty($products)) return;
		if(empty($store)) $store = Shops::getUserShop();
		if(empty($store)) return;

		$today = date("d.m.Y H:i:s");
		$updateQuantity = array();
		foreach ($products as $product) 
			if($product["PRODUCT_ID"] > 0 && $product["QUANTITY"] != 0)
				$updateQuantity[] = array(
					"STORE_ID" => $store,
					"PRODUCT_ID" => $product["PRODUCT_ID"],
					"QUANTITY" => $product["QUANTITY"],
					"DATE" => !empty($product["DATE"]) ? date("d.m.Y H:i:s", strtotime($product["DATE"])) : $today
				);

		if(empty($updateQuantity)) return;

		$prodID = array();
		foreach ($updateQuantity as $product) 
			$prodID[] = $product["PRODUCT_ID"];

		$res = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $prodID, "STORE_ID" => $store), false, false, array("ID", "AMOUNT", "PRODUCT_ID"));
		while($arFields = $res->GetNext())
		{
			foreach($updateQuantity as $keyItem => $product)
				if($arFields["PRODUCT_ID"] == $product["PRODUCT_ID"])
				{
					\CCatalogStoreProduct::Update($arFields["ID"], array("AMOUNT" => $arFields["AMOUNT"] + $product["QUANTITY"], "DATE" => $product["DATE"]));
					unset($updateQuantity[$keyItem]);			
					break;
				}
		}
		foreach($updateQuantity as $product)
		{
			if(empty($product["AMOUNT"]) && !empty($product["QUANTITY"])) 
				$product["AMOUNT"] = $product["QUANTITY"];
			\CCatalogStoreProduct::Add($product);
		}
	}

	public static function getWBarcodePref()
	{
		$result = array(/*"90", */"23", "26");
		return $result;
	}

	public static function isWBarcode($bc = "")
	{
		if(in_array(substr($bc, 0, 2), self::getWBarcodePref()) && strlen($bc) == 13)
			return true;
		return false;
	}

	public static function getProductsByBarcode($barcode, $bc_assoc = false)
	{
		$products = array();
		if(!empty($barcode))
		{
			$barcode = (array) $barcode;
			$arBarcodeFilter = array();
			foreach($barcode as $kb => $bc)
			{
				$arBarcodeFilter[] = $bc;
				if(self::isWBarcode($bc))
					foreach (self::getWBarcodePref() as $fb)
						$arBarcodeFilter[] = $fb.substr($bc, 2, -6)."00000_";
			}
			$res = \Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::getList(array(
				"select" => array("PRODUCT_ID", "BARCODE"),
				"filter" => array("BARCODE" => $arBarcodeFilter)
			));
			while($row = $res->fetch())
			{
				$bcc = $row["BARCODE"];
				if(!in_array($bcc, $barcode))
					foreach($barcode as $kb => $bc)
						if(self::isWBarcode($bc))
							foreach ($wpb as $fb)
								if(stripos($bc, $fb.substr($bc, 2, -6)."00000") === 0)
									$bcc = $bc;
				$products[$bcc] = $row["PRODUCT_ID"];
			}
		}
		return $bc_assoc ? $products : array_values($products);
	}

	public static function getUndefinedBarcodes()
	{
		return array("123456", "4007704007706",  "7077070770778", "5007705007704", "6007706007702");
	}

	public static function getUndefinedProducts()
	{
		return self::getProductsByBarcode(self::getUndefinedBarcodes());
	}
}
