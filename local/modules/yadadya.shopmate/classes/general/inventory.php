<?
class SMInventory
{
	static private $INVENTORY_TABLE_NAME = "b_sm_inventory";
	static private $PRODUCT_INVENTORY_TABLE_NAME = "b_sm_inventory_products";
	static private $PRIMARY_PRODUCTS_INVENTORY_TABLE = "b_sm_primary_inventory_products";
 	static private $PRIMARY_PRODUCTS_INVENTORY_LOG = "b_sm_primary_inventory_exchange_log";

 	public static function hasPrimaryInventoryFlag()
 	{
 		global $DB;

		$strSql = "SELECT * FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_LOG." WHERE SHOP_ID = ".SMShops::getUserShop() . " AND EXCHANGE_DATETIME IS NULL";
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);

	    if($res->SelectedRowsCount() > 0)
	    	return true;
	    else
	    	return false;
 	}

 	public static function addPrimaryInventoryProductLog($arFields)
 	{
 		global $DB;

 		$arFields["SHOP_ID"] = SMShops::getUserShop();

		$arInsert = $DB->PrepareInsert(self::$PRIMARY_PRODUCTS_INVENTORY_LOG, $arFields);

		$strSql = "insert into ".self::$PRIMARY_PRODUCTS_INVENTORY_LOG." (".$arInsert[0].") values(".$arInsert[1].")";

		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());
		return $lastId;	
 	}

 	public static function removePrimaryInventoryProductLog($id)
 	{
 		global $DB;

	    $strSql = "SELECT * FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_LOG." WHERE ID = ".$id;
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res;
 	}

 	public static function getPrimaryInventoryProductsLog()
 	{
 		global $DB;

	    $strSql = "SELECT * FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_LOG." WHERE SHOP_ID = ".SMShops::getUserShop();
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res;
 	}

	public static function addInventory($arFields)
	{
		global $DB;

		$arFields["SHOP_ID"] = SMShops::getUserShop();

		$arInsert = $DB->PrepareInsert(self::$INVENTORY_TABLE_NAME, $arFields);

		$strSql = "insert into ".self::$INVENTORY_TABLE_NAME." (".$arInsert[0].") values(".$arInsert[1].")";

		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());
		return $lastId;
	}

	public static function addPrimaryInventoryProduct($arFields)
	{
		global $DB;

		$arFields['SHOP_ID'] = SMShops::getUserShop();

		$arInsert = $DB->PrepareInsert(self::$PRIMARY_PRODUCTS_INVENTORY_TABLE, $arFields);

		$strSql = "insert into " . self::$PRIMARY_PRODUCTS_INVENTORY_TABLE . " (" . $arInsert[0] . ") values (" . $arInsert[1] . ")";
		
		$res = $DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());
		return $lastId;
	}

	public static function deletePrimaryInventoryProduct($id)
	{
		global $DB;
		$id = intval($id);

		if($id > 0)
		{
			if($DB->Query("DELETE FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_TABLE." WHERE ID = ".$id, true))
				return true;
		}
		return false;
	}

	public static function getPrimaryInventoryProductList()
	{
		global $DB;

	    $strSql = "SELECT * FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_TABLE." WHERE SHOP_ID = ".SMShops::getUserShop();
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res;
	}

	public static function getPrimaryInventoryProductsLogById($id)
 	{
 		global $DB;

	    $strSql = "SELECT * FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_LOG." WHERE SHOP_ID = ".SMShops::getUserShop() . " AND PRODUCT_ID = ".$id;
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res;
 	}

	public static function savePrimaryInventory($date)
	{
		global $DB;

		$arFields['ACCEPT_DATE'] = $date;

		$strUpdate = $DB->PrepareUpdate(self::$PRIMARY_PRODUCTS_INVENTORY_TABLE, $arFields);

		if (!empty($strUpdate))
		{
			$strSql = "UPDATE ".self::$PRIMARY_PRODUCTS_INVENTORY_TABLE." SET ".$strUpdate;
			
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
			else
				return true;
		}
		return false;
	}

	public static function updateInventory($id, $arFields)
	{
		$id = intval($id);
		
		global $DB;

		$strUpdate = $DB->PrepareUpdate(self::$INVENTORY_TABLE_NAME, $arFields);

		if (!empty($strUpdate))
		{
			$strSql = "UPDATE ".self::$INVENTORY_TABLE_NAME." SET ".$strUpdate." WHERE ID = ".$id;
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
			else
				return true;
		}
		return false;
	}

	public static function deleteInventory($id)
	{
		global $DB;
		$id = intval($id);

		if($id > 0)
		{
			// remove all products from inventory
			if($DB->Query("DELETE FROM ".self::$PRODUCT_INVENTORY_TABLE_NAME." WHERE INVENTORY_ID = ".$id, true))
			{
				// remove inventory
				if($DB->Query("DELETE FROM ".self::$INVENTORY_TABLE_NAME." WHERE ID = ".$id, true))
					return true;
			}
		}
		return false;
	}

	public static function deleteAllInventoryProducts($id)
	{
		global $DB;
		$id = intval($id);

		if($id > 0)
		{
			// remove all products from inventory
			if($DB->Query("DELETE FROM ".self::$PRODUCT_INVENTORY_TABLE_NAME." WHERE INVENTORY_ID = ".$id, true))
			{
				return true;
			}
		}
		return false;
	}

	public static function deleteAllPrimaryInventoryProducts()
	{
		global $DB;

		// remove all products from primary inventory
		if($DB->Query("DELETE FROM ".self::$PRIMARY_PRODUCTS_INVENTORY_TABLE . " WHERE SHOP_ID = " . SMShops::getUserShop(), true))
		{
			return true;
		}

		return false;
	}

	public static function getInventoryById($id)
	{
	    global $DB;

	    $strSql = "SELECT * FROM ".self::$INVENTORY_TABLE_NAME." WHERE ID = ".$id;
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res->GetNext();
	}

	public static function getInventoryList()
	{
	    global $DB;

	    $strSql = "SELECT * FROM ".self::$INVENTORY_TABLE_NAME . " WHERE SHOP_ID = " . SMShops::getUserShop();
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res;
	}

	public static function addInventoryProduct($arFields)
	{
		global $DB;

		$arInsert = $DB->PrepareInsert(self::$PRODUCT_INVENTORY_TABLE_NAME, $arFields);

		$strSql = "insert into ".self::$PRODUCT_INVENTORY_TABLE_NAME." (".$arInsert[0].") values(".$arInsert[1].")";

		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		
		if(!$res)
			return false;

		$lastId = intval($DB->LastID());
		
		return $lastId;
	}

	public static function updateInventoryProduct($arFields)
	{
		global $DB;

		$strUpdate = $DB->PrepareUpdate(self::$PRODUCT_INVENTORY_TABLE_NAME, $arFields);

		if (!empty($strUpdate) || empty($arFields['PRODUCT_ID']) || empty($arFields['INVENTORY_ID']))
		{
			// get inventory_product id
			$strSql = "SELECT ID FROM ".self::$PRODUCT_INVENTORY_TABLE_NAME." WHERE PRODUCT_ID = ".$arFields['PRODUCT_ID'].
				" AND INVENTORY_ID = ".$arFields['INVENTORY_ID'];

	    	if($res = $DB->Query($strSql, false, $err_mess.__LINE__)){
	    		$res = $res->GetNext();
	    		$id = $res['ID'];
	    	}
	    	else{
	    		return false;
	    	}

			$strSql = "UPDATE ".self::$PRODUCT_INVENTORY_TABLE_NAME." SET ".$strUpdate." WHERE ID = ".$id;

			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
			else
				return true;
		}
		return false;
	}

	public static function deleteInventoryProduct($arFields)
	{
		global $DB;
		// get inventory_product id
		$strSql = "SELECT ID FROM ".self::$PRODUCT_INVENTORY_TABLE_NAME." WHERE PRODUCT_ID = ".$arFields['PRODUCT_ID'].
			" AND INVENTORY_ID = ".$arFields['INVENTORY_ID'];

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
			if($DB->Query("DELETE FROM ".self::$PRODUCT_INVENTORY_TABLE_NAME." WHERE ID = ".$id, true))
				return true;
		}
		return false;
	}

	public static function getInventoryProductList($inventoryId)
	{
	    global $DB;

	    $strSql = "SELECT * FROM ".self::$PRODUCT_INVENTORY_TABLE_NAME." WHERE INVENTORY_ID = ".$inventoryId;
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
	    return $res;
	}

	public static function HasPrimaryInventory()
	{
	    global $DB;

	    $strSql = "SELECT * FROM ".self::$INVENTORY_TABLE_NAME." WHERE PRIMARY_INVENTORY = 'Y'";
	    $res = $DB->Query($strSql, false, $err_mess.__LINE__);
		if($res->SelectedRowsCount() > 0)
	    	return 'Y';
		else 
			return 'N';
	}

}