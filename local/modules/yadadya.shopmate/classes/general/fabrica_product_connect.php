<?
IncludeModuleLangFile(__FILE__);

class SMFabricaProductConnect
{
	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		CModule::IncludeModule("catalog");
		if (empty($arSelectFields))
			$arSelectFields = array("PARENT_ID", "CONNECT_ID", "AMOUNT", "MEASURE", "MEASURE_FROM", "MEASURE_TO", "WASTE_RATE", "AMOUNT_RATIO");

		$arFields = array(
			"PARENT_ID" => array("FIELD" => "SMC.PARENT_ID", "TYPE" => "int"),
			"CONNECT_ID" => array("FIELD" => "SMC.CONNECT_ID", "TYPE" => "int"),

			"AMOUNT" => array("FIELD" => "SMC.AMOUNT", "TYPE" => "double"),
			"MEASURE" => array("FIELD" => "SMC.MEASURE", "TYPE" => "int"),

			"MEASURE_FROM" => array("FIELD" => "SMC.MEASURE_FROM", "TYPE" => "int"),
			"MEASURE_TO" => array("FIELD" => "SMC.MEASURE_TO", "TYPE" => "int"),
			
			"WASTE_RATE" => array("FIELD" => "SMC.WASTE_RATE", "TYPE" => "double"),
			"AMOUNT_RATIO" => array("FIELD" => "SMC.AMOUNT_RATIO", "TYPE" => "double"),

			"PARENT_TYPE" => array("FIELD" => "SMPP.TYPE", "TYPE" => "int", "FROM" => "INNER JOIN b_sm_fabrica_product SMPP ON (SMC.PARENT_ID = SMPP.ID)"),
			"CONNECT_TYPE" => array("FIELD" => "SMPC.TYPE", "TYPE" => "int", "FROM" => "INNER JOIN b_sm_fabrica_product SMPC ON (SMC.CONNECT_ID = SMPC.ID)"),

			"PARENT_PRODUCT_ID" => array("FIELD" => "SMPPI.PRODUCT_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sm_fabrica_product SMPPI ON (SMC.PARENT_ID = SMPPI.ID)"),
			"CONNECT_PRODUCT_ID" => array("FIELD" => "SMPCI.PRODUCT_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sm_fabrica_product SMPCI ON (SMC.CONNECT_ID = SMPCI.ID)"),
			
			"PRODUCT_ID" => array("FIELD" => "SMPI.PRODUCT_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sm_fabrica_product SMPI ON (SMC.PARENT_ID = SMPI.ID OR SMC.CONNECT_ID = SMPI.ID)"),
			"STORE_ID" => array("FIELD" => "SMPS.STORE_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sm_fabrica_product SMPS ON (SMC.PARENT_ID = SMPS.ID OR SMC.CONNECT_ID = SMPS.ID)"),
		);
		if(!empty($arFilter["ID"]))
		{
			$product_id = $arFilter["ID"];
			unset($arFilter["ID"], $arFilter["PARENT_ID"], $arFilter["CONNECT_ID"]);
		}
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);
		if($product_id > 0)
			$arSqls["WHERE"] .= (!empty($arSqls["WHERE"]) ? " AND " : "") . "(".GetFilterQuery("SMC.PARENT_ID",$product_id,"N")." OR ".GetFilterQuery("SMC.CONNECT_ID",$product_id,"N").")";

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_fabrica_product_connect SMC ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_fabrica_product_connect SMC ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_fabrica_product_connect SMC ".$arSqls["FROM"];
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

		$arInsert = $DB->PrepareInsert("b_sm_fabrica_product_connect", $arFields);

		$strSql = "INSERT INTO b_sm_fabrica_product_connect (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		return $lastId;
	}

	/**
	 * @param $id
	 * @param $arFields
	 * @return bool
	 */
	public static function update($arFields)
	{
		/** @global CDataBase $DB */
		global $DB;
		$arFields["PARENT_ID"] = (int)$arFields["PARENT_ID"];
		$arFields["CONNECT_ID"] = (int)$arFields["CONNECT_ID"];
		$strUpdate = $DB->PrepareUpdate("b_sm_fabrica_product_connect", $arFields);

		if(!empty($strUpdate) && $arFields["PARENT_ID"] > 0 && $arFields["CONNECT_ID"] > 0)
		{
			$strSql = "update b_sm_fabrica_product_connect set ".$strUpdate." where PARENT_ID = ".$arFields["PARENT_ID"]." AND CONNECT_ID = ".$arFields["CONNECT_ID"];
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
		}
		return true;
	}

	public static function delete($arFilter)
	{
		global $DB;
		$arFields = array(
			"PARENT_ID" => array("FIELD" => "PARENT_ID", "TYPE" => "int"),
			"CONNECT_ID" => array("FIELD" => "CONNECT_ID", "TYPE" => "int")
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		if(!empty($arSqls["WHERE"]))
		{
			$DB->Query("DELETE FROM b_sm_fabrica_product_connect WHERE ".$arSqls["WHERE"], true);
			return true;
		}
		return false;
	}
}