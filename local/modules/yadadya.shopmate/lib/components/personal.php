<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\BitrixInternals;
use Yadadya\Shopmate\BitrixInternals\UserTable;
use Yadadya\Shopmate\BitrixInternals\UserGroupTable;

Loc::loadMessages(__FILE__);

class Personal extends Base
{
	protected static $currentFields = array("ID", "NAME", "DEPARTMENT_FORMATED", "POSITION_FORMATED", "RATE", "START_DATE", "SALARY");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"NAME" => array(
		),
		"STORE_ID" => array( //default current store
		),
		"DEPARTMENT_ID" => array(
		),
		"POSITION_ID" => array(
		),
		"PERSONAL_PHONE" => array(
			//"REQUIRED" => "Y",
			//"UNIQUE" => "Y",
			"VERIFICATION" => "phone"
		),
		"EMAIL" => array(
			//"REQUIRED" => "Y",
			"UNIQUE" => "Y",
			"VERIFICATION" => "email"
		),
		"START_DATE" => array(
			"USER_TYPE" => "Date",
		),
		"SALARY" => array(
			"VERIFICATION" => "float"
		),
		"RATE" => array(
			"VERIFICATION" => "float"
		),
		"RATE_SECTIONS" => array(
		),
		"PERSONAL_STREET" => array(
			"PROPERTY_TYPE" => "T",
		),
		"PERSONAL_NOTES" => array(
			"PROPERTY_TYPE" => "T",
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$storeEnum = array();
		$res = Shopmate\BitrixInternals\GroupTable::getList(array(
			"select" => array("ID", "NAME"),
			"filter" => array("STRING_ID" => Shopmate\Shops::$shop_prefix."%"),
		));
		while ($row = $res->fetch()) 
			$storeEnum[$row["ID"]] = array("VALUE" => $row["NAME"]);

		$propList["STORE_ID"] = Array(
			"PROPERTY_TYPE" => "L", 
			"MULTIPLE" => "Y", 
			"ENUM" => $storeEnum, 
			"DEFAULT_VALUE" => array(Shopmate\Shops::getUserStore()),
		);
		$propList["DEPARTMENT_ID"] = Array(
			"PROPERTY_TYPE" => "L", 
			"ENUM" => \Yadadya\Shopmate\Components\PersonalDepartment::getEnumList(), 
		);
		$propList["POSITION_ID"] = Array(
			"PROPERTY_TYPE" => "L", 
			"ENUM" => \Yadadya\Shopmate\Components\PersonalPosition::getEnumList(), 
		);

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("personal");
	}*/

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"DEPARTMENT_FORMATED" => "SMUSER.DEPARTMENT.NAME",
			"POSITION_FORMATED" => "SMUSER.POSITION.NAME",
			"RATE" => "SMUSER.RATE",
			"START_DATE" => "SMUSER.START_DATE",
			"SALARY" => "SMUSER.SALARY",
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);
		$parameters["filter"]["USERGROUP.GROUP_ID"] = array_merge(Shopmate\Rights::GetGlobalGroups("personal"), Shopmate\Rights::GetPersonalGroups());
		$parameters["runtime"][] = new Entity\ReferenceField(
			'USERGROUP',
			'Bitrix\Main\UserGroup',
			array('=ref.USER_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);

		$parameters["count_total"] = false;

		return UserTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$result = self::getList(array(
			"select" => array("ID", "NAME", "DEPARTMENT_ID" => "SMUSER.DEPARTMENT_ID", "POSITION_ID" => "SMUSER.POSITION_ID", "PERSONAL_PHONE", "EMAIL", "START_DATE" => "SMUSER.START_DATE", "SALARY" => "SMUSER.SALARY", "RATE" => "SMUSER.RATE", "RATE_SECTIONS" => "SMUSER.RATE_SECTIONS", "PERSONAL_STREET", "PERSONAL_NOTES"),
			"filter" => array("ID" => $primary)
		))->fetch();
		if ($primary > 0)
		{
			$result["STORE_ID"] = array();
			$obj = new Shopmate\BitrixInternals\UserGroupTable;
			$res = $obj->getList(array(
				"select" => array("STRING_ID" => "GROUP.STRING_ID", "GROUP_ID"),
				"filter" => array(
					"USER_ID" => $primary,
					"GROUP.STRING_ID" => Shopmate\Shops::$shop_prefix."%"
				),
			));
			while ($row = $res->fetch())
			{
				/*$store_id = substr($row["STRING_ID"], strlen(Shopmate\Shops::$shop_prefix));
				if (!empty($store_id) && !in_array($store_id, $result["STORE_ID"]))
					$result["STORE_ID"][] = $store_id;*/
				if (!empty($row["GROUP_ID"]) && !in_array($row["GROUP_ID"], $result["STORE_ID"]))
					$result["STORE_ID"][] = $row["GROUP_ID"];
			}
		}

		return $result;
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEM"]))
		{
			if (empty($arResult["ITEM"]["STORE_ID"]))
				$arResult["ITEM"]["STORE_ID"] = array(Shopmate\Shops::getUserGroup());
		}

		return $arResult;
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
			$tableFields = array(
				"USER" => array("NAME", "PERSONAL_PHONE", "EMAIL", "PERSONAL_STREET", "PERSONAL_NOTES"),
				"SMUSER" => array("DEPARTMENT_ID", "POSITION_ID", "START_DATE", "SALARY", "RATE", "RATE_SECTIONS"),
				"GROUPS" => array("STORE_ID")
			);
			foreach ($tableFields as $table => $fields) 
			{
				$tData = array();
				foreach ($fields as $field) 
					if(isset($data[$field]))
						$tData[$field] = $data[$field];
				//if(!empty($tData))
					switch ($table) 
					{
						case "USER":

							$tobj = new \CUser;

							if(empty($tData["LOGIN"])) 
								$tData["LOGIN"] = \Cutil::translit($tData["NAME"], "ru", array("replace_space"=>"_","replace_other"=>"_"));
							if (empty($tData["EMAIL"]))
								$tData["EMAIL"] = $tData["LOGIN"]."@tmp.ru";

							if($primary > 0)
							{
								$res = $tobj->Update($primary, $tData);
								if(!$res) 
									$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
							}
							else
							{
								if(empty($tData["PASSWORD"])) $tData["PASSWORD"] = randString(8);
								$primary = $tobj->Add($tData);
								if(!$primary){
									$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
									echo $tobj->LAST_ERROR; die;
								}
							}

							break;

						case "SMUSER":

							$tobj = new Shopmate\Internals\UserTable;
							$res = $tobj->getList(array("select" => array("ID"), "filter" => array("ID" => $primary)));
							$tData["START_DATE"] = new \Bitrix\Main\Type\DateTime($tData["START_DATE"]);
							if($row = $res->fetch())
								$res = $tobj->update($row["ID"], $tData);
							else
							{
								$tData["USER_ID"] = $primary;
								$res = $tobj->add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

							break;

						case "GROUPS":

							$shop_group_id = (array) $tData["STORE_ID"];
							$shop_group_id = array_diff($shop_group_id, array(''));
							$personal_group_id = Shopmate\Rights::GetGlobalGroups("personal");

							$add_groups = array_merge($shop_group_id, $personal_group_id);
							/*$current_groups = \CUser::GetUserGroup($primary);

							foreach ($current_groups as $gkey => $gid) 
							{
								if (in_array($gid, $remove_groups))
									unset($current_groups[$gkey]);
								if ($addkey = array_search($gid, $add_groups))
									unset($add_groups[$addkey]);
							}*/

							\CUser::SetUserGroup($primary, $add_groups);

							break;
					}
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

		$result = UserTable::delete($primary);

		return $result;
	}

	public static function getEnumByID(array $elementsID = array())
	{
		return self::getEnumList(array("ID" => $elementsID));
	}

	public static function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();
		
		if(!empty($filter["SEARCH"]))
		{
			$q = $filter["SEARCH"];
			unset($filter["SEARCH"]);
			$filter[] = array(
				"LOGIC" => "OR",
				"LOGIN" => "%".$q."%",
				"EMAIL" => "%".$q."%",
				"NAME" => "%".$q."%",
				"SECOND_NAME" => "%".$q."%",
				"LAST_NAME" => "%".$q."%",
				"PERSONAL_PHONE" => "%".$q."%",
			);
		}
		
		$filter["USERGROUP.GROUP_ID"] = array_merge(Shopmate\Rights::GetGlobalGroups("personal"), Shopmate\Rights::GetPersonalGroups());

		$result = UserTable::GetList(array(
			"select" => array("ID", "LOGIN", "EMAIL", "NAME", "SECOND_NAME", "LAST_NAME"),
			"filter" => $filter,
			"runtime" => array(
				new Entity\ReferenceField(
					'USERGROUP',
					'Bitrix\Main\UserGroup',
					array('=ref.USER_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				)
			)
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["NAME"]." [".$row["EMAIL"]."]";
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}