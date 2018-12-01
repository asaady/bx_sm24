<?
class SMDncDB
{
	static private $UPDATED_PRODUCTS_TABLE_NAME = "b_sm_dnc_updated_products";
	static private $UPDATED_CASHBOX_TABLE_NAME = "b_sm_dnc_cashbox_exchange_monitor";
	static private $CASHBOX_TABLE_NAME = "b_sm_dnc_cashboxes";

	public static function addProduct($arFields)
	{
		global $DB;

		if($arFields["PRODUCT_ID"] > 0)
		{
			if($arFields["STORE_ID"] > 0)
			{
				$arInsert = $DB->PrepareInsert(self::$UPDATED_PRODUCTS_TABLE_NAME, $arFields);
				$strSql = "insert into ".self::$UPDATED_PRODUCTS_TABLE_NAME." (".$arInsert[0].") values(".$arInsert[1].")";
			}
			else
			{
				$strSql = "insert into `".self::$UPDATED_PRODUCTS_TABLE_NAME."` (`PRODUCT_ID`, `STORE_ID`)
					select `PRODUCT_ID` , `STORE_ID` from `b_catalog_store_product` where `PRODUCT_ID`=\"".intval($arFields["PRODUCT_ID"])."\" group by `PRODUCT_ID` , `STORE_ID`";
			}

			$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);

			if(!$res) return false;

			return true;
		}
		return false;
	}

	/*public static function updateProduct($id, $arFields)
	{
		$id = intval($id);
		
		global $DB;

		$strUpdate = $DB->PrepareUpdate(self::$UPDATED_PRODUCTS_TABLE_NAME, $arFields);

		if (!empty($strUpdate))
		{
			$strSql = "UPDATE ".self::$UPDATED_PRODUCTS_TABLE_NAME." SET ".$strUpdate." WHERE PRODUCT_ID = ".$id;
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
			else
				return true;
		}
		return false;
	}

	public static function deleteProduct($id)
	{
		global $DB;
		$id = intval($id);

		if($id > 0)
		{
			if($DB->Query("DELETE FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME." WHERE PRODUCT_ID = ".$id, true))
				return true;
		}
		return false;
	}*/

	public static function deleteAllProducts()
	{
		global $DB;

		if($DB->Query("DELETE FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME, true))
		{
			return true;
		}
		return false;
	}

	public static function markStoerProducts($store_id)
	{
		global $DB;

		if($DB->Query("UPDATE ".self::$UPDATED_PRODUCTS_TABLE_NAME." SET EXPORT=\"Y\" WHERE STORE_ID=\"".intval($store_id)."\"", true))
		{
			return true;
		}
		return false;
	}

	public static function deleteStoreProducts($store_id)
	{
		global $DB;

		if($DB->Query("DELETE FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME." WHERE EXPORT=\"Y\" AND STORE_ID=\"".intval($store_id)."\"", true))
		{
			return true;
		}
		return false;
	}

	public static function getProductsList()
	{
		global $DB;

		$strSql = "SELECT * FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	public static function issetNewStoreProducts($store_id = 0)
	{
		global $DB;

		$strSql = "SELECT COUNT(ID) as CNT FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME." WHERE `STORE_ID`=\"".intval($store_id)."\" AND `EXPORT`=\"N\" GROUP BY `PRODUCT_ID`";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		$iCnt = 0;
		if ($ar_res = $res->Fetch())
			$iCnt = intval($ar_res["CNT"]);
		if($iCnt > 0) return true;
		return false;
	}

	public static function getStoreProductsList($store_id = 0)
	{
		global $DB;

		$strSql = "SELECT * FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME." WHERE `STORE_ID`=\"".intval($store_id)."\" GROUP BY `PRODUCT_ID`";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	public static function getStoreList()
	{
		global $DB;

		$stores = array();

		$strSql = "SELECT `STORE_ID` FROM ".self::$UPDATED_PRODUCTS_TABLE_NAME." GROUP BY `STORE_ID`";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		while($store = $res->Fetch())
			$stores[] = $store["STORE_ID"];

		return $stores;
	}

	// работа с таблицей мониторинга обмена касс
	public static function addToCashboxMonitor($arFields)
	{
		global $DB;

		$arInsert = $DB->PrepareInsert(self::$UPDATED_CASHBOX_TABLE_NAME, $arFields);

		$strSql = "insert into ".self::$UPDATED_CASHBOX_TABLE_NAME." (".$arInsert[0].") values(".$arInsert[1].")";

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		
		if(!$res)
			return false;

		$lastId = intval($DB->LastID());
		
		return $lastId;
	}

	public static function updateCashbox($arFields)
	{	
		global $DB;

		$strUpdate = $DB->PrepareUpdate(self::$UPDATED_CASHBOX_TABLE_NAME, $arFields);

		if (!empty($strUpdate))
		{
			$strSql = "UPDATE ".self::$UPDATED_CASHBOX_TABLE_NAME." SET ".$strUpdate." WHERE CASHBOX = ".$arFields['CASHBOX']." AND SHOP = ".$arFields['SHOP'];
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
			else
				return true;
		}
		return false;
	}

	public static function deleteFromCashboxMonitor($arFields)
	{
		global $DB;
		
		$strSql = "SELECT ID FROM ".self::$UPDATED_CASHBOX_TABLE_NAME." WHERE CASHBOX = ".$arFields['CASHBOX'].
			" AND SHOP = ".$arFields['SHOP'];

		if($res = $DB->Query($strSql, false, $err_mess.__LINE__)){
			$res = $res->GetNext();
			$id = $res['ID'];
		}
		else{
			return false;
		}
		
		$id = intval($id);
		if($id > 0)
		{
			if($DB->Query("DELETE FROM ".self::$UPDATED_CASHBOX_TABLE_NAME." WHERE ID = ".$id, true))
				return true;
		}
		return false;
	}

	public static function getCashboxListOfMonitor($cashbox)
	{
		global $DB;

		$strSql = "SELECT * FROM ".self::$UPDATED_CASHBOX_TABLE_NAME." WHERE CASHBOX = ".$cashbox;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	public static function GetTasksForCurrentCashbox($cashbox, $store)
	{
		global $DB;

		$strSql = "SELECT * FROM ".self::$UPDATED_CASHBOX_TABLE_NAME." WHERE CASHBOX = ".$cashbox;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res->GetNext();	
	}

	public static function RemoveTasksForCurrentCashbox($cashbox, $store)
	{
		global $DB;

		if($DB->Query("DELETE FROM ".self::$UPDATED_CASHBOX_TABLE_NAME." WHERE CASHBOX = ".$cashbox." AND SHOP = ".$store, true))
			return true;
	}

	public static function addCashbox($arFields)
	{
		global $DB;

		$arInsert = $DB->PrepareInsert(self::$CASHBOX_TABLE_NAME, $arFields);

		$strSql = "insert into ".self::$CASHBOX_TABLE_NAME." (".$arInsert[0].") values(".$arInsert[1].")";

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		
		if(!$res)
			return false;

		$lastId = intval($DB->LastID());
		
		return $lastId;
	}

	public static function getCashboxByNumberAndStore($cashbox, $store)
	{
		global $DB;

		$strSql = "SELECT * FROM " . self::$CASHBOX_TABLE_NAME . " WHERE CASHBOX = " . $cashbox . " AND SHOP = " . $store;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res->GetNext();
	}

	public static function getCashboxByStore($store)
	{
		global $DB;

		$strSql = "SELECT * FROM " . self::$CASHBOX_TABLE_NAME . " WHERE SHOP = " . $store;
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}
}