<?
namespace Yadadya\Shopmate;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Discount
{
	public static $group_prefix = "DISCOUNT_ID_";
	function getGroups($list = false)
	{
		$arGroupDiscount = $arGroups = array();
		$obGroup = new \CGroup;
		$rsGroups = $obGroup->GetList($by = "id", $order = "asc", array("STRING_ID" => self::$group_prefix."%"));
		while($arGroup = $rsGroups->Fetch())
		{
			$arGroupDiscount[$arGroup["ID"]] = substr($arGroup["STRING_ID"], strlen(self::$group_prefix));
			$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
		}

		$arDiscounts = array();
		$dbDiscounts = \CCatalogDiscount::GetList(array("SORT" => "ASC"), array("ACTIVE" => "Y",), false, false, array("ID", "NAME", "GROUP_ID"));
		while ($arDiscount = $dbDiscounts->Fetch())
		{
			$arDiscounts[$arDiscount["ID"]]["ID"] = $arDiscount["ID"];
			$arDiscounts[$arDiscount["ID"]]["NAME"] = $arDiscount["NAME"];
			if(array_key_exists($arDiscount["GROUP_ID"], $arGroups))
			{
				$arDiscounts[$arDiscount["ID"]]["GROUP_ID_DISCOUNT"][] = $arDiscount["GROUP_ID"];
			}
			else
			{
				$arDiscounts[$arDiscount["ID"]]["GROUP_ID"][] = $arDiscount["GROUP_ID"];
			}
		}

		$obGroup = new \CGroup;

		foreach($arDiscounts as $discount_id => $arDiscount)
		{
			$arGroupFields = Array(
				"ACTIVE"       => "Y",
				"NAME"         => $arDiscount["NAME"],
				"STRING_ID"    => self::$group_prefix.$discount_id,
				"DISCOUNT_EDIT"=> "Y"
			);
			if(($groupID = array_search($discount_id, $arGroupDiscount)) !== false)
			{
				if($arGroups[$groupID] != $arDiscount["NAME"])
					$obGroup->Update($groupID, $arGroupFields);
			}
			else
			{
				$groupID = $obGroup->Add($arGroupFields);
			}
			$arGroups[$groupID] = $arGroupFields["NAME"];
			if(!is_array($arDiscount["GROUP_ID_DISCOUNT"]) || count($arDiscount["GROUP_ID_DISCOUNT"]) != 1 || $arDiscount["GROUP_ID_DISCOUNT"][0] != $groupID)
			{
				\CCatalogDiscount::Update($discount_id, array("GROUP_IDS" => array($groupID)));
				$arDiscounts[$discount_id]["GROUP_ID_DISCOUNT"] = array($groupID);
			}
		}

		$arOldGroup = array_keys($arGroups);

		foreach($arDiscounts as $discount_id => $arDiscount)
			foreach($arDiscount["GROUP_ID_DISCOUNT"] as $group_id)
				while (($i = array_search($group_id, $arOldGroup)) !== false)
					unset($arOldGroup[$i]);

		foreach($arGroups as $group_id => $group_name)
			if(in_array($group_id, $arOldGroup))
				unset($arGroups[$group_id]);
		
		foreach($arOldGroup as $group_id)
			$obGroup->Delete($group_id);

		return $list ? $arGroups : array_keys($arGroups);
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
			if(stripos($arFields["STRING_ID"], self::$group_prefix) === 0 && $arFields["DISCOUNT_EDIT"] != "Y")
			{
				$APPLICATION->throwException("Символьный идентификатор с префиксом \"".self::$group_prefix."\" является системным.");
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
				$arGroup = \CGroup::GetByID($id)->Fetch();
				if(stripos($arGroup["STRING_ID"], self::$group_prefix) === 0 && $arGroup["STRING_ID"] != $arFields["STRING_ID"] && $arFields["DISCOUNT_EDIT"] != "Y")
				{
					$APPLICATION->throwException("Символьный идентификатор с префиксом \"".self::$group_prefix."\" является системным.");
					return false;
				}
			}
			else
			{
				//delete
				$arGroup = \CGroup::GetByID($id)->Fetch();
				if(stripos($arGroup["STRING_ID"], self::$group_prefix) === 0)
				{
					$APPLICATION->throwException("Символьный идентификатор с префиксом \"".self::$group_prefix."\" является системным.");
					return false;
				}
			}
		}
	}


}