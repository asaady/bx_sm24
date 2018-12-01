<?php

IncludeModuleLangFile(__FILE__);

class SMEGAISWaybill
{
	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		CModule::IncludeModule("catalog");
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "STORE_ID", "NAME", "IDENTITY", "DATE", "NUMBER", "WAYBILL", "FORMBREGINFO", "ACCEPTED");

		$arFields = array(
			"ID" => array("FIELD" => "SMEWB.ID", "TYPE" => "int"),
			"STORE_ID" => array("FIELD" => "SMEWB.STORE_ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "SMEWB.NAME", "TYPE" => "string"),
			"IDENTITY" => array("FIELD" => "SMEWB.IDENTITY", "TYPE" => "string"),
			"DATE" => array("FIELD" => "SMEWB.DATE", "TYPE" => "datetime"),
			"NUMBER" => array("FIELD" => "SMEWB.NUMBER", "TYPE" => "string"),
			"WAYBILL" => array("FIELD" => "SMEWB.WAYBILL", "TYPE" => "string"),
			"FORMBREGINFO" => array("FIELD" => "SMEWB.FORMBREGINFO", "TYPE" => "string"),
			"ACCEPTED" => array("FIELD" => "SMEWB.ACCEPTED", "TYPE" => "string")
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		
		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais_waybill SMEWB ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais_waybill SMEWB ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_egais_waybill SMEWB ".$arSqls["FROM"];
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

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISWaybillAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sm_egais_waybill", $arFields);

		$strSql = "INSERT INTO b_sm_egais_waybill (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISWaybillAdd", true) as $arEvent)
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

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISWaybillUpdate", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array($id, &$arFields)) === false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sm_egais_waybill", $arFields);

		$strSql = "update b_sm_egais_waybill set ".$strUpdate." where ID = ".$id;
		$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);

		foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISWaybillUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($id, $arFields));

		return true;
	}

	public static function delete($id)
	{
		global $DB;
		$id = (int)$id;
		if($id > 0)
		{
			foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISWaybillDelete", true) as $arEvent)
				if(ExecuteModuleEventEx($arEvent, array($id)) === false)
					return false;

			$DB->Query("DELETE FROM b_sm_egais_waybill WHERE ID = ".$id, true);

			foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISWaybillDelete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($id));

			return $id;
		}
		return false;
	}

	function onUpdateOptOut($id, $arOptIn)
	{
		if(empty($arOptIn["XML"])) return false;
		$arOptIn["XML"] = preg_replace("/(<[\/]{0,1})([a-z]+:)/", "$1", $arOptIn["XML"]);
		$arOptIn["DOCUMENT"] = strtoupper($arOptIn["DOCUMENT"]);
		if(in_array($arOptIn["DOCUMENT"], array("WAYBILL", "FORMBREGINFO")))
		{
			$doc_xml = simplexml_load_string($arOptIn["XML"]);
			if($arOptIn["DOCUMENT"] == "WAYBILL")
				$arFields = array(
					"IDENTITY" => (string) $doc_xml->Document->WayBill->Identity,
					"NUMBER" => (string) $doc_xml->Document->WayBill->Header->NUMBER,
					"DATE" => (string) $doc_xml->Document->WayBill->Header->Date,
				);
			elseif($arOptIn["DOCUMENT"] == "FORMBREGINFO")
				$arFields = array(
					"IDENTITY" => (string) $doc_xml->Document->TTNInformBReg->Header->Identity,
					"NUMBER" => (string) $doc_xml->Document->TTNInformBReg->Header->WBNUMBER,
					"DATE" => (string) $doc_xml->Document->TTNInformBReg->Header->WBDate,
				);
			$arFields["DATE"] = ConvertTimeStamp(empty($arFields["DATE"]) ? time() : strtotime($arFields["DATE"]), "SHORT");

			$update = array();
			$wb = new SMEGAISWaybill();
			$rsWB = $wb->getList(array(), $arFields, false, false, array("ID", "XML"));
			while($arWB = $rsWB->Fetch())
				$update[] = $arWB;

			$arFields["NAME"] = GetMessage("SM_WB_TTN_TITLE").$arFields["NUMBER"]." ".GetMessage("SM_WB_TTN_FROM_TITLE")." ".$arFields["DATE"];
			if($arOptIn["DOCUMENT"] == "WAYBILL")
				$arFields["WAYBILL"] = $arOptIn["XML"];
			elseif($arOptIn["DOCUMENT"] == "FORMBREGINFO")
				$arFields["FORMBREGINFO"] = $arOptIn["XML"];
			//print_p($update);
			if(!empty($update))
			{
				foreach ($update as $arWB)
					$wb->update($arWB["ID"], $arFields);
			}
			else
			{
				$wb->add($arFields);
			}
		}
		elseif(in_array($arOptIn["DOCUMENT"], array("TICKET", "CRYPTOTICKET")))
		{
			$arFields = array(
				"NAME" => "",
				"DATE" => (string) $doc_xml->Document->Ticket->Result->ConclusionDate,
				"DOCUMENT" => $arOptIn["DOCUMENT"],
				"REPLY_ID" => $arOptIn["REPLY_ID"],
				"XML" => $arOptIn["XML"],
			);

			if($arOptIn["DOCUMENT"] == "CRYPTOTICKET")
				$arFields["NAME"] = GetMessage("SM_WB_TTN_ACT_SYSTEM_MESSAGE");
			elseif($arOptIn["DOCUMENT"] == "TICKET")
			{
				$doc_xml = simplexml_load_string($arOptIn["XML"]);
				$DocType = (string) $doc_xml->Document->Ticket->DocType;
				if($DocType == "WayBillAct")
					$arFields["NAME"] = GetMessage("SM_WB_TTN_ACT_TICKET");
				elseif($DocType == "WAYBILL")
				{
					$arFields["NAME"] = GetMessage("SM_WB_TTN_ACT_WAYBILL");
					$arFields["DATE"] = (string) $doc_xml->Document->Ticket->OperationResult->OperationDate;
				}
			}
			else
				$arFields["NAME"] = $arOptIn["DOCUMENT"];

			$arFields["DATE"] = ConvertTimeStamp(empty($arFields["DATE"]) ? time() : strtotime($arFields["DATE"]), "FULL");

			$update = array();
			$wb = new SMEGAISWaybillAct();
			$rsWB = $wb->getList(array(), array("DOCUMENT" => $arFields["DOCUMENT"], "REPLY_ID" => $arFields["REPLY_ID"]), false, false, array("ID"));
			while($arWB = $rsWB->Fetch())
				$update[] = $arWB;

			if(!empty($update))
			{
				foreach ($update as $arWB)
					$wb->update($arWB["ID"], $arFields);
			}
			else
			{
				$wb->add($arFields);
			}
		}
	}

	function sendWayBillAct($arFields)
	{
		if(empty($arFields["ACTNUMBER"])) $arFields["ACTNUMBER"] = "1";
		if(empty($arFields["IsAccept"])) $arFields["IsAccept"] = "Accepted";
		if(empty($arFields["Note"])) $arFields["Note"] = "Принимаем продукцию";
		if(empty($arFields["ActDate"])) $arFields["ActDate"] = date("Y-m-d");
		else date("Y-m-d", strtotime($arFields["ActDate"]));
		$arFields["Content"] = "";
		if(is_array($arFields["Positions"]))
		{
			foreach($arFields["Positions"] as $position)
			{
				$arFields["Content"] .= "<wa:Position>".
					"<wa:Identity>".$position["Identity"]."</wa:Identity>".
					"<wa:RealQuantity>".$position["RealQuantity"]."</wa:RealQuantity>".
					"<wa:InformBRegId>".$position["InformBRegId"]."</wa:InformBRegId>".
				"</wa:Position>";
			}
		}

		$device = SMEGAIS::getParams();

		$xml = "<ns:Documents Version=\"1.0\" ".
		"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ".
		"xmlns:ns=\"http://fsrar.ru/WEGAIS/WB_DOC_SINGLE_01\" ".
		"xmlns:oref=\"http://fsrar.ru/WEGAIS/ClientRef\" ".
		"xmlns:pref=\"http://fsrar.ru/WEGAIS/ProductRef\" ".
		"xmlns:wa=\"http://fsrar.ru/WEGAIS/ActTTNSingle\" ".
		"> ".
		"	<ns:Owner> ".
		"		<ns:FSRAR_ID>".$device["FSRAR_ID"]."</ns:FSRAR_ID> ".
		"	</ns:Owner> ".
		"	<ns:Document> ".
		"		<ns:WayBillAct> ".
		"			<wa:Header> ".
		"				<wa:IsAccept>".$arFields["IsAccept"]."</wa:IsAccept> ".
		"				<wa:ACTNUMBER>".$arFields["ACTNUMBER"]."</wa:ACTNUMBER> ".
		"				<wa:ActDate>".$arFields["ActDate"]."</wa:ActDate> ".
		"				<wa:WBRegId>".$arFields["WBRegId"]."</wa:WBRegId> ".
		"				<wa:Note>".$arFields["Note"]."</wa:Note> ".
		"			</wa:Header> ".
		"			<wa:Content>".$arFields["Content"]."</wa:Content> ".
		"		</ns:WayBillAct> ".
		"	</ns:Document> ".
		"</ns:Documents>";

		/*$arActFields = array(
			"WAYBILL_ID" => $arFields["WAYBILL_ID"],
			"NAME" => ($arFields["IsAccept"] != "Accepted" ? GetMessage("SM_WB_TTN_ACT_REJECTED_TITLE") : (empty($arFields["Content"]) ? GetMessage("SM_WB_TTN_ACT_ACCEPTED_TITLE") : GetMessage("SM_WB_TTN_ACT_DIFFERENCES_TITLE"))),
			"DATE" => ConvertTimeStamp(time(), "FULL"),
			"XML" => $xml,
		);

		$act_id = self::add($arActFields);
		$_SESSION["LAST_ACT_ID"] = $act_id;*/

		return SMEGAIS::updateOptIn("/opt/in/WayBillAct", $xml);	
	}

	function onUpdateOptIn($id, $arOptIn)
	{
		if(!empty($arOptIn["XML"]))
		{
			$arOptIn["XML"] = preg_replace("/(<[\/]{0,1})([a-z]+:)/", "$1", $arOptIn["XML"]);
			$doc_xml = simplexml_load_string($arOptIn["XML"]);
			$arFields = array();
			if($arOptIn["DOCUMENT"] == "WayBillAct")
			{
				$arFields = array(
					"NAME" => ((string) $doc_xml->Document->WayBillAct->Header->IsAccept != "Accepted" ? GetMessage("SM_WB_TTN_ACT_REJECTED_TITLE") : (empty($doc_xml->Document->WayBillAct->Content) ? GetMessage("SM_WB_TTN_ACT_ACCEPTED_TITLE") : GetMessage("SM_WB_TTN_ACT_DIFFERENCES_TITLE"))),
					"DATE" => (string) $doc_xml->Document->WayBillAct->Header->ActDate,
					"DOCUMENT" => $arOptIn["DOCUMENT"],
					"REPLY_ID" => $arOptIn["REPLY_ID"],
					"XML" => $arOptIn["XML"],
				);
			}
			$arFields["DATE"] = ConvertTimeStamp(empty($arFields["DATE"]) ? time() : strtotime($arFields["DATE"]), "SHORT");

			$update = array();
			$wb = new SMEGAISWaybillAct();
			$rsWB = $wb->getList(array(), array("DOCUMENT" => $arFields["DOCUMENT"], "REPLY_ID" => $arFields["REPLY_ID"]), false, false, array("ID"));
			while($arWB = $rsWB->Fetch())
				$update[] = $arWB;

			if(!empty($update))
			{
				foreach ($update as $arWB)
					$wb->update($arWB["ID"], $arFields);
			}
			else
			{
				$wb->add($arFields);
			}

			/*if($arOptIn["DOCUMENT"] == "WayBillAct")
				$arFields = array(
					"IDENTITY" => (string) $doc_xml->Document->WayBill->Identity,
					"NUMBER" => (string) $doc_xml->Document->WayBill->Header->NUMBER,
					"DATE" => (string) $doc_xml->Document->WayBill->Header->Date,
				);*/
			/*if($arOptIn["DOCUMENT"] == "WAYBILL")
				$arFields = array(
					"IDENTITY" => (string) $doc_xml->Document->WayBill->Identity,
					"NUMBER" => (string) $doc_xml->Document->WayBill->Header->NUMBER,
					"DATE" => (string) $doc_xml->Document->WayBill->Header->Date,
				);
			elseif($arOptIn["DOCUMENT"] == "FORMBREGINFO")
				$arFields = array(
					"IDENTITY" => (string) $doc_xml->Document->TTNInformBReg->Header->Identity,
					"NUMBER" => (string) $doc_xml->Document->TTNInformBReg->Header->WBNUMBER,
					"DATE" => (string) $doc_xml->Document->TTNInformBReg->Header->WBDate,
				);
			$arFields["DATE"] = ConvertTimeStamp(empty($arFields["DATE"]) ? time() : strtotime($arFields["DATE"]), "SHORT");

			$update = array();
			$wb = new SMEGAISWaybill();
			$rsWB = $wb->getList(array(), $arFields, false, false, array("ID", "XML"));
			while($arWB = $rsWB->Fetch())
				$update[] = $arWB;

			$arFields["NAME"] = GetMessage("SM_WB_TTN_TITLE").$arFields["NUMBER"]." ".GetMessage("SM_WB_TTN_FROM_TITLE")." ".$arFields["DATE"];
			if($arOptIn["DOCUMENT"] == "WAYBILL")
				$arFields["WAYBILL"] = $arOptIn["XML"];
			elseif($arOptIn["DOCUMENT"] == "FORMBREGINFO")
				$arFields["FORMBREGINFO"] = $arOptIn["XML"];
			//print_p($update);
			if(!empty($update))
			{
				foreach ($update as $arWB)
					$wb->update($arWB["ID"], $arFields);
			}
			else
			{
				$wb->add($arFields);
			}*/
		}
	}
}