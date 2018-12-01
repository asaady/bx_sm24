<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

IncludeModuleLangFile(__FILE__);

class SMTodo
{
	function array_diff_assoc($array1, $array2)
	{
		$array1 = (array) $array1;
		$array2 = (array) $array2;
		$result = array_diff_assoc($array1, $array2);
		foreach($array1 as $key1 => $val1)
		{
			if(is_array($val1))
			{
				if(!empty($array2[$key1]))
				{
					$val3 = self::array_diff_assoc($val1, $array2[$key1]);
					if(!empty($val3))
						$result[$key1] = $val3;
				}
				else
				{
					if(!empty($val1))
						$result[$key1] = $val1;
				}
			}
		}
		return $result;
	}

	function array_del_assoc($array1, $array2)
	{
		$array1 = (array) $array1;
		$array2 = (array) $array2;
		$result = array();
		foreach($array1 as $key1 => $val1)
		{
			if(is_array($val1))
			{
				if(empty($array2[$key1]))
				{
					if(!empty($val1))
						$result[$key1] = $val1;
				}
			}
		}
		return $result;
	}

	function prepChanges($array1, $array2)
	{
		$result["CHANGES"] = self::array_diff_assoc($array1, $array2);
		$result["DELETES"] = self::array_del_assoc($array2, $array1);
		$result["PREVIOUS"] = $array2;

		return $result;
	}

	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		CModule::IncludeModule("catalog");
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "TIMESTAMP_X", "STORE_ID", "TYPE", "SECTION_ID", "ITEM_ID", "REMOTE_ADDR", "USER_AGENT", "REQUEST_URI", "SITE_ID", "USER_ID", "GUEST_ID", "DESCRIPTION", "PRICE", "CURRENCY", "CREDIT", "ACTIVE");

		if(is_array($arOrder) && is_array($arGroupBy))
			foreach ($arOrder as $sort => $by)
				if(!in_array($sort, $arGroupBy))
					unset($arOrder[$sort]);

		$arFields = array(
			"ID" => array("FIELD" => "ID", "TYPE" => "int"),
			"TIMESTAMP_X" => array("FIELD" => "TIMESTAMP_X", "TYPE" => "int"),
			"SECTION_ID" => array("FIELD" => "SECTION_ID", "TYPE" => "string"),
			"STORE_ID" => array("FIELD" => "STORE_ID", "TYPE" => "int"),
			"TYPE" => array("FIELD" => "\"TODO\"", "TYPE" => "string"),
			"ITEM_ID" => array("FIELD" => "ITEM_ID", "TYPE" => "int"),
			"SITE_ID" => array("FIELD" => "SITE_ID", "TYPE" => "string"),
			"REMOTE_ADDR" => array("FIELD" => "REMOTE_ADDR", "TYPE" => "string"),
			"USER_AGENT" => array("FIELD" => "USER_AGENT", "TYPE" => "string"),
			"REQUEST_URI" => array("FIELD" => "REQUEST_URI", "TYPE" => "string"),
			"USER_ID" => array("FIELD" => "USER_ID", "TYPE" => "int"),
			"GUEST_ID" => array("FIELD" => "GUEST_ID", "TYPE" => "int"),
			"DESCRIPTION" => array("FIELD" => "DESCRIPTION", "TYPE" => "string"),
			"PRICE" => array("FIELD" => "\"0\"", "TYPE" => "double"),
			"CURRENCY" => array("FIELD" => "\"\"", "TYPE" => "string"),
			"CREDIT" => array("FIELD" => "\"\"", "TYPE" => "string"),
			"ACTIVE" => array("FIELD" => "ACTIVE", "TYPE" => "string"),
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (class_exists('SMFinanceLog')) 
		{
			$arFields = array(
				"ID" => array("FIELD" => "ID", "TYPE" => "int"),
				"TIMESTAMP_X" => array("FIELD" => "TIMESTAMP_X", "TYPE" => "int"),
				"SECTION_ID" => array("FIELD" => "TRANSACTION", "TYPE" => "string"),
				"STORE_ID" => array("FIELD" => "STORE_ID", "TYPE" => "int"),
				"TYPE" => array("FIELD" => "TYPE", "TYPE" => "string"),
				"ITEM_ID" => array("FIELD" => "ITEM_ID", "TYPE" => "int"),
				"SITE_ID" => array("FIELD" => "SITE_ID", "TYPE" => "string"),
				"REMOTE_ADDR" => array("FIELD" => "REMOTE_ADDR", "TYPE" => "string"),
				"USER_AGENT" => array("FIELD" => "USER_AGENT", "TYPE" => "string"),
				"REQUEST_URI" => array("FIELD" => "REQUEST_URI", "TYPE" => "string"),
				"USER_ID" => array("FIELD" => "USER_ID", "TYPE" => "int"),
				"GUEST_ID" => array("FIELD" => "GUEST_ID", "TYPE" => "int"),
				"DESCRIPTION" => array("FIELD" => "DESCRIPTION", "TYPE" => "string"),
				"PRICE" => array("FIELD" => "PRICE", "TYPE" => "double"),
				"CURRENCY" => array("FIELD" => "CURRENCY", "TYPE" => "string"),
				"CREDIT" => array("FIELD" => "CREDIT", "TYPE" => "string"),
				"ACTIVE" => array("FIELD" => "ACTIVE", "TYPE" => "string"),
			);
			$arFSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
			$arFSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arFSqls["SELECT"]);
		}
		
		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_todo_log ".$arSqls["FROM"];
		if (!empty($arSqls["WHERE"]))
				$strSql .= " WHERE ".$arSqls["WHERE"];
		if(!empty($arFSqls))
		{
			$strFSql = "SELECT ".$arFSqls["SELECT"]." FROM b_sm_finance_log ".$arFSqls["FROM"];
			if (!empty($arFSqls["WHERE"]))
				$strFSql .= " WHERE ".$arFSqls["WHERE"];
			$strSql = "SELECT * FROM ((".$strSql.") UNION ALL (".$strFSql.")) TF";
			if (!empty($arFSqls["GROUPBY"]))
				$strSql .= " GROUP BY ".$arFSqls["GROUPBY"];
			if (!empty($arFSqls["ORDERBY"]))
				$strSql .= " ORDER BY ".$arFSqls["ORDERBY"];
		}
		else
		{
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= " GROUP BY ".$arSqls["GROUPBY"];
			if (!empty($arSqls["ORDERBY"]))
				$strSql .= " ORDER BY ".$arSqls["ORDERBY"];
		}

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$cnt = 0;
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while ($arRes = $dbRes->Fetch())
				$cnt += $arRes["CNT"];
			return $cnt > 0 ? $cnt : false;
		}

		$intTopCount = 0;
		$boolNavStartParams = (!empty($arNavStartParams) && is_array($arNavStartParams));
		if ($boolNavStartParams && array_key_exists('nTopCount', $arNavStartParams))
		{
			$intTopCount = intval($arNavStartParams["nTopCount"]);
		}
		if ($boolNavStartParams && 0 >= $intTopCount)
		{
			$strSql_tmp = "SELECT ".(!empty($arSqls["GROUPBY"]) ? $arSqls["SELECT"] : "COUNT('x') as CNT")." FROM b_sm_todo_log ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
					$strSql_tmp .= " WHERE ".$arSqls["WHERE"];
			if(!empty($arFSqls))
			{
				$strFSql_tmp = "SELECT ".(!empty($arFSqls["GROUPBY"]) ? $arFSqls["SELECT"] : "COUNT('x') as CNT")." FROM b_sm_finance_log ".$arFSqls["FROM"];
				if (!empty($arFSqls["WHERE"]))
					$strFSql_tmp .= " WHERE ".$arFSqls["WHERE"];
				$strSql_tmp = "SELECT * FROM ((".$strSql_tmp.") UNION ALL (".$strFSql_tmp.")) TF";
				if (!empty($arFSqls["GROUPBY"]))
					$strSql_tmp .= " GROUP BY ".$arFSqls["GROUPBY"];
			}
			else
			{
				if (!empty($arSqls["GROUPBY"]))
					$strSql_tmp .= " GROUP BY ".$arSqls["GROUPBY"];
			}

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				while ($arRes = $dbRes->Fetch())
					$cnt += $arRes["CNT"];
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