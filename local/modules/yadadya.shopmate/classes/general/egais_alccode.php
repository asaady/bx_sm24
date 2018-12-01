<?
class SMEGAISAlcCode
{
	/** Add new store in table b_sm_egais_alccode,
	 * @static
	 * @param $arFields
	 * @return bool|int
	 */
	static function add($arFields)
	{
		global $DB;

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeSMEGAISAlcCodeAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				return false;

		if(array_key_exists('DATE_CREATE', $arFields))
			unset($arFields['DATE_CREATE']);
		if(array_key_exists('DATE_MODIFY', $arFields))
			unset($arFields['DATE_MODIFY']);

		$arFields['~DATE_MODIFY'] = $DB->GetNowFunction();
		$arFields['~DATE_CREATE'] = $DB->GetNowFunction();

		if(!self::CheckFields('ADD',$arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_sm_egais_alccode", $arFields);

		$strSql = "INSERT INTO b_sm_egais_alccode (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		foreach(GetModuleEvents("yadadya.shopmate", "OnSMEGAISAlcCodeAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($lastId, $arFields));

		return $lastId;
	}

	protected function CheckFields($action, &$arFields)
	{
		if((($action == 'ADD') || isset($arFields["PRODUCT_ID"])) && intval($arFields["PRODUCT_ID"]) <= 0)
		{
			return false;
		}

		if((($action == 'ADD') || isset($arFields["ALCCODE"])) && strlen($arFields["ALCCODE"]) <= 0)
		{
			return false;
		}

		return true;
	}

	static function Update($id, $arFields)
	{
		global $DB;
		$id = intval($id);

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeSMEGAISAlcCodeUpdate", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array($id, &$arFields)) === false)
				return false;

		if(array_key_exists('DATE_CREATE',$arFields))
			unset($arFields['DATE_CREATE']);
		if(array_key_exists('DATE_MODIFY', $arFields))
			unset($arFields['DATE_MODIFY']);
		if(array_key_exists('DATE_STATUS', $arFields))
			unset($arFields['DATE_STATUS']);
		if(array_key_exists('CREATED_BY', $arFields))
			unset($arFields['CREATED_BY']);

		$arFields['~DATE_MODIFY'] = $DB->GetNowFunction();

		if($id <= 0 || !self::CheckFields('UPDATE',$arFields))
			return false;
		$strUpdate = $DB->PrepareUpdate("b_sm_egais_alccode", $arFields);

		if(!empty($strUpdate))
		{
			$strSql = "UPDATE b_sm_egais_alccode SET ".$strUpdate." WHERE ID = ".$id." ";
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
		}

		foreach(GetModuleEvents("yadadya.shopmate", "OnSMEGAISAlcCodeUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($id, $arFields));

		return $id;
	}

	static function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeSMEGAISAlcCodeDelete", true) as $event)
				ExecuteModuleEventEx($event, array($id));

			$DB->Query("DELETE FROM b_sm_egais_alccode WHERE ID = ".$id." ", true);

			foreach(GetModuleEvents("yadadya.shopmate", "OnSMEGAISAlcCodeDelete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($id));

			return true;
		}
		return false;
	}

	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "PRODUCT_ID", "STORE_ID", "ALCCODE");

		$arFields = array(
			"ID" => array("FIELD" => "CB.ID", "TYPE" => "int"),
			"PRODUCT_ID" => array("FIELD" => "CB.PRODUCT_ID", "TYPE" => "int"),
			"STORE_ID" => array("FIELD" => "CB.STORE_ID", "TYPE" => "int"),
			"ALCCODE" => array("FIELD" => "CB.ALCCODE", "TYPE" => "string"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais_alccode CB ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais_alccode CB ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_egais_alccode CB ".$arSqls["FROM"];
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