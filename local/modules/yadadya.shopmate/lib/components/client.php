<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class Client extends Base
{
	protected static $currentFields = array("ID", "CLIENT", "DISCOUNTS", "WORK_PHONE", "EMAIL", "WORK_STREET", "WORK_NOTES", "CORPORATE_FORMATED");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array(
		"SEARCH" => array(),
		"CORPORATE" => array(
			"PROPERTY_TYPE" => "L",
			"MULTIPLE" => "N",
			"LIST_TYPE" => "C",
			"USER_TYPE" => "checkbox",
			"ENUM" => array("Y" => ""),
		),
	);
	protected static $propList = array(
		"PERSON_TYPE" => array(
			"PROPERTY_TYPE" => "L",
			"REQUIRED" => "Y",
			"ENUM" => array(
				1 => "fiz",
				2 => "yur",
			),
			"DEFAULT_VALUE" => 1
		),
		"INN" => array(
			"UNIQUE" => "Y"
		),
		"CORPORATE" => array(
			"PROPERTY_TYPE" => "L",
			"MULTIPLE" => "N",
			"LIST_TYPE" => "C",
			"USER_TYPE" => "checkbox",
			"ENUM" => array("Y" => ""),
		),
		"WORK_COMPANY" => array(),
		"NAME" => array(),
		"BIK" => array(),
		"OGRN" => array(),
		"WORK_PHONE" => array(
			"REQUIRED" => "Y",
			"UNIQUE" => "Y",
			"VERIFICATION" => "phone"		
		),
		"EMAIL" => array(
			//"REQUIRED" => "Y",
			"UNIQUE" => "Y",
			"VERIFICATION" => "email"
		),
		"REGULAR" => array(
			"PROPERTY_TYPE" => "L",
			"REQUIRED" => "Y",
			"ENUM" => array(
				1 => "yes",
				0 => "no",
			)
		),
		"CONTRACT" => array(),
		"CONTRACT_DATE" => array(
			"USER_TYPE" => "Date",
		),
		"DELAY" => array(),
		"WORK_STREET" => array(),
		"WORK_NOTES" => array(
			"PROPERTY_TYPE" => "T",
		),
		"GROUP_ID_DISCOUNT" => array(
			"PROPERTY_TYPE" => "L",
			//"MULTIPLE" => "Y",
			"ENUM" => array()
		),
		"GROUP_ID_PRICELIST" => array(
			"PROPERTY_TYPE" => "L",
			//"MULTIPLE" => "Y",
			"ENUM" => array()
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["PERSON_TYPE"]["ENUM"] = array(
			1 => array("VALUE" => Loc::getMessage("PERSON_TYPE_FIZ")),
			2 => array("VALUE" => Loc::getMessage("PERSON_TYPE_YUR")),
		);

		$propList["REGULAR"]["ENUM"] = array(
			1 => array("VALUE" => Loc::getMessage("YES")),
			0 => array("VALUE" => Loc::getMessage("NO")),
		);

		$arGroupsDiscount = Shopmate\Discount::getGroups(true);
		foreach($arGroupsDiscount as $group_id => $group_name)
			$propList["GROUP_ID_DISCOUNT"]["ENUM"][$group_id] = array("VALUE" => $group_name);

		$res = Pricelist::getList(array(
			"select" => array("ID", "NAME_FORMATED", "GROUP_ID" => "GROUP.ID"),
			"runtime" => array(
				"GROUP" => new Entity\ReferenceField(
					'GROUP',
					'Yadadya\Shopmate\BitrixInternals\Group',
					array('=ref.STRING_ID' => new \Bitrix\Main\DB\SqlExpression("CONCAT(?s, ?#)", "PRICELIST_ID_", strtolower(\Yadadya\Shopmate\BitrixInternals\CatGroupTable::getEntity()->getCode()).".ID")),
					array('join_type' => 'LEFT')
				),
			),
			"order" => array("ID" => "ASC")
		));
		while ($row = $res->fetch())
			$propList["GROUP_ID_PRICELIST"]["ENUM"][$row["GROUP_ID"]] = array("VALUE" => $row["NAME_FORMATED"]);

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		$user_perm = parent::GetUserPermission("clients");
		if ($user_perm >= "X")
			$user_perm = "W";
		return $user_perm;
		//return parent::GetUserPermission("clients");
	}*/

	public function resultModifier($arResult)
	{
		if (is_array($arResult["ITEMS"]))
		{
			$groups = array();
			$discount_groups = Shopmate\Discount::getGroups(true);

			foreach ($arResult["ITEMS"] as $keyItem => $arItem) 
			{
				$discounts = array();
				$itemGroups = explode(",", $arItem["DISCOUNTS"]);
				foreach ($itemGroups as $group) 
					if (!in_array($group, $groups) && array_key_exists($group, $discount_groups))
						$discounts[] = $discount_groups[$group];
				$arResult["ITEMS"][$keyItem]["DISCOUNTS"] = implode(", ", $discounts);
			}
		}
		return $arResult;
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		foreach($filter as $field => $value) 
			if(!empty($value))
			{
				switch($field) 
				{
					case "CORPORATE":

						$arFilter["SMUSER.CORPORATE"] = $value;

						break;


					case "SEARCH":

						if(is_array($value)) $value = array_shift($value);

						$arFilter["SEARCH"] = parent::getSearchComboFilter($value);

						break;


					default:

						$arFilter[$field] = $value;

						break;
				}
		}
		
		return $arFilter;
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"CLIENT" => new Entity\ExpressionField("CLIENT", 
				"(CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE %s 
				END)", 
				array("SMUSER.PERSON_TYPE", "WORK_COMPANY", "NAME", "NAME")
			),
			"CLIENT_ENUM" => new Entity\ExpressionField("CLIENT_ENUM", 
				"CONCAT((CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE %s 
				END), \" [\", %s, \"]\")", 
				array("SMUSER.PERSON_TYPE", "WORK_COMPANY", "NAME", "NAME", "EMAIL")
			),
			"PERSON_TYPE" => "SMUSER.PERSON_TYPE",
			"CORPORATE" => "SMUSER.CORPORATE",
			"CORPORATE_FORMATED" => new Entity\ExpressionField("CORPORATE_FORMATED", 
				"(CASE %s 
					WHEN \"Y\" 
						THEN \"".Loc::getMessage("YES")."\"
					ELSE \"".Loc::getMessage("NO")."\"
				END)", 
				array("SMUSER.CORPORATE")
			),
			"INN" => "SMUSER.INN",
			"BIK" => "SMUSER.BIK",
			"OGRN" => "SMUSER.OGRN",
			"REGULAR" => "SMUSER.REGULAR",
			"CONTRACT" => "SMUSER.CONTRACT",
			"CONTRACT_DATE" => "SMUSER.CONTRACT_DATE",
			"DELAY" => "SMUSER.DELAY",
			/*"DISCOUNTS" => new Entity\ExpressionField("DISCOUNTS", "GROUP_CONCAT(DISTINCT %s SEPARATOR ', ')", array("SMUSER.USERGROUP.GROUP.NAME", "SMUSER.USERGROUP.GROUP.ID")),*/
			"DISCOUNTS" => new Entity\ExpressionField("DISCOUNTS", "GROUP_CONCAT(DISTINCT %s)", array("SMUSER.USERGROUP.GROUP_ID")),
			"GROUP_ID_DISCOUNT" => new Entity\ExpressionField("GROUP_ID_DISCOUNT", "GROUP_CONCAT(%s)", array("SMUSER.USERGROUP.GROUP_ID")),
			"GROUP_ID_PRICELIST" => new Entity\ExpressionField("GROUP_ID_PRICELIST", "GROUP_CONCAT(%s)", array("SMUSER.USERGROUP.GROUP_ID")),
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		if (isset($parameters["filter"]["SEARCH"]))
		{
			$searchFields = array("WORK_COMPANY", "NAME", "SMUSER.BIK", "SMUSER.OGRN", "WORK_PHONE", "EMAIL", "SMUSER.CONTRACT", "WORK_STREET", "WORK_NOTES");
			$concatFields = "CONCAT(IFNULL(%s, \"\")".str_repeat(", \" \", IFNULL(%s, \"\")", count($searchFields) - 1).")";
			$parameters["runtime"]["SEARCH"] = new Entity\ExpressionField("SEARCH", 
				$concatFields,
				$searchFields
			);
		}

		/*$parameters["filter"]["SMUSER.USERGROUP.GROUP_ID"] = array_merge((array) Shopmate\Shops::getUserGroup(), Shopmate\Rights::GetGlobalGroups("clients"));*/
		$q = new Entity\Query(\Bitrix\Main\UserGroupTable::getEntity());
		$q->setSelect(array("USER_ID"));
		$q->setFilter(array("GROUP_ID" => Shopmate\Shops::getUserGroup()));
		$parameters["filter"][]["@ID"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");
		$q = new Entity\Query(\Bitrix\Main\UserGroupTable::getEntity());
		$q->setSelect(array("USER_ID"));
		$q->setFilter(array("GROUP_ID" => Shopmate\Rights::GetGlobalGroups("clients")));
		$parameters["filter"][]["@ID"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");

		if ($_REQUEST["FILTER"] == "Y") {
			foreach (self::$currentFields as $curField) {
				if($curField == $_REQUEST["FIELD"]) $allow = true;
			}
			if($allow) {
				$parameters["filter"][][$_REQUEST["FIELD"]] = "%".$_REQUEST["REQ"]."%";
			}
		}

		$parameters["count_total"] = false;

		return Shopmate\BitrixInternals\UserTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$select = array_keys(static::$propList);
		$parameters = array("filter" => array("ID" => $primary), "select" => $select);
		$item = self::getList($parameters)->fetch();
		$propList = self::getPropList();
		$item["GROUP_ID_DISCOUNT"] = array_intersect(array_keys($propList["GROUP_ID_DISCOUNT"]["ENUM"]), explode(",", $item["GROUP_ID_DISCOUNT"]));
		$item["GROUP_ID_PRICELIST"] = array_intersect(array_keys($propList["GROUP_ID_PRICELIST"]["ENUM"]), explode(",", $item["GROUP_ID_PRICELIST"]));
		return $item;
	}

	public static function checkErrors(array $data, array $propList = array(), &$errors, $ignoreRequired = false)
	{
		$errors = (array) $errors;
		$errors = array_merge((array) $errors, parent::checkErrors($data, $propList, $errors, $ignoreRequired));

		$filter = array();
		foreach ($propList as $prop => $arProp)
			if($arProp["UNIQUE"] == "Y" && !empty($data[$prop]))
				$filter[$prop] = $data[$prop];

		if (!empty($filter))
		{
			$res = self::getList(
				array(
					"select" => array_merge(array("ID", "CLIENT"), array_keys($filter)), 
					"filter" => array("!ID" => $data["ID"], array_merge(array("LOGIC" => "OR"), $filter)), 
					"group" => array("ID"))
			);
			while ($row = $res->fetch())
			{
				$prop_name = "";
				foreach ($filter as $prop => $pval) {
					if($data[$prop] == $row[$prop]){
						$prop_name .= (!empty($prop_name) ? ", " : "") . Loc::getMessage($prop."_TITLE");

						$client_link = "<a target=\"_blank\" href=\"/clients/edit=Y&CODE=".$row["ID"]."\">".$row["CLIENT"]."</a>";

						$errors[] = new Entity\EntityError(Loc::getMessage("UNICUE_ERROR", array("#PROP_NAME#" => $prop_name, "#CLIENT#" => $client_link)));
					}
				}
			}
		}

		return $errors;
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
		
		$data["ID"] = $primary;
		$errors = self::checkErrors($data, static::$propList);
		unset($data["ID"]);
		if(!empty($errors)) $result->addErrors($errors);

		if($result->isSuccess())
		{
			$tableFields = array(
				"USER" => array("WORK_COMPANY", "NAME", "WORK_PHONE", "EMAIL", "WORK_STREET", "WORK_NOTES"),
				"SMUSER" => array("PERSON_TYPE", "CORPORATE", "INN", "BIK", "OGRN", "REGULAR", "CONTRACT", "CONTRACT_DATE", "DELAY"),
				"GROUPS" => array("GROUP_ID_DISCOUNT", "GROUP_ID_PRICELIST")
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

							if(empty($tData["LOGIN"])) $tData["LOGIN"] = $tData["EMAIL"];
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
							$tData["CONTRACT_DATE"] = new \Bitrix\Main\Type\DateTime($tData["CONTRACT_DATE"]);
							if (empty($tData["CORPORATE"]))
								$tData["CORPORATE"] = "N";
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

							$shop_group_id = (array) Shopmate\Shops::getUserGroup();
							$client_group_id = Shopmate\Rights::GetGlobalGroups("clients");
							$discount_group_id = (array) $tData["GROUP_ID_DISCOUNT"];
							$pricelist_group_id = (array) $tData["GROUP_ID_PRICELIST"];

							$add_groups = array_merge($shop_group_id, $client_group_id, $discount_group_id, $pricelist_group_id);
							$remove_groups = Shopmate\Discount::getGroups();
							$current_groups = \CUser::GetUserGroup($primary);

							foreach ($remove_groups as $gkey => $gid)
								if(in_array($gid, $add_groups))
									unset($remove_groups[$gkey]);

							foreach ($current_groups as $gkey => $gid) 
							{
								if (in_array($gid, $remove_groups))
									unset($current_groups[$gkey]);
								if ($addkey = array_search($gid, $add_groups))
									unset($add_groups[$addkey]);
							}

							\CUser::SetUserGroup($primary, array_merge($current_groups, $add_groups));

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
		
		//$result = Shopmate\Internals\FinanceMoneyCatTable::delete($primary);

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
				"LOGIC" => "OR",
				"LOGIN" => "%".$q."%",
				"EMAIL" => "%".$q."%",
				"NAME" => "%".$q."%",
				"SECOND_NAME" => "%".$q."%",
				"LAST_NAME" => "%".$q."%",
				"WORK_PHONE" => "%".$q."%",
			);
		}

		$filter["SMUSER.CORPORATE"] = "Y";

		$result = self::getList(array(
			"select" => array("ID", "CLIENT_ENUM"),
			"filter" => $filter
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["CLIENT_ENUM"];
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}