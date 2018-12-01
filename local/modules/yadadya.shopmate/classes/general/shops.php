<?
IncludeModuleLangFile(__FILE__);

class SMShops
{
	public static $shop_prefix = "SHOP_ID_";

	//add/edit/delete shop's user group & price type
	function CustomShop($shopID, $shopFields) //OnCatalogStoreAdd, OnCatalogStoreUpdate, OnCatalogStoreDelete
	{
		CModule::IncludeModule("catalog");

		//shop's user group
		$groupID = 0;
		$obGroup = new CGroup;
		$rsGroups = $obGroup->GetList($by = "id", $order = "asc", array("STRING_ID" => self::$shop_prefix.$shopID));
		if($arGroups = $rsGroups->Fetch())
			$groupID = $arGroups["ID"];
		if(!empty($shopFields))
		{
			$arGroupFields = Array(
				"ACTIVE"       => "Y",
				"NAME"         => $shopFields["TITLE"],
				"STRING_ID"    => static::$shop_prefix.$shopID,
				"SHOP_EDIT"    => "Y"
			);
			if($groupID > 0)
				$obGroup->Update($groupID, $arGroupFields);
			else $groupID = $obGroup->Add($arGroupFields);
		}
		elseif($groupID > 0)
		{
			$obGroup->Delete($groupID);
		}


		//shop's price type
		$priceID = 0;
		$obPriceType = new CCatalogGroup;
		$rsPriceTypes = $obPriceType->GetList(array("ID" => "ASC"), array("XML_ID" => self::$shop_prefix.$shopID));
		if($arPriceType = $rsPriceTypes->Fetch())
			$priceID = $arPriceType["ID"];
		if(!empty($shopFields))
		{
			$arPriceTypeFields = Array(
				"NAME" => self::$shop_prefix.$shopID,
				"BASE" => "N",
				"XML_ID" => self::$shop_prefix.$shopID,
				"USER_GROUP" => array($groupID),
				"USER_GROUP_BUY" => array(1),
				"USER_LANG" => array(LANGUAGE_ID => $shopFields["TITLE"]),
				"SHOP_EDIT" => "Y"
			);
			if($priceID > 0)
				$obPriceType->Update($priceID, $arPriceTypeFields);
			else $priceID = $obPriceType->Add($arPriceTypeFields);
		}
		elseif($priceID > 0)
		{
			$obPriceType->Delete($priceID);
		}
	}

	function CustomGroupClose($param1, $param2)
	{
		global $APPLICATION;
		$id = 0;
		$arFields = array();
		if(is_array($param1))
		{
			//add
			$arFields = &$param1;
			if(stripos($arFields["STRING_ID"], self::$shop_prefix) === 0 && $arFields["SHOP_EDIT"] != "Y")
			{
				$APPLICATION->throwException("Символьный идентификатор с префиксом \"".self::$shop_prefix."\" является системным.");
				return false;
			}
		}
		else
		{
			$id = &$param1;
			if(is_array($param2))
			{
				//update
				$arFields = &$param2;
				$arGroup = CGroup::GetByID($id)->Fetch();
				if(stripos($arGroup["STRING_ID"], self::$shop_prefix) === 0 && $arGroup["STRING_ID"] != $arFields["STRING_ID"] && $arFields["SHOP_EDIT"] != "Y")
				{
					$APPLICATION->throwException("Символьный идентификатор с префиксом \"".self::$shop_prefix."\" является системным.");
					return false;
				}
			}
			else
			{
				//delete
				$arGroup = CGroup::GetByID($id)->Fetch();
				if(stripos($arGroup["STRING_ID"], self::$shop_prefix) === 0)
				{
					$shopID = substr($arPriceType["XML_ID"], strlen(self::$shop_prefix));
					if(self::issetStore($shopID))
					{
						$APPLICATION->throwException("Символьный идентификатор с префиксом \"".self::$shop_prefix."\" является системным.");
						return false;
					}
				}
			}
		}
	}

	function CustomPriceTypeClose($param1, $param2)
	{
		global $APPLICATION;
		$id = 0;
		$arFields = array();
		if(is_array($param1))
		{
			//add
			$arFields = &$param1;
			if(stripos($arFields["XML_ID"], self::$shop_prefix) === 0 && $arFields["SHOP_EDIT"] != "Y")
			{
				$APPLICATION->throwException("Внешний код с префиксом \"".self::$shop_prefix."\" является системным.");
				return false;
			}
		}
		else
		{
			CModule::IncludeModule("catalog");
			$id = &$param1;
			if(is_array($param2))
			{
				//update
				$arFields = &$param2;
				$arPriceType = CCatalogGroup::GetByID($id);
				if(stripos($arPriceType["XML_ID"], self::$shop_prefix) === 0 && $arPriceType["XML_ID"] != $arFields["XML_ID"] && $arFields["SHOP_EDIT"] != "Y")
				{
					$APPLICATION->throwException("Внешний код с префиксом \"".self::$shop_prefix."\" является системным.");
					return false;
				}
				//save connect with user group
				if(isset($arFields["USER_GROUP"]) && is_array($arFields["USER_GROUP"]))
				{
					$groupID = 0;
					$shopID = substr($arPriceType["XML_ID"], strlen(self::$shop_prefix));
					$rsGroups = CGroup::GetList($by = "id", $order = "asc", array("STRING_ID" => self::$shop_prefix.$shopID));
					if($arGroups = $rsGroups->Fetch())
						$groupID = $arGroups["ID"];
					if($groupID > 0 && !in_array($groupID, $arFields["USER_GROUP"]))
						$arFields["USER_GROUP"][] = $groupID;
				}
			}
			else
			{
				//delete
				$arPriceType = CCatalogGroup::GetByID($id);
				if(stripos($arPriceType["XML_ID"], self::$shop_prefix) === 0)
				{
					$shopID = substr($arPriceType["XML_ID"], strlen(self::$shop_prefix));
					if(self::issetStore($shopID))
					{
						$APPLICATION->throwException("Внешний код с префиксом \"".self::$shop_prefix."\" является системным.");
						return false;
					}
				}
			}
		}
	}

