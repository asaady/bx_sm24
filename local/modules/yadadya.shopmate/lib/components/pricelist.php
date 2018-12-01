<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class Pricelist extends Base
{
	protected static $store_prefix = "SHOP_ID_";
	protected static $currentFields = array("ID", "NAME_FORMATED");
	protected static $currentSort = array("ID" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => Array(
			"REQUIRED" => "Y",
		),
		"DISCOUNT" => Array(
			"PROPERTY_TYPE" => "N",
			"DEFAULT_VALUE" => "0",
		),
		"PRODUCTS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"READONLY" => "Y",
			"PROPERTY_LIST" => array(
				"NAME" => array(
					"DISABLED" => "Y",
				),
				"BASE_PRICE" => array(
					"DISABLED" => "Y",
				),
				"PRICE" => array(
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
				"PRODUCT_ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		/*"COMMENT" => array(
			"PROPERTY_TYPE" => "T"
		)*/
	);

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("products");
	}*/

	public function getList(array $parameters = array())
	{
		$parameters["filter"][] = array(
			"LOGIC" => "OR", 
			"XML_ID" => self::$store_prefix.Shopmate\Shops::getUserStore()."%",
			"NAME" => self::$store_prefix.Shopmate\Shops::getUserStore()."%",
		);

		$referenceFields = array(
			"NAME_FORMATED" =>  new Entity\ExpressionField("NAME_FORMATED", 
				"(CASE 
					WHEN (%1\$s = \"\") OR (%1\$s IS NULL)
						THEN %2\$s
					ELSE %1\$s 
				END)",
				array("CURRENT_LANG.NAME", "NAME")
			),
		);
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);
		$parameters["count_total"] = false;

		return Shopmate\BitrixInternals\CatGroupTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEMS"]))
			foreach ($arResult["ITEMS"] as $key => $val) 
				if ($val["ID"] == \Yadadya\Shopmate\Shops::getUserPrice())
					$arResult["ITEMS"][$key]["CAN_DELETE"] = "N";

		if (isset($arResult["ITEM"]) && empty($arResult["ITEM"]["PRODUCTS"]))
		{
			$basePriselist = self::getBasePricelist();
			foreach ($basePriselist["PRODUCTS"] as $key => $val)
				unset($basePriselist["PRODUCTS"][$key]["ID"]);
			$arResult["ITEM"]["PRODUCTS"] = $basePriselist["PRODUCTS"];
		}
		return $arResult;
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		$arFilter = $filter;

		return $arFilter;
	}

	public function getByID($primary = 0)
	{
		$result = self::getList(array("filter" => array("ID" => $primary)))->fetch();

		$res = Shopmate\BitrixInternals\ProductTable::getList(array(
			"select" => array("ID", "PRICE_ID" => "CPRICE.ID", "NAME" => "IBLOCK_ELEMENT.NAME", "BASE_PRICE" => "BPRICE.PRICE", "PRICE" => "CPRICE.PRICE"),
			"runtime" => array(
				"PROD_STORY" => new Entity\ReferenceField(
					'PROD_STORY',
					'Yadadya\Shopmate\Internals\ShopProductHistory',
					array('=ref.PRODUCT_ID' => 'this.ID', 'ref.SHOP_ID' => new DB\SqlExpression(Shopmate\Shops::getUserShop())),
					array('join_type' => 'INNER')
				),
				"BPRICE" => new Entity\ReferenceField(
					'BPRICE',
					'Yadadya\Shopmate\BitrixInternals\Price',
					array('=ref.PRODUCT_ID' => 'this.ID', 'ref.CATALOG_GROUP_ID' => array(Shopmate\Shops::getUserPrice())),
					array('join_type' => 'LEFT')
				),
				"CPRICE" => new Entity\ReferenceField(
					'CPRICE',
					'Yadadya\Shopmate\BitrixInternals\Price',
					array('=ref.PRODUCT_ID' => 'this.ID', 'ref.CATALOG_GROUP_ID' => array($primary)),
					array('join_type' => 'LEFT')
				),
			)
		));
		$result["NAME"] = $result["NAME_FORMATED"];
		while ($row = $res->fetch())
		{
			$row["PRODUCT_ID"] = $row["ID"];
			$row["ID"] = $row["PRICE_ID"];
			if (empty($row["PRICE"]))
				$row["PRICE"] = $row["BASE_PRICE"];
			$result["PRODUCTS"][] = $row;
		}
		
		return $result;
	}

	public function getBasePricelist()
	{
		return self::getByID(Shopmate\Shops::getUserPrice());
	}


	public function add(array $data)
	{
		$result = parent::add($data);

		if($result->isSuccess())
		{
			self::update(0, $data, $result);
		}

		return $result;
	}

	public function update($primary, array $data, $result = null)
	{
		if(!($result instanceof \Bitrix\Main\Entity\AddResult)) 
		{
			if(is_object($this)) 
			{
				$this->result->setData(array("ID" => $primary));
				self::prepareResult();
			}
			$result = parent::update($primary, $data);
		}

		if($result->isSuccess())
		{
			$dataGroup = array(
				"NAME" => $data["NAME"],
				"XML_ID" => self::$store_prefix.Shopmate\Shops::getUserStore()."_".($primary > 0 ? $primary : "TMP"),
			);
			if($primary > 0)
			{
				$res = Shopmate\BitrixInternals\CatGroupTable::update($primary, $dataGroup);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
			}
			else
			{
				$res = Shopmate\BitrixInternals\CatGroupTable::add($dataGroup);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
				else
					$primary = $res->GetID();

				$res = Shopmate\BitrixInternals\CatGroupTable::update($primary, array("XML_ID" => self::$store_prefix.Shopmate\Shops::getUserStore()."_".$primary));
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
			}

			if ($primary > 0)
			{
				$res = Shopmate\BitrixInternals\CatGroupLangTable::getList(array(
					"select" => array("ID"),
					"filter" => array(
						"CATALOG_GROUP_ID" => $primary,
						"LANG" => LANGUAGE_ID
					),
				));
				if ($row = $res->fetch())
					$res = Shopmate\BitrixInternals\CatGroupLangTable::update($row["ID"], array("NAME" => $data["NAME"]));
				else
					$res = Shopmate\BitrixInternals\CatGroupLangTable::add(array(
						"CATALOG_GROUP_ID" => $primary,
						"LANG" => LANGUAGE_ID,
						"NAME" => $data["NAME"],
					));
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());


				$groupId = false;
				$res = Shopmate\BitrixInternals\GroupTable::getList(array(
					"select" => array("ID", "NAME", "ACTIVE"),
					"filter" => array(
						"STRING_ID" => "PRICELIST_ID_".$primary,
					),
				));
				if ($row = $res->fetch())
				{
					$groupId = $row["ID"];
					if ($row["ACTIVE"] != "Y" || $row["NAME"] != $data["NAME"])
						$res = Shopmate\BitrixInternals\GroupTable::update($row["ID"], array("NAME" => $data["NAME"], "ACTIVE" => "Y"));
				}
				else
				{
					$res = Shopmate\BitrixInternals\GroupTable::add(array("NAME" => $data["NAME"], "ACTIVE" => "Y", "STRING_ID" => "PRICELIST_ID_".$primary));
					$groupId = $res->getId();
				}
				if ($res instanceof \Bitrix\Main\Entity\UpdateResult && !$res->isSuccess())
					$result->addErrors($res->getErrors());


				if ($groupId)
				{
					$buyId = $nobuyId = false;
					$res = Shopmate\BitrixInternals\CatGroup2GroupTable::getList(array(
						"select" => array("ID", "BUY"),
						"filter" => array(
							"CATALOG_GROUP_ID" => $primary,
							"GROUP_ID" => array($groupId, 1),
						),
					));
					while ($row = $res->fetch())
					{
						if ($row["BUY"] == "Y")
							$buyId = $row["ID"];
						else
							$nobuyId = $row["ID"];
					}
					if (!$buyId)
						$res = Shopmate\BitrixInternals\CatGroup2GroupTable::add(array(
							"CATALOG_GROUP_ID" => $primary,
							"GROUP_ID" => 1,
							"BUY" => "Y"
						));
					if (!$nobuyId)
						$res = Shopmate\BitrixInternals\CatGroup2GroupTable::add(array(
							"CATALOG_GROUP_ID" => $primary,
							"GROUP_ID" => $groupId,
							"BUY" => "N"
						));
				}

				foreach ($data["PRODUCTS"] as $prodPrice) 
				{
					$priceData = array(
						"PRODUCT_ID" => $prodPrice["PRODUCT_ID"],
						"PRICE" => floatval($prodPrice["PRICE"]),
						"PRICE_SCALE" => floatval(empty($prodPrice["PRICE_SCALE"]) ? $prodPrice["PRICE"] : $prodPrice["PRICE_SCALE"]),
						"CURRENCY" => empty($prodPrice["CURRENCY"]) ? "RUB" : $prodPrice["CURRENCY"],
						"CATALOG_GROUP_ID" => $primary,

					);

					if (empty($prodPrice["ID"]))
						$res = Shopmate\BitrixInternals\PriceTable::add($priceData);
					else
						$res = Shopmate\BitrixInternals\PriceTable::update($prodPrice["ID"], $priceData);

					if(!$res->isSuccess())
						$result->addErrors($res->getErrors());
				}
			}

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}

		return $result;
	}

	public function delete($primary)
	{
		$primary = intval($primary);
		$result = parent::delete($primary);

		if ($primary == \Yadadya\Shopmate\Shops::getUserPrice())
			$result->addError(new Entity\EntityError("Base price can't delete."));

		if(!$result->isSuccess()) return $result;
		
		$result = Shopmate\BitrixInternals\CatGroupTable::delete($primary);

		$delClass = Base::getSqlFieldClass(Shopmate\BitrixInternals\CatGroup2GroupTable::getTableName(), array("CATALOG_GROUP_ID" => array("data_type" => "integer", "primary" => true)), "CatGroup2Group")."Table";
		$res = $delClass::delete(array("CATALOG_GROUP_ID" => $primary));
		if (!$res->isSuccess())
			$result->addErrors($res->getErrors());

		$delClass = Base::getSqlFieldClass(Shopmate\BitrixInternals\GroupTable::getTableName(), array("STRING_ID" => array("data_type" => "string", "primary" => true)), "Group")."Table";
		$res = $delClass::delete(array("STRING_ID" => "PRICELIST_ID_".$primary));
		if (!$res->isSuccess())
			$result->addErrors($res->getErrors());

		$delClass = Base::getSqlFieldClass(Shopmate\BitrixInternals\PriceTable::getTableName(), array("CATALOG_GROUP_ID" => array("data_type" => "integer", "primary" => true)), "Price")."Table";
		$res = $delClass::delete(array("CATALOG_GROUP_ID" => $primary));
		if (!$res->isSuccess())
			$result->addErrors($res->getErrors());

		return $result;
	}
}