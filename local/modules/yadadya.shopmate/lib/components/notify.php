<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\Internals;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;

Loc::loadMessages(__FILE__);

//http://wrapbootstrap.com/preview/WB0R5L90S

class Notify extends Base
{
	protected static $currentFields = array("ID", "DATE", "USER_FORMATED", "EVENT_TYPE", "ITEM_OBJECT", "ITEM_ID", "URL", "DESCRIPTION");
	protected static $currentSort = array("DATE" => "DESC");
	protected static $filterList = array();
	protected static $propList = array();

	public static function GetUserPermission()
	{
		return "R";
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"USER_FORMATED" => new Entity\ExpressionField("USER_FORMATED", 
				"(CONCAT(%s, \" (\", %s, \")\"))", 
				array("USER.WORK_COMPANY", "USER.NAME")
			),
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		global $USER;
		//$parameters["filter"]["!USER_ID"] = $USER->getId();
		$parameters["filter"]["STORE_TO"] = array(Shopmate\Shops::getUserShop(), false);
		$parameters["filter"]["USER_TO"] = array($USER->getId(), false);
		$parameters["filter"]["<=DATE"] = new \Bitrix\Main\Type\DateTime();

		return Internals\NotifyTable::getList($parameters);
	}

	public static function getOrder(array $order = array())
	{
		return static::$currentSort;
	}

	public function getByID($primary = 0)
	{
		return self::getList(array("filter" => array("ID" => $primary)))->fetch();
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
			$data["ID"] = $primary;
			$result = parent::update($primary, $data);
		}

		if($result->isSuccess())
		{
			$tobj = new Internals\NotifyTable;
			print_p($data);
			die();

			if($primary > 0)
				$res = $tobj->Update($primary, $data);
			else
				$res = $tobj->Add($data);

			if(!$res->isSuccess())
				$result->addErrors($res->getErrors());
			else
				$primary = $res->getId();

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}

		return $result;
	}

	public function delete($primary)
	{
		return Internals\NotifyTable::delete($primary);
	}

	public function getNewCnt()
	{
		global $USER;
		$cnt = 0;
		$res = self::getList(array(
			"select" => array("CNT"), 
			"filter" => array(
				">=DATE" => new \Bitrix\Main\Type\DateTime($USER->GetParam("PREV_AUTH"), "Y-m-d H:i:s"),
			), 
			"runtime" => array(new Entity\ExpressionField('CNT', 'COUNT(*)'))));
		if ($row = $res->fetch()) 
			$cnt = $row["CNT"];
		return $cnt;
	}
}