	function getUserShops($shopID = false)
	{
		global $USER;

		$arGroups = array();

		$obGroup = new CGroup;
		$arFilter = array("STRING_ID" => self::$shop_prefix.($shopID ? $shopID : "%"));
		if(!$USER->IsAdmin()) 
			$arFilter["ID"] = implode("|", $USER->GetUserGroupArray());
		$rsGroups = $obGroup->GetList($by = "id", $order = "asc", $arFilter);
		while($arGroup = $rsGroups->Fetch())
		{
			$arGroup["SHOP_ID"] = substr($arGroup["STRING_ID"], strlen(self::$shop_prefix));
			$arGroups[] = $arGroup;
			/*$currentShop = substr($arGroup["STRING_ID"], strlen(self::$shop_prefix));
			$currentGroup = $arGroup["ID"];
			$currentPrice = $currentPriceName = false;*/
		}

		return $arGroups;
	}

	function setUserShopParams($shopID = false)
	{
		global $USER;

		$currentShop = self::getUserShop();
		$currentGroup = self::getUserGroup();
		$currentPrice = self::getUserPrice();
		$currentPriceName = self::getUserPriceName();

		$shopID = IntVal($shopID);

		if($shopID > 0 || !($currentShop && $currentGroup && $currentPrice))
		{
			$shopFields = \Bitrix\Catalog\StoreTable::GetList(array("select" => array("ID", "TITLE"), "filter" => array("ID" => $shopID)))->fetch();
			self::CustomShop($shopFields["ID"], array("TITLE" => $shopFields["TITLE"]));
			if($shopID > 0 || !($currentShop && $currentGroup))
			{
				$arGroups = self::getUserShops($shopID);
				if(!empty($arGroups))
				{
					$arGroup = current($arGroups);
					$currentShop = substr($arGroup["STRING_ID"], strlen(self::$shop_prefix));
					$currentGroup = $arGroup["ID"];
					$currentPrice = $currentPriceName = false;
				}
				/*$obGroup = new CGroup;
				$rsGroups = $obGroup->GetList($by = "id", $order = "asc", array("STRING_ID" => self::$shop_prefix.($shopID ? $shopID : "%"), "ID" => implode("|", $USER->GetUserGroupArray())));
				if($arGroup = $rsGroups->Fetch())
				{
					$currentShop = substr($arGroup["STRING_ID"], strlen(self::$shop_prefix));
					$currentGroup = $arGroup["ID"];
					$currentPrice = $currentPriceName = false;
				}*/
			}
			if($currentShop && !$currentPrice)
			{
				CModule::IncludeModule("catalog");
				$rsPriceTypes = CCatalogGroup::GetList(
					array("ID" => "ASC"),
					array(/*"CAN_ACCESS" => "Y", */"XML_ID" => self::$shop_prefix.$currentShop),
					false,
					false,
					array()
				);
				if($arPriceType = $rsPriceTypes->Fetch())
				{
					$currentPrice = $arPriceType["ID"];
					$currentPriceName = $arPriceType["NAME"];
				}
			}
			if($currentShop && $currentGroup && $currentPrice)
			{
				$USER->SetParam("SM_SHOP", $currentShop);
				$USER->SetParam("SM_GROUP", $currentGroup);
				$USER->SetParam("SM_PRICE", $currentPrice);
				$USER->SetParam("SM_PRICE_NAME", $currentPriceName);
			}
			else
			{
				$USER->SetParam("SM_SHOP", false);
				$USER->SetParam("SM_GROUP", false);
				$USER->SetParam("SM_PRICE", false);
				$USER->SetParam("SM_PRICE_NAME", false);
			}
		}
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
		$obGroup = new CGroup;
		$rsGroups = $obGroup->GetList($by = "id", $order = "asc", array("STRING_ID" => self::$shop_prefix."%"));
		while($arGroup = $rsGroups->Fetch())
			$arGroups[] = $arGroup["ID"];
		return $arGroups;
	}

	function issetStore($shopID = 0)
	{
		$shopID = IntVal($shopID);
		if($shopID > 0 && CModule::IncludeModule("catalog"))
		{
			$rsStore = CCatalogStore::GetList(array(), array("ID" => $shopID), false, false, array("ID"));
			if($arStore = $rsStore->Fetch())
				return $arStore["ID"];
			else return false;
		}
		else return false;
	}
}