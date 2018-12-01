<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class Register extends Base
{
	protected static $currentFields = array("ID");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"USER_TYPE" => array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "contractor"
		),
		"WORK_COMPANY" => array(),
		"INN" => array(
			"UNIQUE" => "Y"
		),
		"TAX_TYPE" => array(
			"PROPERTY_TYPE" => "L",
			"LIST_TYPE" => "C",
			"ENUM" => array(
				"nds" => "full (with NDS)",
				"usn" => "usn",
				"patent" => "patent",
			)
		),
		"NAME" => array(),
		"EMAIL" => array(
			"REQUIRED" => "Y",
			"UNIQUE" => "Y",
			"VERIFICATION" => "email"
		),
		"WORK_PHONE" => array(
			"REQUIRED" => "Y",
			"UNIQUE" => "Y",
			"VERIFICATION" => "phone"
		),
		"PASSWORD" => array(
			"REQUIRED" => "Y",
		),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["TAX_TYPE"]["ENUM"] = array(
			"nds" => array("VALUE" => Loc::getMessage("TAX_TYPE_NDS")),
			"usn" => array("VALUE" => Loc::getMessage("TAX_TYPE_USN")),
			"patent" => array("VALUE" => Loc::getMessage("TAX_TYPE_PATENT")),
		);

		return $propList;
	}

	public static function GetUserPermission()
	{
		return "W";
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEM"])) unset($arResult["PROPERTY_LIST"]["PASSWORD"]["REQUIRED"]);
		return $arResult;
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"USER" => new Entity\ExpressionField("USER", 
				"(CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE %s 
				END)", 
				array("SMUSER.PERSON_TYPE", "WORK_COMPANY", "NAME", "NAME")
			),
			"USER_ENUM" => new Entity\ExpressionField("USER_ENUM", 
				"CONCAT((CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE %s 
				END), \" [\", %s, \"]\")", 
				array("SMUSER.PERSON_TYPE", "WORK_COMPANY", "NAME", "NAME", "EMAIL")
			),
			"INN" => "SMUSER.INN",
			"TAX_TYPE" => "SMUSER.TAX_TYPE",
			"USER_TYPE" => "SMUSER.USER_TYPE",
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		/*$q = new Entity\Query(\Bitrix\Main\UserGroupTable::getEntity());
		$q->setSelect(array("USER_ID"));
		$q->setFilter(array("GROUP_ID" => Shopmate\Rights::GetGlobalGroups("contractors")));
		$parameters["filter"][]["@ID"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");*/

		return Shopmate\BitrixInternals\UserTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$select = array_keys(static::$propList);
		$parameters = array("filter" => array("ID" => $primary), "select" => $select);
		$item = self::getList($parameters)->fetch();
		unset($item["PASSWORD"]);
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
			$res = self::getList(array("select" => array_merge(array("ID", "USER"), array_keys($filter)), "filter" => array("!ID" => $data["ID"], array_merge(array("LOGIC" => "OR"), $filter)), "group" => array("ID")));
			while ($row = $res->fetch())
			{
				$prop_name = "";
				foreach ($filter as $prop => $pval)
					if($data[$prop] == $row[$prop])
						$prop_name .= (!empty($prop_name) ? ", " : "") . Loc::getMessage($prop."_TITLE");

				//$user_link = "<a target=\"_blank\" href=\"/users/edit=Y&CODE=".$row["ID"]."\">".$row["USER"]."</a>";
				$user_link = $row["USER"];

				$errors[] = new Entity\EntityError(Loc::getMessage("UNICUE_ERROR", array("#PROP_NAME#" => $prop_name, "#USER#" => $user_link)));
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

		if($result->isSuccess())
		{
			$tableFields = array(
				"USER" => array("WORK_COMPANY", "NAME", "WORK_PHONE", "EMAIL", "PASSWORD"),
				"SMUSER" => array("INN", "TAX_TYPE", "USER_TYPE"),
			);
			foreach ($tableFields as $table => $fields) 
			{
				$tData = array();
				foreach ($fields as $field) 
					if(isset($data[$field]))
						$tData[$field] = $data[$field];
				if(!empty($tData))
					switch ($table) 
					{
						case "USER":

							$tobj = new \CUser;

							if (empty($tData["LOGIN"]) && empty($primary)) $tData["LOGIN"] = $tData["EMAIL"];
							if (empty($tData["ACTIVE"]) && empty($primary)) $tData["ACTIVE"] = "N";
							if (empty($tData["PASSWORD"])) unset($tData["PASSWORD"]);
							if (empty($tData["GROUP_ID"]) && empty($primary)) $tData["GROUP_ID"] = Shopmate\Rights::GetGlobalGroups("contractors");
							if ($primary > 0)
							{
								$res = $tobj->Update($primary, $tData);
								if(!$res) 
									$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
							}
							else
							{
								if (empty($tData["PASSWORD"])) $tData["PASSWORD"] = randString(8);
								$primary = $tobj->Add($tData);
								if(!$primary)
									$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
							}

							break;

						case "SMUSER":

							$tobj = new Shopmate\Internals\UserTable;
							$res = $tobj->getList(array("select" => array("ID"), "filter" => array("USER_ID" => $primary)));
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
					}
			}

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}

		return $result;
	}

	public static function redirectAfterSave($to_list = true, \Bitrix\Main\Result $result = null)
	{
		global $APPLICATION;
		$sRedirectUrl = $APPLICATION->GetCurPageParam("", array("edit", "CODE", "successMessage"), $get_index_page=false);

		$sAction = ($result instanceof \Bitrix\Main\Entity\AddResult) ? "ADD" : "UPDATE";
		$sRedirectUrl .= (strpos($sRedirectUrl, "?") === false ? "?" : "&") . "successMessage=" . $sAction;

		LocalRedirect($sRedirectUrl);
		exit();
	}

	public function onPrepareParams($arParams)
	{
		global $USER;
		if ($USER->IsAuthorized()) $arParams["CODE"] = $USER->getId();
		return parent::onPrepareParams($arParams);
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

		$result = self::getList(array(
			"select" => array("ID", "USER_ENUM"),
			"filter" => $filter
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["USER_ENUM"];
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}