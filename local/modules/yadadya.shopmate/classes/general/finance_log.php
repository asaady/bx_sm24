<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

IncludeModuleLangFile(__FILE__);

class SMFinanceLog
{
	function Log($STORE_ID, $TYPE, $TRANSACTION, $ITEM_ID, $CREDIT = "N", $PRICE = 0, $CURRENCY = "RUB", $DESCRIPTION = false, $SITE_ID = false)
	{
		return self::Add(array(
			"STORE_ID" => $STORE_ID,
			"TYPE" => $TYPE,
			"TRANSACTION" => $TRANSACTION,
			"ITEM_ID" => $ITEM_ID,
			"CREDIT" => $CREDIT,
			"PRICE" => $PRICE,
			"CURRENCY" => $CURRENCY,
			"DESCRIPTION" => $DESCRIPTION,
			"SITE_ID" => $SITE_ID,
		));
	}

	function Add($arFields)
	{
		global $USER, $DB;
		static $arType = array(
			"IN",
			"OUT",
		);

		$url = preg_replace("/(&?sessid=[0-9a-z]+)/", "", $_SERVER["REQUEST_URI"]);
		$SITE_ID = defined("ADMIN_SECTION") && ADMIN_SECTION==true ? false : SITE_ID;

		$arFields = array(
			"STORE_ID" => intval($arFields["STORE_ID"]),
			"TYPE" => in_array($arFields["TYPE"], $arType)? $arFields["TYPE"]: "UNKNOWN",
			"TRANSACTION" => strlen($arFields["TRANSACTION"]) <= 0? "UNKNOWN": $arFields["TRANSACTION"],
			"ITEM_ID" => strlen($arFields["ITEM_ID"]) <= 0? "UNKNOWN": $arFields["ITEM_ID"],
			"CREDIT" => $arFields["CREDIT"] == "Y" ? "Y" : "N",
			"PRICE" => floatval($arFields["PRICE"]),
			"CURRENCY" => $arFields["CURRENCY"],
			"ACTIVE" => "Y",
			"REMOTE_ADDR" => $_SERVER["REMOTE_ADDR"],
			"USER_AGENT" => $_SERVER["HTTP_USER_AGENT"],
			"REQUEST_URI" => $url,
			"SITE_ID" => strlen($arFields["SITE_ID"]) <= 0 ? $SITE_ID : $arFields["SITE_ID"],
			"USER_ID" => is_object($USER) && ($USER->GetID() > 0)? $USER->GetID(): false,
			"GUEST_ID" => (isset($_SESSION) && array_key_exists("SESS_GUEST_ID", $_SESSION) && $_SESSION["SESS_GUEST_ID"] > 0? $_SESSION["SESS_GUEST_ID"]: false),
			"DESCRIPTION" => $arFields["DESCRIPTION"],
		);

		$oldFilter = array(
			"STORE_ID" => $arFields["STORE_ID"],
			"TYPE" => $arFields["TYPE"],
			"TRANSACTION" => $arFields["TRANSACTION"],
			"ITEM_ID" => $arFields["ITEM_ID"],
			"CREDIT" => $arFields["CREDIT"],
			"ACTIVE" => "Y"
		);

		$LAST_ID = 0;

		if($arLog = self::GetList(Array("ID" => "DESC"), $oldFilter)->Fetch())
		{
			if($arLog["PRICE"] == $arFields["PRICE"] && $arLog["CURRENCY"] == $arFields["CURRENCY"])
				$LAST_ID = $arLog["ID"];
		}

		$DB->Update(
			"b_sm_finance_log", 
			array("ACTIVE" => "'N'"),
			"WHERE `STORE_ID` = '".$arFields["STORE_ID"]."' AND ".
			"`TYPE` = '".$arFields["TYPE"]."' AND ".
			"`TRANSACTION` = '".$arFields["TRANSACTION"]."' AND ".
			"`ITEM_ID` = '".$arFields["ITEM_ID"]."' AND ".
			"`CREDIT` = '".$arFields["CREDIT"]."' AND ".
			"`ACTIVE` = 'Y'"
		);

		if($LAST_ID > 0)
			$DB->Update("b_sm_finance_log", array("ACTIVE" => "'Y'"), "WHERE `ID` = ".$LAST_ID);
		else $LAST_ID = $DB->Add("b_sm_finance_log", $arFields, array("DESCRIPTION"), "", false, "", array("ignore_dml"=>true));

		return $LAST_ID;
	}

	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "TIMESTAMP_X", "TRANSACTION", "STORE_ID", "TYPE", "ITEM_ID", "SITE_ID", "REMOTE_ADDR", "USER_AGENT", "REQUEST_URI", "USER_ID", "GUEST_ID", "PRICE", "CURRENCY", "CREDIT", "ACTIVE");

		$arFields = array(
			"ID" => array("FIELD" => "FL.ID", "TYPE" => "int"),
			"TIMESTAMP_X" => array("FIELD" => "FL.TIMESTAMP_X", "TYPE" => "datetime"),
			"TRANSACTION" => array("FIELD" => "FL.TRANSACTION", "TYPE" => "string"),
			"STORE_ID" => array("FIELD" => "FL.STORE_ID", "TYPE" => "int"),
			"TYPE" => array("FIELD" => "FL.TYPE", "TYPE" => "string"),
			"ITEM_ID" => array("FIELD" => "FL.ITEM_ID", "TYPE" => "int"),
			"SITE_ID" => array("FIELD" => "FL.SITE_ID", "TYPE" => "string"),
			"REMOTE_ADDR" => array("FIELD" => "FL.REMOTE_ADDR", "TYPE" => "string"),
			"USER_AGENT" => array("FIELD" => "FL.USER_AGENT", "TYPE" => "string"),
			"REQUEST_URI" => array("FIELD" => "FL.REQUEST_URI", "TYPE" => "string"),
			"USER_ID" => array("FIELD" => "FL.USER_ID", "TYPE" => "int"),
			"GUEST_ID" => array("FIELD" => "FL.GUEST_ID", "TYPE" => "int"),
			"PRICE" => array("FIELD" => "FL.PRICE", "TYPE" => "double"),
			"CURRENCY" => array("FIELD" => "FL.CURRENCY", "TYPE" => "string"),
			"CREDIT" => array("FIELD" => "FL.CREDIT", "TYPE" => "string"),
			"ACTIVE" => array("FIELD" => "FL.ACTIVE", "TYPE" => "string"),
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		
		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_finance_log FL ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_finance_log FL ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_finance_log FL ".$arSqls["FROM"];
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