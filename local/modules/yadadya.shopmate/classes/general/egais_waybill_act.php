<?php

IncludeModuleLangFile(__FILE__);

class SMEGAISWaybillAct
{
	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		CModule::IncludeModule("catalog");
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "NAME", "DATE", "DOCUMENT", "REPLY_ID", "XML");

		$arFields = array(
			"ID" => array("FIELD" => "SMEWBA.ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "SMEWBA.NAME", "TYPE" => "string"),
			"DATE" => array("FIELD" => "SMEWBA.DATE", "TYPE" => "datetime"),
			"DOCUMENT" => array("FIELD" => "SMEWBA.DOCUMENT", "TYPE" => "string"),
			"REPLY_ID" => array("FIELD" => "SMEWBA.REPLY_ID", "TYPE" => "string"),
			"XML" => array("FIELD" => "SMEWBA.XML", "TYPE" => "string")
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		
		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais_waybill_act SMEWBA ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais_waybill_act SMEWBA ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_egais_waybill_act SMEWBA ".$arSqls["FROM"];
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

	/**
	* @static
	* @param $arFields
	* @return bool|int
	*/
	static function add($arFields)
	{
		global $DB;

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISWaybillActAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sm_egais_waybill_act", $arFields);

		$strSql = "INSERT INTO b_sm_egais_waybill_act (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISWaybillActAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($lastId, $arFields));

		return $lastId;
	}

	/**
	 * @param $id
	 * @param $arFields
	 * @return bool
	 */
	public static function update($id, $arFields)
	{
		/** @global CDataBase $DB */
		global $DB;
		$id = (int)$id;

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISWaybillActUpdate", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array($id, &$arFields)) === false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sm_egais_waybill_act", $arFields);

		$strSql = "update b_sm_egais_waybill_act set ".$strUpdate." where ID = ".$id;
		$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);

		foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISWaybillActUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($id, $arFields));

		return true;
	}

	public static function delete($id)
	{
		global $DB;
		$id = (int)$id;
		if($id > 0)
		{
			foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISWaybillActDelete", true) as $arEvent)
				if(ExecuteModuleEventEx($arEvent, array($id)) === false)
					return false;

			$DB->Query("DELETE FROM b_sm_egais_waybill_act WHERE ID = ".$id, true);

			foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISWaybillActDelete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($id));

			return $id;
		}
		return false;
	}

	function onUpdateWaybillAct($id, $arFields)
	{
		if(strtoupper($arFields["DOCUMENT"]) == "TICKET")
		{
			$doc_xml = simplexml_load_string($arFields["XML"]);
			$IsAccept = (string) $doc_xml->Document->Ticket->OperationResult->OperationResult;
			$DocType = (string) $doc_xml->Document->Ticket->DocType;
			if($DocType == "WAYBILL" && $IsAccept == "Accepted")
			{
				$arFilter = array(
					"IDENTITY" => (string) $doc_xml->Document->Ticket->Identity,
					"~FORMBREGINFO" => "%".((string) $doc_xml->Document->Ticket->RegID)."%",
				);
				$wb = new SMEGAISWaybill();
				$rsWB = $wb->getList(array(), $arFilter, false, false, array("ID"));
				while($arWB = $rsWB->Fetch())
				{
					$wb->update($arWB["ID"], array("ACCEPTED" => "Y"));
				}
			}
		}
	}
}