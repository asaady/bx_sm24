<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\BitrixInternals;

Loc::loadMessages(__FILE__);

class Store extends Base
{
	protected static $currentFields = array("ID");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array();

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("store");
	}

	public function getList(array $parameters = array())
	{
		return BitrixInternals\StoreTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$select = array_keys(static::$propList);
		$parameters = array("filter" => array("ID" => $primary), "select" => $select);
		$item = self::getList($parameters)->fetch();
		return $item;
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();
		if(!empty($filter["SEARCH"]))
		{
			$q = $filter["SEARCH"];
			unset($filter["SEARCH"]);
			$filter[] = array(
				"LOGIC" => "OR",
				"TITLE" => "%".$q."%",
				"ADDRESS" => "%".$q."%",
				"DESCRIPTION" => "%".$q."%",
				"XML_ID" => "%".$q."%"
			);
		}
		$result = BitrixInternals\StoreTable::GetList(array(
			"select" => array("ID", "TITLE", "XML_ID", "ADDRESS"),
			"filter" => $filter
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["TITLE"]." (".$row["XML_ID"].", ".$row["ADDRESS"].")";
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}