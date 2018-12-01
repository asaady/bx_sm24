<?
IncludeModuleLangFile(__FILE__);

class SMDocs
{
    static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
    {
        global $DB;
        if (empty($arSelectFields))
            $arSelectFields = array("ID", "DOC_ID", "TOTAL_FACT", "NUMBER_DOCUMENT");

        $arFields = array(
            "ID" => array("FIELD" => "SMD.ID", "TYPE" => "int"),
            "DOC_ID" => array("FIELD" => "SMD.DOC_ID", "TYPE" => "int"),
            "TOTAL_FACT" => array("FIELD" => "SMD.TOTAL_FACT", "TYPE" => "double"),
            "NUMBER_DOCUMENT" => array("FIELD" => "SMD.NUMBER_DOCUMENT", "TYPE" => "string"),
        );
        $arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
        $arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);

        if (empty($arGroupBy) && is_array($arGroupBy))
        {
            $strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_store_docs SMD ".$arSqls["FROM"];
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

        $strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_store_docs SMD ".$arSqls["FROM"];
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
            $strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_store_docs SMD ".$arSqls["FROM"];
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

        $arInsert = $DB->PrepareInsert("b_sm_store_docs", $arFields);

        $strSql = "INSERT INTO b_sm_store_docs (".$arInsert[0].") VALUES(".$arInsert[1].")";

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
    public static function update($id, $arFields)
    {
        /** @global CDataBase $DB */
        global $DB;
        $id = (int)$id;
        $strUpdate = $DB->PrepareUpdate("b_sm_store_docs", $arFields);

        if(!empty($strUpdate))
        {
            $strSql = "update b_sm_store_docs set ".$strUpdate." where DOC_ID = ".$id;
            if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
                return false;
        }
        return true;
    }

    public static function delete($id)
    {
        global $DB;
        $id = (int)$id;
        if($id > 0)
        {
            $DB->Query("DELETE FROM b_sm_store_docs WHERE DOC_ID = ".$id, true);
            return true;
        }
        return false;
    }

    function OnCatalogDocumentAdd($docId, $arFields)
    {
    	$arFields["DOC_ID"] = $docId;
    	self::Add($arFields);
    }

    function OnCatalogDocumentUpdate($docId, $arFields)
    {
    	self::Update($docId, $arFields);
    }

    function OnCatalogDocumentDelete($docId)
    {
    	self::delete($docId);
    }
}