<?
namespace Yadadya\Shopmate;
use Bitrix\Main\Localization\Loc;

use Bitrix\Catalog;

Loc::loadMessages(__FILE__);

class Shops
{
	public static $shop_prefix = "SHOP_ID_";

	function issetStore($shopID = 0)
	{
		$shopID = IntVal($shopID);
		if($shopID > 0)
		{
			$res = Catalog\StoreTable::GetList(array(
				"select" => array("ID"),
				"filter" => array("ID" => $shopID)
			));
			if($arStore = $res->Fetch())
				return true;
			else return false;
		}
		else return false;
	}

	function getStores($onlyId = false)
	{
		$result = array();
		$parameters = array("filter" => array("ACTIVE" => "Y"));
		if ($onlyId)
			$parameters["select"] = array("ID");
		$res = Catalog\StoreTable::getList($parameters);
		while ($row = $res->fetch()) 
			$result[] = $onlyId ? $row["ID"] : $row;
		return $result;
	}

	public function setUserShopParams($shopID = false)
	{
		\SMShops::setUserShopParams($shopID);
	}

	function getUserStore()
	{
		return self::getUserShop();
	}

	function getUserShop()
	{
		global $USER;
		$userShop = $USER->GetParam("SM_SHOP");
		return $userShop ? $userShop : false;
	}

	function getUserGroup()
	{
		global $USER;
		$userGroup = $USER->GetParam("SM_GROUP");
		return $userGroup ? $userGroup : false;
	}

	function getUserPrice()
	{
		global $USER;
		$userPrice = $USER->GetParam("SM_PRICE");
		return $userPrice ? $userPrice : false;
	}

	function getUserPriceName()
	{
		global $USER;
		$userPriceName = $USER->GetParam("SM_PRICE_NAME");
		return $userPriceName ? $userPriceName : false;
	}

	function getGroups()
	{
		$arGroups = array();
		$obGroup = new \CGroup;
		$rsGroups = $obGroup->GetList($by = "id", $order = "asc", array("STRING_ID" => self::$shop_prefix."%"));
		while($arGroup = $rsGroups->Fetch())
			$arGroups[] = $arGroup["ID"];
		return $arGroups;
	}

	function getGroupByStore($store = 0, $only_id = true)
	{
		if($store > 0)
		{
			if($group = \Bitrix\Main\GroupTable::GetList(array("select" => $only_id ? array("ID") : array("*"), "filter" => array("STRING_ID" => self::$shop_prefix.$store)))->fetch())
				return $only_id ? $group["ID"] : $group;
		}
		return false;
	}

	function getCatalogGroupByStore($store = 0, $only_id = true)
	{
		if($store > 0)
		{
			if($group = \Bitrix\Catalog\GroupTable::GetList(array("select" => $only_id ? array("ID") : array("*"), "filter" => array("XML_ID" => self::$shop_prefix.$store)))->fetch())
				return $only_id ? $group["ID"] : $group;
		}
		return false;
	}

	function getUserPriceGroups($user_id = 0)
	{
		global $USER;
		if (empty($user_id) && $USER->IsAuthorized())
			$user_id = $USER->GetId();
		$groups = array();
		
		if($user_id > 0)
		{
			$parameters = array(
				"select" => array("ID"),
				"filter" => array(
					"XML_ID" => self::$shop_prefix.self::getUserStore()."%",
					"UUG.USER_ID" => $user_id
				),
				"runtime" => array(
					new \Bitrix\Main\Entity\ReferenceField(
						'CG2G',
						'Yadadya\Shopmate\BitrixInternals\CatGroup2Group',
						array('=ref.CATALOG_GROUP_ID' => 'this.ID', "ref.BUY" => new \Bitrix\Main\DB\SqlExpression("?s", "N")),
						array('join_type' => 'LEFT')
					),
					new \Bitrix\Main\Entity\ReferenceField(
						'UG',
						'Yadadya\Shopmate\BitrixInternals\Group',
						array('=ref.ID' => new \Bitrix\Main\DB\SqlExpression("?#", strtolower(\Yadadya\Shopmate\BitrixInternals\CatGroupTable::getEntity()->getCode()."_".strtolower('CG2G')).".GROUP_ID")),
						array('join_type' => 'LEFT')
					),
					new \Bitrix\Main\Entity\ReferenceField(
						'UUG',
						'Yadadya\Shopmate\BitrixInternals\UserGroup',
						array('=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression("?#", strtolower(\Yadadya\Shopmate\BitrixInternals\CatGroupTable::getEntity()->getCode()."_".strtolower('UG')).".ID")),
						array('join_type' => 'LEFT')
					),
				)
			);

			$res = \Yadadya\Shopmate\BitrixInternals\CatGroupTable::getList($parameters);
			while ($row = $res->fetch())
				$groups[] = $row["ID"];
		}

		if (empty($groups))
			$groups = array(self::getUserPrice());
		
		return $groups;
	}

	public function SimpleAdd($title = "")
	{
		$oStore = new \CCatalogStore;

		if(empty($title))
			$title = "sample";

		if($store = $oStore->GetList(array(), array("XML_ID" => $title), false, false, array("ID"))->Fetch())
			return $store["ID"];

		return $oStore->Add(array("TITLE" => $title, "XML_ID" => $title, "ADDRESS" => $title));
	}
}