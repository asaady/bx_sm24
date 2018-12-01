<?
IncludeModuleLangFile(__FILE__);

class SMStoreDocsElement
{
	protected static function CheckFields($action, &$arFields)
	{
		if((isset($arFields["SHOP_PRICE"])))
		{
			$arFields["SHOP_PRICE"] =  preg_replace("|\s|", '', $arFields["SHOP_PRICE"]);
		}

		return true;
	}

	public static function update($id, $arFields)
	{
		$id = intval($id);
		unset($arFields["ID"]);

		if($id <= 0 || !self::CheckFields('UPDATE',$arFields))
			return false;

		global $DB;
		$strUpdate = $DB->PrepareUpdate("b_sm_docs_element", $arFields);
		$strSql = "UPDATE b_sm_docs_element SET ".$strUpdate." WHERE DOCS_ELEMENT_ID = ".$id;
		if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
			return false;

		return true;
	}

	public static function delete($id)
	{
		global $DB;
		$id = intval($id);
		if($id > 0)
		{
			$DB->Query("DELETE FROM b_sm_docs_element WHERE DOCS_ELEMENT_ID = ".$id." ", true);

			return true;
		}
		return false;
	}

	public static function add($arFields)
	{
		global $DB;

		$arInsert = $DB->PrepareInsert("b_sm_docs_element", $arFields);
		$strSql = "INSERT INTO b_sm_docs_element (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		return $lastId;
	}

	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;

		if (empty($arSelectFields))
			$arSelectFields = array("ID", "DOCS_ELEMENT_ID", "DOC_AMOUNT", "SHOP_PRICE", "END_DATE", "PURCHASING_NDS");

		$arFields = array(
			"ID" => array("FIELD" => "DE.ID", "TYPE" => "int"),
			"DOCS_ELEMENT_ID" => array("FIELD" => "DE.DOCS_ELEMENT_ID", "TYPE" => "int"),
			"DOC_AMOUNT" => array("FIELD" => "DE.DOC_AMOUNT", "TYPE" => "double"),
			"SHOP_PRICE" => array("FIELD" => "DE.SHOP_PRICE", "TYPE" => "double"),
			"END_DATE" => array("FIELD" => "DE.END_DATE", "TYPE" => "datetime"),
			"PURCHASING_NDS" => array("FIELD" => "DE.PURCHASING_NDS", "TYPE" => "double"),
			"ELEMENT_ID" => array("FIELD" => "CDE.ELEMENT_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_docs_element CDE ON (DE.DOCS_ELEMENT_ID = CDE.ID)"),
			"DOC_ID" => array("FIELD" => "SDE.DOC_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_docs_element SDE ON (DE.DOCS_ELEMENT_ID = SDE.ID)"),
			"STORE_ID" => array("FIELD" => "SDE.STORE_TO", "TYPE" => "int", "FROM" => "INNER JOIN b_catalog_docs_element SDE ON (DE.DOCS_ELEMENT_ID = SDE.ID)"),
		);
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_docs_element DE ".$arSqls["FROM"];
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
		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_docs_element DE ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_docs_element DE ".$arSqls["FROM"];
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

    function OnCatalogStoreDocsElementAdd($docsElementId, $arFields)
    {
    	$arFields["DOCS_ELEMENT_ID"] = $docsElementId;
    	self::Add($arFields);

    	//add amount in store
    	$arStoreProduct = array(
    		"PRODUCT_ID" => $arFields["ELEMENT_ID"], 
    		"AMOUNT" => $arFields["AMOUNT"],
    		"STORE_ID" => $arFields["STORE_TO"]
    	);

		$rsProps = CCatalogStoreProduct::GetList(array(),array("PRODUCT_ID"=>$arStoreProduct['PRODUCT_ID'], "STORE_ID"=>$arStoreProduct['STORE_ID']),false,false,array("ID", "AMOUNT"));
		if($arProps = $rsProps->GetNext())
		{

			$arStoreProduct["AMOUNT"] += $arProps["AMOUNT"];
			return CCatalogStoreProduct::Update($arProps["ID"],$arStoreProduct);
		}
		else
			return CCatalogStoreProduct::Add($arStoreProduct);
    }

    function OnCatalogStoreDocsElementUpdate($docsElementId, $arFields)
    {
    	self::Update($docsElementId, $arFields);
    }

    function OnCatalogStoreDocsElementDelete($docsElementId)
    {
    	self::delete($docsElementId);
    }
}