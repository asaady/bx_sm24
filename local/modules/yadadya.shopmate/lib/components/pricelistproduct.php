<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class PricelistProduct extends Base
{
	protected static $store_prefix = "SHOP_ID_";
	protected static $currentFields = array("ID", "NAME");
	protected static $currentSort = array("ID" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(
			"DISABLED" => "Y"
		),
		"PRODUCTS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"READONLY" => "Y",
			"PROPERTY_LIST" => array(
				"NAME" => array(
					"DISABLED" => "Y",
				),
				"PRICE" => array(
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		"COMMENT" => array(
			"REQUIRED" => "Y",
			"PROPERTY_TYPE" => "T"
		)
	);

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("products");
	}*/

	public function getList(array $parameters = array())
	{
		$referenceFields = array();
		$parameters["runtime"][] = new Entity\ReferenceField(
			'PROD_STORY',
			'Yadadya\Shopmate\Internals\ShopProductHistory',
			array('=ref.PRODUCT_ID' => 'this.ID', 'ref.SHOP_ID' => new DB\SqlExpression(Shopmate\Shops::getUserShop())),
			array('join_type' => 'INNER')
		);
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		return Iblock\ElementTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		$arResult["SHOW_ADD_ITEM_BUTTON"] = "N";
		$arResult["CAN_DELETE"] = "N";
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

		$res = Shopmate\BitrixInternals\PriceTable::getList(array(
			"select" => array("ID", "PRODUCT_ID", "PRICE", 
				"NAME" =>  new Entity\ExpressionField("NAME", 
				"(CASE 
					WHEN (%1\$s = \"\") OR (%1\$s IS NULL)
						THEN %2\$s
					ELSE %1\$s 
				END)",
				array("CATALOG_GROUP.CURRENT_LANG.NAME", "CATALOG_GROUP.NAME")
			),
			),
			"filter" => array(
				"PRODUCT_ID" => $primary,
				"QUANTITY_FROM" => false,
				"QUANTITY_TO" => false,
				"CATALOG_GROUP.XML_ID" => self::$store_prefix.Shopmate\Shops::getUserStore()."%"
			),
		));
		$result["PRODUCTS"] = $res->fetchAll();
		
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
			$result->addError(new Entity\EntityError("It can't add."));
			//self::update(0, $data, $result);
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
			if($primary > 0)
			{
				foreach ($data["PRODUCTS"] as $prodPrice) 
				{
					$priceData = array(
						"PRICE" => floatval($prodPrice["PRICE"]),
						"PRICE_SCALE" => floatval(empty($prodPrice["PRICE_SCALE"]) ? $prodPrice["PRICE"] : $prodPrice["PRICE_SCALE"]),

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

		$result->addError(new Entity\EntityError("It can't delete."));

		return $result;
	}
}