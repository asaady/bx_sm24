<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class Section extends Base
{
	protected static $currentFields = array("ID", "NAME_FORMATED", "SORT");
	protected static $currentSort = array("SORT" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(
			"REQUIRED" => "Y"
		),
		"IBLOCK_SECTION_ID" => array(
			"PROPERTY_TYPE" => "L",
		),
		"CODE" => array(
			"UNIQUE" => "Y"
		),
		"SORT" => Array(
			"PROPERTY_TYPE" => "N",
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$arEnum = array();
		$arEnum["SECTIONS"] = Products::getSectionsEnum();
		foreach ($arEnum["SECTIONS"] as $sectionId => $arSection)
			$arEnum["SECTIONS"][$sectionId]["VALUE"] = $arSection["DEPTH_LEVEL"] > 0 ? str_repeat("- ", $arSection["DEPTH_LEVEL"])." ".$arSection["VALUE"] : "- ".$arSection["VALUE"];
		$propList["IBLOCK_SECTION_ID"]["ENUM"] = $arEnum["SECTIONS"];

		return $propList;
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array("PARENT_FORMATED" => "PARENT.NAME", "NAME_FORMATED" => new Entity\ExpressionField("NAME_FORMATED", "CONCAT(REPEAT('- ', %s), %s)", ["DEPTH_LEVEL", "NAME"]));
		
		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["filter"]["IBLOCK_ID"] = is_object($this) ? $this->getCatalogID() : self::getCatalogID();

		if (is_array($parameters["order"]))
			$parameters["order"] = array_merge(["LEFT_MARGIN" => "ASC"], $parameters["order"]);
		
		$parameters["count_total"] = false;

		return Shopmate\BitrixInternals\SectionTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (is_array($arResult["ITEMS"]))
		{
			$arResult["VIEW_DELETE"] = "N";
		}
		return $arResult;
	}

	public function getByID($primary = 0)
	{
		$result = Shopmate\BitrixInternals\SectionTable::getList(array("filter" => array("ID" => $primary)))->fetch();

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
			$tobj = new \CIBlockSection;
			if (!isset($data["ACTIVE"]))
				$data["ACTIVE"] = "Y";
			if ($primary > 0)
			{
				$res = $tobj->Update($primary, $data);
				if(!$res) 
					$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
			}
			else
			{
				$data["IBLOCK_ID"] = self::getCatalogID();
				$primary = $tobj->Add($data);
				if(!$primary)
					$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
			}

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}

		return $result;
	}

	public static function getEnumByID(array $elementsID = array())
	{
		return self::getEnumList(array("ID" => $elementsID));
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();

		$filter["IBLOCK_ID"] = self::getCatalogID();

		if(isset($filter["SEARCH"]))
		{
			$q = $filter["SEARCH"];
			$filter["NAME"] = "%".$q."%";
		}
		unset($filter["SEARCH"]);

		$parameters = array(
			"select" => array("ID", "NAME", "DEPTH_LEVEL"),
			"filter" => $filter,
			"order" => ["LEFT_MARGIN" => "ASC"],
			"limit" => empty($q) ? 0 : 10
		);

		$result = self::getList($parameters);
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = str_repeat("- ", $row["DEPTH_LEVEL"])." ".$row["NAME"];
			if (!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}

	/*public function delete($primary)
	{
		$primary = intval($primary);
		$result = parent::delete($primary);
		if(!$result->isSuccess()) return $result;

		$result = Shopmate\BitrixInternals\SectionTable::delete($primary);

		return $result;
	}*/
}