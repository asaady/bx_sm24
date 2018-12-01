<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class SettingsRight extends Base
{
	protected static $currentFields = array("ID", "ITEM_FORMATED", "GROUP_FORMATED");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"ITEM" => Array(
		),
		"GROUP_ID" => Array(
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		/*$propList["PARENT_ID"]["PROPERTY_TYPE"] = "L";
		$res = Shopmate\Internals\SettingsGroupItemTable::getList(array("select" => array("ID", "NAME", "PARENT_ID")));
		while ($row = $res->fetch())
			$chapterList[] = $row;
		$chapterList = parentSort($chapterList, 0, "PARENT_ID");
		foreach ($chapterList as $chapter) 
			$propList["PARENT_ID"]["ENUM"][$chapter["ID"]] = str_repeat(" .", $chapter["DEPTH_LEVEL"])." ".$chapter["NAME"];*/

		return $propList;
	}

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("settingsright", true);
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array( 
			"ITEM_FORMATED" => new Entity\ExpressionField("ITEM_FORMATED", 
				"(CASE %s 
					WHEN \"USER\" 
						THEN CONCAT(\"".Loc::getMessage("ITEM_TYPE_USER").": \", %s)
					WHEN \"POSITION\" 
						THEN CONCAT(\"".Loc::getMessage("ITEM_TYPE_POSITION").": \", %s)
					WHEN \"DEPARTMENT\" 
						THEN CONCAT(\"".Loc::getMessage("ITEM_TYPE_DEPARTMENT").": \", %s)
					ELSE \"-\" 
				END)", 
				array("ITEM_TYPE", "USER.NAME", "POSITION.NAME", "DEPARTMENT.NAME")
			),
			"GROUP_FORMATED" => "GROUP.NAME"
		);
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);
		$parameters["filter"]["GROUP.STORE_ID"] = Shopmate\Shops::getUserStore();

		$parameters["count_total"] = false;

		return Shopmate\Internals\SettingsGroupItemTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEM"]))
		{
			$itemEnum = \Yadadya\Shopmate\Components\SettingsRight::getEnumList(array("ID" => $arResult["ITEM"]["ITEM_ID"], "ITEM_TYPE" => $arResult["ITEM"]["ITEM_TYPE"]));
			$groupEnum = \Yadadya\Shopmate\Components\SettingsGroup::getEnumList(/*array("ID" => $arResult["ITEM"]["GROUP_ID"])*/);
			$propListTemplate = array( 
				"ITEM" => array( 
					"PROPERTY_TYPE" => "L", 
					"LIST_TYPE" => "AJAX",
					"ENUM" => $itemEnum, 
					"DATA" => array( 
						"url" => getLocalPath("modules/yadadya.shopmate/ajax/search_right_item.php"), 
					), 
				), 
				"GROUP_ID" => Array(
					"PROPERTY_TYPE" => "L", 
					"ENUM" => $groupEnum, 
				),
			);
			$arResult["PROPERTY_LIST"] = Template::propListMerge($arResult["PROPERTY_LIST"], $propListTemplate); 
		}

		return $arResult;
	}

	public function getByID($primary = 0)
	{
		$result = Shopmate\Internals\SettingsGroupItemTable::getList(array("filter" => array("ID" => $primary)))->fetch();
		$result["ITEM"] = $result["ITEM_TYPE"]."_".$result["ITEM_ID"];
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
			$dataItem = explode("_", $data["ITEM"]);
			unset($data["ITEM"]);
			$data["ITEM_TYPE"] = $dataItem[0];
			$data["ITEM_ID"] = $dataItem[1];

			if ($primary > 0)
				$res = Shopmate\Internals\SettingsGroupItemTable::update($primary, $data);
			else
			{
				$res = Shopmate\Internals\SettingsGroupItemTable::add($data);
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

		$result = Shopmate\Internals\SettingsGroupItemTable::delete($primary);

		return $result;
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();

		$itemTypes = array("USER", "POSITION", "DEPARTMENT");

		if (!empty($filter["ITEM_TYPE"]))
			$itemTypes = (array) $filter["ITEM_TYPE"];
		unset($filter["ITEM_TYPE"]);

		$itemEnum["USER"] = \Yadadya\Shopmate\Components\Personal::getEnumList($filter);
		$itemEnum["POSITION"] = \Yadadya\Shopmate\Components\PersonalPosition::getEnumList($filter);
		$itemEnum["DEPARTMENT"] = \Yadadya\Shopmate\Components\PersonalDepartment::getEnumList($filter);

		foreach ($itemTypes as $type) 
		{
			switch ($type) 
			{
				case "USER":
					$itemEnum[$type] = \Yadadya\Shopmate\Components\Personal::getEnumList($filter);
					break;

				case "POSITION":
					$itemEnum[$type] = \Yadadya\Shopmate\Components\PersonalPosition::getEnumList($filter);
					break;

				case "DEPARTMENT":
					$itemEnum[$type] = \Yadadya\Shopmate\Components\PersonalDepartment::getEnumList($filter);
					break;
				
				default:
					$itemEnum[$type] = array();
					break;
			}

			foreach ($itemEnum[$type] as $id => $value) 
			{
				if (is_array($value))
					$value["VALUE"] = "[".Loc::getMessage("ITEM_TYPE_".$type)."] " . $value["VALUE"];
				else
					$value = "[".Loc::getMessage("ITEM_TYPE_".$type)."] " . $value;
				$arResult[$type."_".$id] = $value;
			}
		}
		
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}