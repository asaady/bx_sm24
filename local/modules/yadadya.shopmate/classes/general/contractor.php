<?
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("catalog");

class SMContractor
	extends CCatalogContractor
{
	/** Add new store in table b_sm_contractor,
	* @static
	* @param $arFields
	* @return bool|int
	*/
	public static function add($arFields)
	{
		if($arFields["CONTRACTOR_ID"] = parent::add($arFields))
		{
			global $DB;

			$arInsert = $DB->PrepareInsert("b_sm_contractor", $arFields);

			$strSql = "INSERT INTO b_sm_contractor (".$arInsert[0].") VALUES(".$arInsert[1].")";

			$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
			if(!$res)
				return false;
			$lastId = intval($arFields["CONTRACTOR_ID"]);
			return $lastId;
		}
		return false;
	}

	static function update($id, $arFields)
	{
		if(parent::update($id, $arFields))
		{
			global $DB;
			$id = intval($id);

			if($id <= 0) return false;
			$strUpdate = $DB->PrepareUpdate("b_sm_contractor", $arFields);

			if(!empty($strUpdate))
			{
				$strSql = "UPDATE b_sm_contractor SET ".$strUpdate." WHERE CONTRACTOR_ID = ".$id." ";
				$w = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				if (is_object($w))
				{
					$rows = $w->AffectedRowsCount();

					if ($rows <= 0 && $additional_check)
					{
						$w = $this->Query("SELECT 'x' FROM b_sm_contractor CONTRACTOR_ID = ".$id);
						if (is_object($w))
							if ($w->Fetch())
								$rows = $w->SelectedRowsCount();
					}
				}
				if($rows <= 0)
				{
					$arFields["CONTRACTOR_ID"] = $id;
					$arInsert = $DB->PrepareInsert("b_sm_contractor", $arFields);
					$strSql = "INSERT INTO b_sm_contractor (".$arInsert[0].") VALUES(".$arInsert[1].")";
					$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
					if(!$res)
						return false;
				}
			}
			return $id;
		}
		return false;
	}

	static function delete($id)
	{
		if(parent::delete($id))
		{
			global $DB;
			$id = intval($id);
			if($id > 0)
			{
				$dbDocument = CCatalogDocs::getList(array(), array("CONTRACTOR_ID" => $id));
				if($arDocument = $dbDocument->Fetch())
				{
					$GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage("CC_CONTRACTOR_HAVE_DOCS"));
					return false;
				}

				return $DB->Query("DELETE FROM b_sm_contractor WHERE CONTRACTOR_ID = ".$id." ", true);
			}
			return false;
		}
	}

	public static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "CONTRACTOR_ID", "BIK", "OGRN", "NDS", "REGULAR", "CONTRACT", "CONTRACT_DATE", "DELAY", "ADDRESS_FACT", "NOTES", "PRODUCTION", "DISCOUNT");

		$arFields = array(
			"ID" => array("FIELD" => "CC.ID", "TYPE" => "int"),
			"CONTRACTOR_ID" => array("FIELD" => "CC.CONTRACTOR_ID", "TYPE" => "int"),
			"BIK" => array("FIELD" => "CC.BIK", "TYPE" => "string"),
			"OGRN" => array("FIELD" => "CC.OGRN", "TYPE" => "string"),
			"NDS" => array("FIELD" => "CC.NDS", "TYPE" => "string"),
			"REGULAR" => array("FIELD" => "CC.REGULAR", "TYPE" => "char"),
			"CONTRACT" => array("FIELD" => "CC.CONTRACT", "TYPE" => "string"),
			"CONTRACT_DATE" => array("FIELD" => "CC.CONTRACT_DATE", "TYPE" => "datetime"),
			"DELAY" => array("FIELD" => "CC.DELAY", "TYPE" => "string"),
			"ADDRESS_FACT" => array("FIELD" => "CC.ADDRESS_FACT", "TYPE" => "string"),
			"NOTES" => array("FIELD" => "CC.NOTES", "TYPE" => "string"),
			"PRODUCTION" => array("FIELD" => "CC.PRODUCTION", "TYPE" => "string"),
			"DISCOUNT" => array("FIELD" => "CC.DISCOUNT", "TYPE" => "string"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_contractor CC ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_contractor CC ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_contractor CC ".$arSqls["FROM"];
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

	public static function getDebt($id)
	{
		global $DB;
		$id = intval($id);
		if($id > 0)
		{
			$summ = $DB->Query("SELECT SUM(CD.TOTAL-SMD.TOTAL_FACT) as DEBT FROM b_catalog_store_docs CD INNER JOIN b_sm_store_docs SMD ON (CD.ID = SMD.DOC_ID) WHERE CD.CONTRACTOR_ID = ".$id." ", true)->Fetch();
			return IntVal($summ["DEBT"]);
		}
		return false;
	}

	public static function getListOR($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "PERSON_TYPE", "PERSON_NAME", "PERSON_LASTNAME", "PERSON_MIDDLENAME", "EMAIL", "PHONE", "POST_INDEX", "COUNTRY", "CITY", "COMPANY", "ADDRESS", "INN", "KPP");

		$arFields = array(
			"ID" => array("FIELD" => "CC.ID", "TYPE" => "int"),
			"PERSON_TYPE" => array("FIELD" => "CC.PERSON_TYPE", "TYPE" => "char"),
			"PERSON_NAME" => array("FIELD" => "CC.PERSON_NAME", "TYPE" => "string"),
			"PERSON_LASTNAME" => array("FIELD" => "CC.PERSON_LASTNAME", "TYPE" => "string"),
			"PERSON_MIDDLENAME" => array("FIELD" => "CC.PERSON_MIDDLENAME", "TYPE" => "string"),
			"EMAIL" => array("FIELD" => "CC.EMAIL", "TYPE" => "string"),
			"PHONE" => array("FIELD" => "CC.PHONE", "TYPE" => "string"),
			"POST_INDEX" => array("FIELD" => "CC.POST_INDEX", "TYPE" => "string"),
			"COUNTRY" => array("FIELD" => "CC.COUNTRY", "TYPE" => "string"),
			"CITY" => array("FIELD" => "CC.CITY", "TYPE" => "string"),
			"COMPANY" => array("FIELD" => "CC.COMPANY", "TYPE" => "string"),
			"ADDRESS" => array("FIELD" => "CC.ADDRESS", "TYPE" => "string"),
			"INN" => array("FIELD" => "CC.INN", "TYPE" => "string"),
			"KPP" => array("FIELD" => "CC.KPP", "TYPE" => "string"),
			"DATE_CREATE" => array("FIELD" => "CC.DATE_CREATE", "TYPE" => "datetime"),
			"DATE_MODIFY" => array("FIELD" => "CC.DATE_MODIFY", "TYPE" => "datetime"),
			"CREATED_BY" => array("FIELD" => "CC.CREATED_BY", "TYPE" => "int"),
			"MODIFIED_BY" => array("FIELD" => "CC.MODIFIED_BY", "TYPE" => "int"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		$arSqls["WHERE"] = str_ireplace(" and ", " or ", $arSqls["WHERE"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_contractor CC ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_catalog_contractor CC ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_catalog_contractor CC ".$arSqls["FROM"];
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

	function getByINN($inn = "")
	{
		$arResult = array();
		if(!empty($inn) && class_exists("phpQuery"))
		{
			$key = $val = "";
			$tmpResult = array();
			$file = file_get_contents("http://online.igk-group.ru/ru/home?name=&ogrn=&inn=".$inn);
			$document = phpQuery::newDocument($file);
			$result = $document->find("#home_bottom_results div[id^=tab]:visible table tr:eq(1) td table tr")->children();
			foreach ($result as $cell) 
			{
				if($cell->tagName == "th")
				{
					$key = $cell->textContent;
				}
				elseif($cell->tagName == "td" && !empty($key))
				{
					$val = $cell->textContent;
					$tmpResult[trim($key)] = trim($val);
					$key = $val = "";
				}
				else
				{
					$key = $val = "";
				}
			}

			$arResult = array(
				"PERSON_NAME" => $tmpResult["Руководство"],
				"COMPANY" => $tmpResult["Краткое название"],
				"INN" => $tmpResult["ИНН"],
				"ADDRESS" => $tmpResult["Адрес"],
				"OGRN" => $tmpResult["ОГРН"],
			);
		}
		return $arResult;
	}
}