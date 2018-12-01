<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class SettingsGroup extends Base
{
	protected static $currentFields = array("ID", "NAME");
	protected static $currentSort = array("PARENT_ID" => "ASC", "ID" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(
			"REQUIRED" => "Y"
		),
		"DEPARTMENT_ID" => Array(
		),
		"POSITION_ID" => Array(
		),
		"USER_ID" => Array(
		),
		"PARENT_ID" => Array(
		),
		"CHAPTERS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"READONLY" => "Y",
			"PROPERTY_LIST" => array(
				"NAME" => array(
					"DISABLED" => "Y",
				),
				"READ" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
					"USER_TYPE" => "checkbox",
					"ENUM" => array("Y" => " "),
				),
				"WRITE" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
					"USER_TYPE" => "checkbox",
					"ENUM" => array("Y" => " "),
				),
				"DELETE" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
					"USER_TYPE" => "checkbox",
					"ENUM" => array("Y" => " "),
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
				"DEPTH_LEVEL" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["PARENT_ID"]["PROPERTY_TYPE"] = "L";
		$res = Shopmate\Internals\SettingsGroupTable::getList(array("select" => array("ID", "NAME", "PARENT_ID")));
		while ($row = $res->fetch())
			$chapterList[] = $row;
		$chapterList = parentSort($chapterList, 0, "PARENT_ID");
		foreach ($chapterList as $chapter) 
			$propList["PARENT_ID"]["ENUM"][$chapter["ID"]] = str_repeat(" .", $chapter["DEPTH_LEVEL"])." ".$chapter["NAME"];

		return $propList;
	}

	public static function GetUserPermission()
	{
		global $USER;
		return $USER->IsAdmin() ? "X" : "";
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array("PARENT_FORMATED" => "PARENT.NAME");
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);
		$parameters["select"][] = "PARENT_ID";
		$parameters["filter"]["STORE_ID"] = Shopmate\Shops::getUserStore();

		$parameters["count_total"] = false;

		return Shopmate\Internals\SettingsGroupTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEMS"]))
			$arResult["ITEMS"] = parentSort($arResult["ITEMS"], 0, "PARENT_ID");

		if (!empty($arResult["ITEM"]))
		{
			$personalEnum = \Yadadya\Shopmate\Components\Personal::getEnumList(array("ID" => $arResult["ITEM"]["USER_ID"]));
			$departmentEnum = \Yadadya\Shopmate\Components\PersonalDepartment::getEnumList(/*array("ID" => $arResult["ITEM"]["DEPARTMENT_ID"])*/);
			$positionEnum = \Yadadya\Shopmate\Components\PersonalPosition::getEnumList(/*array("ID" => $arResult["ITEM"]["POSITION_ID"])*/);
			$propListTemplate = array( 
				"DEPARTMENT_ID" => Array(
					"PROPERTY_TYPE" => "L", 
					"MULTIPLE" => "Y",
					"ENUM" => $departmentEnum, 
				),
				"POSITION_ID" => Array(
					"PROPERTY_TYPE" => "L", 
					"MULTIPLE" => "Y",
					"ENUM" => $positionEnum, 
				),
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
			$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate); 
		}

		return $arResult;
	}

	public function getByID($primary = 0)
	{
		$result = Shopmate\Internals\SettingsGroupTable::getList(array("filter" => array("ID" => $primary)))->fetch();

		$res = Shopmate\Internals\SettingsChapterTable::getList(array(
			"select" => array("ID", "NAME", "PARENT_ID", "RIGHT" => "RIGHTS.RIGHT"),
			"runtime" => array(
				"RIGHTS" => new Entity\ReferenceField(
					'RIGHTS',
					'Yadadya\Shopmate\Internals\SettingsRight',
					array('=ref.CHAPTER_ID' => 'this.ID', 'ref.GROUP_ID' => new DB\SqlExpression($primary)),
					array('join_type' => 'LEFT')
				),
			)
		));
		while ($row = $res->fetch())
		{
			$row = array_merge($row, self::charToRights($row["RIGHT"]));
			$result["CHAPTERS"][] = $row;
		}
		$result["CHAPTERS"] = parentSort($result["CHAPTERS"], 0, "PARENT_ID");

		$itemsTypes = array("DEPARTMENT", "POSITION", "USER");
		$res = Shopmate\Internals\SettingsGroupItemTable::getList(array(
			"select" => array("ITEM_ID", "ITEM_TYPE"),
			"filter" => array("GROUP_ID" => $primary)
		));
		while ($row = $res->fetch())
			if (in_array($row["ITEM_TYPE"], $itemsTypes))
				$result[$row["ITEM_TYPE"]."_ID"][] = $row["ITEM_ID"];

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
			$groupData = array();
			foreach ($data as $key => $value) 
				if (in_array($key, array("NAME", "PARENT")))
					$groupData[$key] = $value;
			if ($primary > 0)
				$res = Shopmate\Internals\SettingsGroupTable::update($primary, $groupData);
			else
			{
				$groupData["STORE_ID"] = Shopmate\Shops::getUserStore();
				$res = Shopmate\Internals\SettingsGroupTable::add($groupData);
				if ($res->isSuccess())
					$primary = $res->GetID();
			}

			if(!$res->isSuccess())
				$result->addErrors($res->getErrors());

			if ($primary > 0)
			{
				//CHAPTERS RIGHTS
				$savedRights = array();
				$res = Shopmate\Internals\SettingsRightTable::getList(array(
					"filter" => array("GROUP_ID" => $primary)
				));
				while ($row = $res->fetch())
					$savedRights[] = $row["CHAPTER_ID"];

				foreach ($data["CHAPTERS"] as $chapter) 
				{
					$right = self::rightsToChar($chapter);
					if (in_array($chapter["ID"], $savedRights))
						$res = Shopmate\Internals\SettingsRightTable::update(array("GROUP_ID" => $primary, "CHAPTER_ID" => $chapter["ID"]), array("RIGHT" => $right));
					else
						$res = Shopmate\Internals\SettingsRightTable::add(array("GROUP_ID" => $primary, "CHAPTER_ID" => $chapter["ID"], "RIGHT" => $right));

					if(!$res->isSuccess())
						$result->addErrors($res->getErrors());
				}

				//GROUP ITEMS
				$savedItems = array();
				$res = Shopmate\Internals\SettingsGroupItemTable::getList(array(
					"select" => array("ID", "ITEM_ID", "ITEM_TYPE"),
					"filter" => array("GROUP_ID" => $primary)
				));
				while ($row = $res->fetch())
					$savedItems[$row["ITEM_TYPE"]."_".$row["ITEM_ID"]] = $row;

				$itemsTypes = array("DEPARTMENT", "POSITION", "USER");
				foreach ($itemsTypes as $itemsType) 
					foreach ($data[$itemsType."_ID"] as $item) 
						if ($item > 0)
						{
							if (!array_key_exists($itemsType."_".$item, $savedItems))
							{
								$res = Shopmate\Internals\SettingsGroupItemTable::add(array("ITEM_ID" => $item, "ITEM_TYPE" => $itemsType, "GROUP_ID" => $primary));
								if (!$res->isSuccess())
									$result->addErrors($res->getErrors());
								else
									unset($savedItems[$itemsType."_".$item]);
							}
							else
								unset($savedItems[$itemsType."_".$item]);
						}

				foreach ($savedItems as $savedItem) 
					$res = Shopmate\Internals\SettingsGroupItemTable::delete($savedItem["ID"]);
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
		if(!$result->isSuccess()) return $result;

		$result = Shopmate\Internals\SettingsGroupTable::delete($primary);

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

		$result = Shopmate\Internals\SettingsGroupTable::GetList(array(
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

	public function rightsToChar(array $rights = array())
	{
		return $rights["DELETE"] == "Y" ? "X" : ($rights["WRITE"] == "Y" ? "W" : ($rights["READ"] == "Y" ? "R" : ""));
	}

	public function charToRights($char = "")
	{
		$rights = array();
		if ($char >= "R")
			$rights["READ"] = "Y";
		if ($char >= "W")
			$rights["WRITE"] = "Y";
		if ($char >= "X")
			$rights["DELETE"] = "Y";
		return $rights;
	}
}