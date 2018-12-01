<?
IncludeModuleLangFile(__FILE__);

class SMProduct
{
	protected static function checkFields($action, &$arFields)
	{
		$action = strtoupper($action);
		if ('UPDATE' != $action && 'ADD' != $action)
			return false;
		if (is_set($arFields, "SHELF_LIFE") || 'ADD' == $arFields["SHELF_LIFE"])
		{
			$arFields["SHELF_LIFE"] = str_replace(',', '.', $arFields["SHELF_LIFE"]);
			$arFields["SHELF_LIFE"] = doubleval($arFields["SHELF_LIFE"]);
		}
		return true;
	}

	public static function update($id, $arFields)
	{
		$id = intval($id);
		if($id < 0 || !self::checkFields('UPDATE', $arFields))
			return false;
		global $DB;
		$strUpdate = $DB->PrepareUpdate("b_sm_product", $arFields);
		if (!empty($strUpdate))
		{
			$strSql = "UPDATE b_sm_product SET ".$strUpdate." WHERE PRODUCT_ID = ".$id;
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
		}
		return $id;
	}

	public static function delete($id)
	{
		global $DB;
		$id = intval($id);
		if($id > 0)
		{
			if($DB->Query("DELETE FROM b_sm_product WHERE PRODUCT_ID = ".$id, true))
				return true;
		}
		return false;
	}

	function add($arFields)
	{
		global $DB;
		if(!self::CheckFields('ADD',$arFields))
			return false;

		$arInsert = $DB->PrepareInsert("b_sm_product", $arFields);
		$strSql = "insert into b_sm_product (".$arInsert[0].") values(".$arInsert[1].")";

		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());
		return $lastId;
	}

	function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "PRODUCT_ID", "SHELF_LIFE", "DNC_TYPE_CODE");
		$arFields = array(
			"ID" => array("FIELD" => "MR.ID", "TYPE" => "int"),
			"PRODUCT_ID" => array("FIELD" => "MR.PRODUCT_ID", "TYPE" => "int"),
			"SHELF_LIFE" => array("FIELD" => "MR.SHELF_LIFE", "TYPE" => "double"),
			"DNC_TYPE_CODE" => array("FIELD" => "MR.DNC_TYPE_CODE", "TYPE" => "int"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "select ".$arSqls["SELECT"]." from b_sm_product MR ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql .= " where ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= " group by ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "select ".$arSqls["SELECT"]." from b_sm_product MR ".$arSqls["FROM"];
		if (!empty($arSqls["WHERE"]))
			$strSql .= " where ".$arSqls["WHERE"];
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= " group by ".$arSqls["GROUPBY"];
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= " order by ".$arSqls["ORDERBY"];

		$intTopCount = 0;
		$boolNavStartParams = (!empty($arNavStartParams) && is_array($arNavStartParams));
		if ($boolNavStartParams && isset($arNavStartParams['nTopCount']))
		{
			$intTopCount = intval($arNavStartParams["nTopCount"]);
		}
		if ($boolNavStartParams && 0 >= $intTopCount)
		{
			$strSql_tmp = "select COUNT('x') as CNT FROM b_sm_product MR ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= " where ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= " group by ".$arSqls["GROUPBY"];

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
				$strSql .= " limit ".$intTopCount;
			}
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return $dbRes;
	}
}