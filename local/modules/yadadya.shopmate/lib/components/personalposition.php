<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class PersonalPosition extends Base
{
	protected static $currentFields = array("ID", "NAME");
	protected static $currentSort = array("PARENT_ID" => "ASC", "ID" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(
			"REQUIRED" => "Y"
		),
		"PARENT_ID" => Array(
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["PARENT_ID"]["PROPERTY_TYPE"] = "L";
		$res = Shopmate\Internals\PersonalPositionTable::getList(array("select" => array("ID", "NAME", "PARENT_ID")));
		while ($row = $res->fetch())
			$parentList[] = $row;
		$parentList = parentSort($parentList, 0, "PARENT_ID");
		foreach ($parentList as $parent) 
			$propList["PARENT_ID"]["ENUM"][$parent["ID"]] = str_repeat(" .", $parent["DEPTH_LEVEL"])." ".$parent["NAME"];

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("personal");
	}*/

	public function getList(array $parameters = array())
	{
		$referenceFields = array("PARENT_FORMATED" => "PARENT.NAME");
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);
		$parameters["select"][] = "PARENT_ID";
		$parameters["filter"]["STORE_ID"] = Shopmate\Shops::getUserStore();

		unset($parameters["offset"], $parameters["limit"]);

		return Shopmate\Internals\PersonalPositionTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEMS"]))
			$arResult["ITEMS"] = parentSort($arResult["ITEMS"], 0, "PARENT_ID");

		if (!empty($arResult["ITEM"]))
		{
			/*$personalEnum = \Yadadya\Shopmate\Components\Personal::getEnumList(array("ID" => $arResult["ITEM"]["USER_ID"]));
			$propListTemplate = array( 
				"USER_ID" => array( 
					"PROPERTY_TYPE" => "L", 
					"LIST_TYPE" => "AJAX",
					"MULTIPLE" => "Y",
					"ENUM" => $personalEnum, 
					"DATA" => array( 
						"url" => getLocalPath("modules/yadadya.shopmate/ajax/search_personal.php"), 
					), 
				), 
			);
			$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate);*/
		}

		return $arResult;
	}

	public function getByID($primary = 0)
	{
		$result = Shopmate\Internals\PersonalPositionTable::getList(array("filter" => array("ID" => $primary)))->fetch();
		return $result;
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
			if ($primary > 0)
				$res = Shopmate\Internals\PersonalPositionTable::update($primary, $data);
			else
			{
				$data["STORE_ID"] = Shopmate\Shops::getUserStore();
				$res = Shopmate\Internals\PersonalPositionTable::add($data);
				if ($res->isSuccess())
					$primary = $res->GetID();
			}

			if(!$res->isSuccess())
				$result->addErrors($res->getErrors());

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
		if(!$result->isSuccess()) return $result;

		$result = Shopmate\Internals\PersonalPositionTable::delete($primary);

		return $result;
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();
		
		if(!empty($filter["SEARCH"]))
		{
			$q = $filter["SEARCH"];
			unset($filter["SEARCH"]);
			$filter[] = array(
				//"LOGIC" => "OR",
				"NAME" => "%".$q."%",
			);
		}
		
		$filter["STORE_ID"] = Shopmate\Shops::getUserStore();

		$result = Shopmate\Internals\PersonalPositionTable::GetList(array(
			"select" => array("ID", "NAME"),
			"filter" => $filter,
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["NAME"];
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}