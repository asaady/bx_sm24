<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class SettingsChapter extends Base
{
	protected static $currentFields = array("ID", "NAME", "STRING_ID"/*, "PARENT_FORMATED"*/);
	protected static $currentSort = array("PARENT_ID" => "ASC", "ID" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(
			"REQUIRED" => "Y"
		),
		"STRING_ID" => array(
			"REQUIRED" => "Y",
			"UNIQUE" => "Y"
		),
		"PARENT_ID" => Array(
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["PARENT_ID"]["PROPERTY_TYPE"] = "L";
		$res = Shopmate\Internals\SettingsChapterTable::getList(array("select" => array("ID", "NAME", "PARENT_ID")));
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
		
		$parameters["count_total"] = false;

		return Shopmate\Internals\SettingsChapterTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEMS"]))
			$arResult["ITEMS"] = parentSort($arResult["ITEMS"], 0, "PARENT_ID");

		return $arResult;
	}

	public function getByID($primary = 0)
	{
		$result = Shopmate\Internals\SettingsChapterTable::getList(array("filter" => array("ID" => $primary)))->fetch();

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
		print_p($primary);
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
				$res = Shopmate\Internals\SettingsChapterTable::update($primary, $data);
			else
			{
				$res = Shopmate\Internals\SettingsChapterTable::add($data);
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

		$result = Shopmate\Internals\SettingsChapterTable::delete($primary);

		return $result;
	}
}