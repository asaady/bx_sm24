<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class FinanceMoneyCat extends Base
{
	protected static $currentFields = array("ID", "OUTGO", "VALUE");
	protected static $currentSort = array("VALUE" => "ASC");
	protected static $filterList = array();
	protected static $propList = array(
		"OUTGO" => array(
			"PROPERTY_TYPE" => "L",
			"LIST_TYPE" => "C",
			"ENUM" => array(
				"report" => "on report",
				"payroll" => "payroll",
			)
		),
		"VALUE" => array(),
	);
	public static $arParams = array();

	public static function getPropList()
	{
		$propList = static::$propList;

		if (!empty(self::$arParams["OUTGO"]) && array_key_exists(self::$arParams["OUTGO"], $propList["OUTGO"]["ENUM"]))
		{
			$propList["OUTGO"]["DEFAULT_VALUE"] = "report";
			$propList["OUTGO"]["PROPERTY_TYPE"] = "H";
		}

		return $propList;
	}

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("financemoney");
	}

	public function onPrepareParams($arParams)
	{
		self::$arParams = $arParams;
		return parent::onPrepareParams($arParams);
	}

	public static function getOrderList($sort_fields = array())
	{
		if (empty($sort_fields))
			$sort_fields = static::$currentFields;

		if (!empty(self::$arParams["OUTGO"]))
			foreach ($sort_fields as $key => $sort) 
				if ($sort == "OUTGO") 
					unset($sort_fields[$key]);

		return parent::getOrderList($sort_fields);
	}

	public function getList(array $parameters = array())
	{
		if (!empty(self::$arParams["OUTGO"]))
			$parameters["filter"]["OUTGO"] = self::$arParams["OUTGO"];

		$parameters["count_total"] = false;
		
		return Shopmate\Internals\FinanceMoneyCatTable::getList($parameters);
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
		$parameters = array("filter" => array("ID" => $primary));
		return Shopmate\Internals\FinanceMoneyCatTable::getList($parameters)->fetch();
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
			if($data["OUTGO"] == "report" && !empty($_FILES["ITEM_ID"]))
			{
				$ITEM_FILE = $_FILES["ITEM_ID"];
				$ITEM_FILE["old_file"] = $data["ITEM_ID"];
				//$ITEM_FILE["del"] = $data["ITEM_ID_del"];
				$arIMAGE["MODULE_ID"] = "yadadya.shopmate";
				if (strlen($ITEM_FILE["name"])>0 || strlen($ITEM_FILE["del"])>0) 
					$data["ITEM_ID"] = \CFile::SaveFile($ITEM_FILE, "finance_money_report");
			}

			if($primary > 0)
			{
				$res = Shopmate\Internals\FinanceMoneyCatTable::update($primary, $data);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
			}
			else
			{
				$res = Shopmate\Internals\FinanceMoneyCatTable::add($data);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
				else
					$primary = $res->GetID();
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
		
		$result = Shopmate\Internals\FinanceMoneyCatTable::delete($primary);

		return $result;
	}
}