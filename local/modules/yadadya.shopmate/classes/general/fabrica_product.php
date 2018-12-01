<?
IncludeModuleLangFile(__FILE__);

class SMFabricaProduct
{
	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		CModule::IncludeModule("catalog");
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "PARENT_ID", "PRODUCT_ID", "NAME", "MEASURE", "MEASURE_FROM", "MEASURE_TO", "TYPE", "AMOUNT", "AMOUNT_MEASURE", "FAULT_RATIO", "WASTE_RATE", "STORE_ID", "USER_ID", "QUANTITY", "ACTIVE");

		$arFields = array(
			"ID" => array("FIELD" => "SMD.ID", "TYPE" => "int"),
			"PARENT_ID" => array("FIELD" => "SMD.PARENT_ID", "TYPE" => "int"),

			"PRODUCT_ID" => array("FIELD" => "SMD.PRODUCT_ID", "TYPE" => "int"),
			"NAME" => array("FIELD" => "SMD.NAME", "TYPE" => "string"),
			"MEASURE" => array("FIELD" => "SMD.MEASURE", "TYPE" => "int"),

			"MEASURE_FROM" => array("FIELD" => "SMD.MEASURE_FROM", "TYPE" => "int"),
			"MEASURE_TO" => array("FIELD" => "SMD.MEASURE_TO", "TYPE" => "int"),

			"TYPE" => array("FIELD" => "SMD.TYPE", "TYPE" => "string"),

			"AMOUNT" => array("FIELD" => "SMD.AMOUNT", "TYPE" => "double"),
			"AMOUNT_MEASURE" => array("FIELD" => "SMD.AMOUNT_MEASURE", "TYPE" => "int"),

			"FAULT_RATIO" => array("FIELD" => "SMD.FAULT_RATIO", "TYPE" => "double"),
			"WASTE_RATE" => array("FIELD" => "SMD.WASTE_RATE", "TYPE" => "double"),

			"STORE_ID" => array("FIELD" => "SMD.STORE_ID", "TYPE" => "int"),
			"USER_ID" => array("FIELD" => "SMD.USER_ID", "TYPE" => "int"),

			"QUANTITY" => array("FIELD" => "SMD.QUANTITY", "TYPE" => "double"),
			"ACTIVE" => array("FIELD" => "SMD.ACTIVE", "TYPE" => "string"),

			"PROD_QUANTITY" => array("FIELD" => "SUM(SMD.QUANTITY)", "TYPE" => "double", "FROM" => "INNER JOIN b_sm_fabrica_product SMPQ ON (SMD.PRODUCT_ID = SMPQ.PRODUCT_ID)"),
		);

		if($arFilter["ID"] > 0 && $arFilter["INCLUDE_SUBPRODUCT"] == "Y")
		{
			$product_full = IntVal($arFilter["ID"]);
			unset($arFilter["ID"], $arFilter["INCLUDE_SUBPRODUCT"]);
		}
		elseif($arFilter["INCLUDE_SUBPRODUCT"] == "N")
		{
			$product_parents = true;
			unset($arFilter["INCLUDE_SUBPRODUCT"]);
		}
		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		if($product_full > 0)
			$arSqls["WHERE"] .= (!empty($arSqls["WHERE"]) ? " AND " : "") . "(".GetFilterQuery("SMD.ID",$product_full,"N")." OR ".GetFilterQuery("SMD.PARENT_ID",$product_full,"N").")";
		if($product_parents)
			$arSqls["WHERE"] .= (!empty($arSqls["WHERE"]) ? " AND " : "") . "!(SMD.PARENT_ID > 0 AND SMD.PARENT_ID <> SMD.ID)";

		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_fabrica_product SMD ".$arSqls["FROM"];
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

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_fabrica_product SMD ".$arSqls["FROM"];
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
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_fabrica_product SMD ".$arSqls["FROM"];
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

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeFabricaProductAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sm_fabrica_product", $arFields);

		$strSql = "INSERT INTO b_sm_fabrica_product (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		foreach(GetModuleEvents("yadadya.shopmate", "OnFabricaProductAdd", true) as $arEvent)
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

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeFabricaProductUpdate", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array($id, &$arFields)) === false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sm_fabrica_product", $arFields);

		$strSql = "update b_sm_fabrica_product set ".$strUpdate." where ID = ".$id;
		$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);

		foreach(GetModuleEvents("yadadya.shopmate", "OnFabricaProductUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($id, $arFields));

		return true;
	}

	public static function delete($id)
	{
		global $DB;
		$id = (int)$id;
		if($id > 0)
		{
			foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeFabricaProductDelete", true) as $arEvent)
				if(ExecuteModuleEventEx($arEvent, array($id)) === false)
					return false;

			$DB->Query("DELETE FROM b_sm_fabrica_product WHERE ID = ".$id, true);

			foreach(GetModuleEvents("yadadya.shopmate", "OnFabricaProductDelete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($id));

			return $id;
		}
		return false;
	}

	function calcProductQuantity($id)
	{
		$id = (int)$id;
		if($id > 0)
		{
			$oProduct = new SMFabricaProduct();
			$oConnect = new SMFabricaProductConnect();
			$SHOP_ID = SMShops::getUserShop();
			$arSelConnect = array("PARENT_ID", "CONNECT_ID", "AMOUNT", "MEASURE", "MEASURE_FROM", "MEASURE_TO", "WASTE_RATE", "AMOUNT_RATIO", "PARENT_TYPE", "CONNECT_TYPE", "PARENT_PRODUCT_ID", "CONNECT_PRODUCT_ID");

			$elementsID = $arMergeParentID = $arProductsID = $arProducts = $arConnects = array();

			$rsConnects = $oConnect->GetList(array(), array("PRODUCT_ID" => $id, "STORE_ID" => $SHOP_ID), false, false, $arSelConnect);
			while($arConnect = $rsConnects->Fetch())
			{
				if($arConnect["PARENT_PRODUCT_ID"] == $id && $arConnect["PARENT_TYPE"] == 2
					|| $arConnect["CONNECT_PRODUCT_ID"] == $id && $arConnect["PARENT_TYPE"] == 1)
				{
					$arConnects[] = $arConnect;
					if(!in_array($arConnect["PARENT_ID"], $arProductsID)) $arProductsID[] = $arConnect["PARENT_ID"];
					if(!in_array($arConnect["CONNECT_ID"], $arProductsID)) $arProductsID[] = $arConnect["CONNECT_ID"];
					if($arConnect["CONNECT_PRODUCT_ID"] == $id && $arConnect["PARENT_TYPE"] == 1)
						$arMergeParentID[] = $arConnect["PARENT_ID"];
				}
			}

			if(!empty($arMergeParentID))
			{
				$rsConnects = $oConnect->GetList(array(), array("PARENT_ID" => $arMergeParentID, "!CONNECT_PRODUCT_ID" => $id, "STORE_ID" => $SHOP_ID), false, false, $arSelConnect);
				while($arConnect = $rsConnects->Fetch())
				{
					$arConnects[] = $arConnect;
					if(!in_array($arConnect["PARENT_ID"], $arProductsID)) $arProductsID[] = $arConnect["PARENT_ID"];
					if(!in_array($arConnect["CONNECT_ID"], $arProductsID)) $arProductsID[] = $arConnect["CONNECT_ID"];
				}
			}

			$rsProducts = $oProduct->GetList(array("ID" => "ASC"), array("ID" => $arProductsID, "STORE_ID" => $SHOP_ID), false, false, array("*"));
			while($arProduct = $rsProducts->Fetch())
			{
				$arProduct["LAST_QUANTITY"] = $arProduct["QUANTITY"] = $arProduct["QUANTITY"] > 0 ? $arProduct["QUANTITY"] : 0;
				$arProduct["PROD_QUANTITY"] = $arProduct["PROD_QUANTITY"] > 0 ? $arProduct["PROD_QUANTITY"] : 0;
				$arProducts[$arProduct["ID"]] = $arProduct;
				if($arProduct["PRODUCT_ID"] > 0 && !in_array($arProduct["PRODUCT_ID"], $elementsID)) $elementsID[] = $arProduct["PRODUCT_ID"];
			}

			$arAmount = array();
			$rsProps = CCatalogStoreProduct::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $elementsID, "STORE_ID" => $SHOP_ID), false, false, array("PRODUCT_ID", "AMOUNT"));
			if($arProp = $rsProps->GetNext())
				$arAmount[$arProp["PRODUCT_ID"]] = $arProp["AMOUNT"];
			foreach($arProducts as $prodID => $arProduct)
				$arProducts[$prodID]["CAT_AMOUNT"] = $arAmount[$arProduct["PRODUCT_ID"]] > 0 ? $arAmount[$arProduct["PRODUCT_ID"]] : 0;

			foreach($arConnects as $arConnect) 
				$arProducts[$arConnect["PARENT_ID"]]["CONNECT"][] = $arConnect;

			$arExp = array();

			foreach($arProducts as $prodID => $arProduct) 
			{
				if($arProduct["TYPE"] == 1 && is_array($arProduct["CONNECT"]))
				{
					$prodQuantity = array();
					foreach($arProduct["CONNECT"] as $arConnect) 
					{
						$cQuantity = $arProducts[$arConnect["CONNECT_ID"]]["PROD_QUANTITY"] + $arProducts[$arConnect["CONNECT_ID"]]["CAT_AMOUNT"];
						$pQuantity = (($arConnect["MEASURE"] != $arProduct["MEASURE"] ? $arConnect["MEASURE_TO"] / $arConnect["MEASURE_FROM"] : 1) * $cQuantity - $arExp[$arConnect["CONNECT_ID"]][$arConnect["PARENT_ID"]]) / $arConnect["AMOUNT"] / $arProduct["AMOUNT"];
						$prodQuantity[] = $pQuantity;
					}
					$arProducts[$prodID]["QUANTITY"] = min($prodQuantity);

					foreach($arProduct["CONNECT"] as $arConnect)
						$arExp[$arConnect["CONNECT_ID"]][$arConnect["PARENT_ID"]] += $arConnect["AMOUNT"] * $arProducts[$prodID]["QUANTITY"] / $arProduct["AMOUNT"];
				}
				elseif($arProduct["TYPE"] == 2 && is_array($arProduct["CONNECT"]))
				{
					foreach($arProduct["CONNECT"] as $arConnect) 
					{
						$pQuantity = ($arProduct["MEASURE"] != $arProduct["AMOUNT_MEASURE"] ? $arProduct["MEASURE_TO"] / $arProduct["MEASURE_FROM"] : 1) * ($arProduct["PROD_QUANTITY"] + $arProduct["CAT_AMOUNT"] - $arExp[$arConnect["PARENT_ID"]][$arConnect["CONNECT_ID"]]);
						$cQuantity = $pQuantity * $arConnect["AMOUNT"] / $arProduct["AMOUNT"];
						$arProducts[$arConnect["CONNECT_ID"]]["QUANTITY"] = ($arConnect["MEASURE"] != $arProduct["AMOUNT_MEASURE"] ? $arConnect["MEASURE_FROM"] / $arConnect["MEASURE_TO"] : 1) * $cQuantity;
						$arExp[$arConnect["PARENT_ID"]][$arConnect["CONNECT_ID"]] += $arProduct["QUANTITY"] + $arProduct["CAT_AMOUNT"];
					}
				}
			}

			$oElement = new SMFabricaProduct();

			foreach($arProducts as $prodID => $arProduct)
			{
				if($arProduct["QUANTITY"] <= 0) 
					$arProduct["QUANTITY"] = 0;
				if($arProduct["LAST_QUANTITY"] != $arProduct["QUANTITY"])
				{
					$oElement->Update($arProduct["ID"], array("PRODUCT_ID" => $arProduct["PRODUCT_ID"], "QUANTITY" => $arProduct["QUANTITY"]));
				}
			}
		}
		return false;
	}

	function OnFPUpdateToCalcQuantity($id, $arFields)
	{
		$arFields["PRODUCT_ID"] = IntVal($arFields["PRODUCT_ID"]);
		if($arFields["PRODUCT_ID"] <= 0)
		{
			$rsProducts = SMFabricaProduct::GetList(array("ID" => "ASC"), array("ID" => $id), false, false, array("PRODUCT_ID"));
			if($arProduct = $rsProducts->Fetch())
				$arFields["PRODUCT_ID"] = $arProduct["PRODUCT_ID"];
		}
		if($arFields["PRODUCT_ID"] > 0)
			SMFabricaProduct::calcProductQuantity($arFields["PRODUCT_ID"]);
	}

	function OnFPBeforeDelToCalcQuantity($id)
	{
		global $delFabricaProductID;

		$delFabricaProductID = 0;

		$rsProducts = SMFabricaProduct::GetList(array("ID" => "ASC"), array("ID" => $id), false, false, array("PRODUCT_ID"));
		if($arProduct = $rsProducts->Fetch())
			$delFabricaProductID = $arProduct["PRODUCT_ID"];
	}

	function OnFPDeleteToCalcQuantity($id)
	{
		global $delFabricaProductID;

		if($delFabricaProductID > 0)
			SMFabricaProduct::calcProductQuantity($delFabricaProductID);
	}

	function OnSPUpdateToCalcQuantity($id, $arFields)
	{
		$arFields["PRODUCT_ID"] = IntVal($arFields["PRODUCT_ID"]);
		if($arFields["PRODUCT_ID"] <= 0)
		{
			$rsProducts = CCatalogStoreProduct::GetList(array("ID" => "ASC"), array("ID" => $id), false, false, array("PRODUCT_ID"));
			if($arProduct = $rsProducts->Fetch())
				$arFields["PRODUCT_ID"] = $arProduct["PRODUCT_ID"];
		}
		if($arFields["PRODUCT_ID"] > 0)
			SMFabricaProduct::calcProductQuantity($arFields["PRODUCT_ID"]);
	}

	function OnSPBeforeDelToCalcQuantity($id)
	{
		global $delFabricaProductID;

		$delFabricaProductID = 0;

		$rsProducts = CCatalogStoreProduct::GetList(array("ID" => "ASC"), array("ID" => $id), false, false, array("PRODUCT_ID"));
		if($arProduct = $rsProducts->Fetch())
			$delFabricaProductID = $arProduct["PRODUCT_ID"];
	}

	function OnSPDeleteToCalcQuantity($id)
	{
		global $delFabricaProductID;

		if($delFabricaProductID > 0)
			SMFabricaProduct::calcProductQuantity($delFabricaProductID);
	}
}