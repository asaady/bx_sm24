<?
IncludeModuleLangFile(__FILE__);

class SMUser
	extends CUser
{
	public static $simple_user_login = "simple_buyer";

	function SimpleBuyerAdd($login = "")
	{
		global $USER;

		if(empty($login))
			$login = self::$simple_user_login;

		if($arUser = CUser::GetList(($by="id"), ($order="asc"), array("LOGIN" => $login), array("SELECT" => array("ID")))->Fetch())
			return $arUser["ID"];

		$arFields = array(
			"LOGIN" => $login,
			"EMAIL" => $login."@email.buy",
			"ACTIVE" => "Y",
			"SITE_ID" => SITE_ID
		);

		$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
		if($def_group!="")
		{
			$arFields["GROUP_ID"] = explode(",", $def_group);
			$arPolicy = $USER->GetGroupPolicy($arFields["GROUP_ID"]);
		}
		else
		{
			$arPolicy = $USER->GetGroupPolicy(array());
		}
		$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
		if($password_min_length <= 0)
			$password_min_length = 6;
		$password_chars = array(
			"abcdefghijklnmopqrstuvwxyz",
			"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			"0123456789",
		);
		if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
			$password_chars[] = ",.<>/?;:'\"[]{}\\|`~!@#\$%^&*()-_+=";
		$arFields["PASSWORD"] = $arFields["CONFIRM_PASSWORD"] = randString($password_min_length, $password_chars);

		$arFields["LID"] = $arFields["SITE_ID"];
		return $USER->Add($arFields);
	}

	function CustomBuyerClose(&$param)
	{
		global $APPLICATION;
		$id = is_array($param) ? $param["ID"] : $param;
		
		if($id > 0)
		{
			$arUser = CUser::GetByID($id)->Fetch();
			if($arUser["LOGIN"] == self::$simple_user_login)
			{
				if(is_array($param))
					$param["LOGIN"] = self::$simple_user_login;
				else
				{
					$APPLICATION->throwException("Пользователь является системным.");
					return false;
				}
			}
		}
	}

	/** Add new store in table b_sm_user,
	* @static
	* @param $arFields
	* @return bool|int
	*/
	function add($arFields)
	{
		if(empty($arFields["LOGIN"])) $arFields["LOGIN"] = $arFields["EMAIL"];
		if(empty($arFields["PASSWORD"])) $arFields["PASSWORD"] = randString(8);
		if($arFields["USER_ID"] = parent::add($arFields))
		{
			global $DB;

			$arInsert = $DB->PrepareInsert("b_sm_user", $arFields);

			$strSql = "INSERT INTO b_sm_user (".$arInsert[0].") VALUES(".$arInsert[1].")";

			$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
			if(!$res)
				return false;
			$lastId = intval($arFields["USER_ID"]);
			return $lastId;
		}
		return false;
	}

	function update($id, $arFields)
	{
		if(parent::update($id, $arFields))
		{
			global $DB;
			$id = intval($id);

			if($id <= 0) return false;
			/*if(is_array($arFields) && !empty($arFields))
			{
				$cFields = array();
				$tableFields = array_keys($DB->GetTableFields("b_sm_user"));
				foreach ($arFields as $key => $value)
					if(in_array($key, $tableFields))
						$cFields[$key] = "\"".$DB->ForSql($value)."\"";

				$rows = $DB->Update("b_sm_user", $cFields, "WHERE USER_ID='".$id."'", "File: ".__FILE__."<br>Line: ".__LINE__);
				
				if($rows <= 0)
				{
					$cFields["USER_ID"] = $id;
					$DB->Insert("b_sm_user", $cFields, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}*/
			$rows = 0;
			$strUpdate = $DB->PrepareUpdate("b_sm_user", $arFields);

			if(!empty($strUpdate))
			{
				$strSql = "UPDATE b_sm_user SET ".$strUpdate." WHERE USER_ID = ".$id." ";
				$w = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if (is_object($w))
				{
					$rows = $w->AffectedRowsCount();

					if ($rows <= 0 && $additional_check)
					{
						$w = $this->Query("SELECT 'x' FROM ".$table." ".$WHERE, $ignore_errors, $error_position);
						if (is_object($w))
							if ($w->Fetch())
								$rows = $w->SelectedRowsCount();
					}
				}
				if($rows <= 0)
				{
					$cFields["USER_ID"] = $id;
					$DB->Insert("b_sm_user", $cFields, "File: ".__FILE__."<br>Line: ".__LINE__);
				}
			}
			return $id;
		}
		return false;
	}

	public static function delete($id)
	{
		if(parent::delete($id))
		{
			global $DB;
			$id = intval($id);
			if($id > 0)
			{
				$dbOrder = CSaleOrder::getList(array(), array("USER_ID" => $id));
				if($arOrder = $dbOrder->Fetch())
				{
					$GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage("CC_CONTRACTOR_HAVE_DOCS"));
					return false;
				}

				return $DB->Query("DELETE FROM b_sm_user WHERE USER_ID = ".$id." ", true);
			}
			return false;
		}
	}

	public static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "USER_ID", "PERSON_TYPE", "INN", "BIK", "OGRN", "REGULAR", "CONTRACT", "CONTRACT_DATE", "DELAY", "NOTES"/*, "DEPARTMENT"*/, "START_DATE", "SALARY", "RATE", "RATE_SECTIONS");

		$arFields = array(
			"ID" => array("FIELD" => "CC.ID", "TYPE" => "int"),
			"USER_ID" => array("FIELD" => "CC.USER_ID", "TYPE" => "int"),
			"PERSON_TYPE" => array("FIELD" => "CC.PERSON_TYPE", "TYPE" => "char"),
			"INN" => array("FIELD" => "CC.INN", "TYPE" => "string"),
			"BIK" => array("FIELD" => "CC.BIK", "TYPE" => "string"),
			"OGRN" => array("FIELD" => "CC.OGRN", "TYPE" => "string"),
			"REGULAR" => array("FIELD" => "CC.REGULAR", "TYPE" => "char"),
			"CONTRACT" => array("FIELD" => "CC.CONTRACT", "TYPE" => "string"),
			"CONTRACT_DATE" => array("FIELD" => "CC.CONTRACT_DATE", "TYPE" => "datetime"),
			"DELAY" => array("FIELD" => "CC.DELAY", "TYPE" => "string"),
			"NOTES" => array("FIELD" => "CC.NOTES", "TYPE" => "string"),
			//"DEPARTMENT" => array("FIELD" => "CC.DEPARTMENT", "TYPE" => "string"),
			"START_DATE" => array("FIELD" => "CC.START_DATE", "TYPE" => "datetime"),
			"SALARY" => array("FIELD" => "CC.SALARY", "TYPE" => "double"),
			"RATE" => array("FIELD" => "CC.RATE", "TYPE" => "double"),
			"RATE_SECTIONS" => array("FIELD" => "CC.RATE_SECTIONS", "TYPE" => "string"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_user CC ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_user CC ".$arSqls["FROM"];
		if (!empty($arSqls["WHERE"]))
			$strSql .= " WHERE ".$arSqls["WHERE"];
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= " GROUP BY ".$arSqls["GROUPBY"];
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= " ORDER BY ".$arSqls["ORDERBY"];

		$intTopCount = 0;
		$boolNavStartParams = (!empty($arNavStartParams) && is_array($arNavStartParams));
		if ($boolNavStartParams && array_key_exists('nTopCount', $arNavStartParams))
		{
			$intTopCount = intval($arNavStartParams["nTopCount"]);
		}
		if ($boolNavStartParams && 0 >= $intTopCount)
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_user CC ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if ($boolNavStartParams && 0 < $intTopCount)
			{
				$strSql .= " LIMIT ".$intTopCount;
			}
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}
}