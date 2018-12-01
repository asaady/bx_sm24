<?
IncludeModuleLangFile(__FILE__);

class SMProductions
{
	function CustomSaveName()
	{
		/*if($_REQUEST["CURRENT_SAVE"] && $REQUEST["CODE"] > 0)
		{
			print_p($_REQUEST);
			die();
		}*/
	}
	function CustomSave($arCustomFields)
	{
		global $USER;
		$userId = intval($USER->GetID());
		$PRODUCT_ID = $arCustomFields["ID"];
		
		if($PRODUCT_ID/* && $_REQUEST["CURRENT_SAVE"] == "Y"*/ && CModule::IncludeModule("catalog"))
		{
			if(isset($_REQUEST["PROPERTY"]["COMMENT"]) && empty($_REQUEST["PROPERTY"]["COMMENT"][0]))
			{
				global $APPLICATION;
				$APPLICATION->throwException(GetMessage("SM_COMMENT_ERROR"));
				return false;
			}

			//$arCustomFields = array("ID" => $PRODUCT_ID);
			//save barcodes
			$barcode_request = $_REQUEST["PROPERTY"]["CAT_BARCODE"];
			$barcodes = array();
			if(!empty($barcode_request))
			{
				if(is_array($barcode_request))
				{
					foreach($barcode_request as $bcode)
						if(!empty($bcode))
							$barcodes[] = $bcode;
				}
				else $barcodes[] = $barcode_request;
			}
			$barcodes = array_unique($barcodes);
			if(!empty($barcodes))
			{
				$arCurrentBarcodes = array();
				$dbResultList = CCatalogStoreBarCode::getList(array("ID" => "ASC"), array("PRODUCT_ID" => $PRODUCT_ID));
				while($arFields = $dbResultList->Fetch())
					$arCurrentBarcodes[] = $arFields;
				if(!empty($arCurrentBarcodes))
				{
					foreach($arCurrentBarcodes as $keyCurrentBarcode => $arCurrentBarcode) 
						if($keyCurrentBarcode < count($barcodes))
							CCatalogStoreBarCode::Update($arCurrentBarcode["ID"], array_merge($arCurrentBarcode, array("BARCODE" => $barcodes[$keyCurrentBarcode], "MODIFIED_BY" => $userId)));
						else
							CCatalogStoreBarCode::Delete($arCurrentBarcode["ID"]);
				}
				if(count($barcodes) > $dbResultList->SelectedRowsCount())
					for($keyBarcode = $dbResultList->SelectedRowsCount(); $keyBarcode < count($barcodes); $keyBarcode++)
						CCatalogStoreBarCode::Add(array(
							"BARCODE" => trim($barcodes[$keyBarcode]),
							"PRODUCT_ID" => $PRODUCT_ID,
							"CREATED_BY" => $userId,
							"MODIFIED_BY" => $userId,
							"STORE_ID" => 0,
						));
			}

			//save alccodes
			if(CModule::IncludeModule("yadadya.shopmate") && class_exists("SMEGAISAlcCode"))
			{
				$alccode_request = $_REQUEST["PROPERTY"]["CAT_ALCCODE"];
				$alccodes = array();
				if(!empty($alccode_request))
				{
					if(is_array($alccode_request))
					{
						foreach($alccode_request as $bcode)
							if(!empty($bcode))
								$alccodes[] = $bcode;
					}
					else $alccodes[] = $alccode_request;
				}
				$alccodes = array_unique($alccodes);
				if(!empty($alccodes))
				{
					$arCurrentAlccodes = array();
					$dbResultList = SMEGAISAlcCode::getList(array("ID" => "ASC"), array("PRODUCT_ID" => $PRODUCT_ID));
					while($arFields = $dbResultList->Fetch())
						$arCurrentAlccodes[] = $arFields;
					if(!empty($arCurrentAlccodes))
					{
						foreach($arCurrentAlccodes as $keyCurrentAlccode => $arCurrentAlccode) 
							if($keyCurrentAlccode < count($alccodes))
								SMEGAISAlcCode::Update($arCurrentAlccode["ID"], array_merge($arCurrentAlccode, array("ALCCODE" => $alccodes[$keyCurrentAlccode], "MODIFIED_BY" => $userId)));
							else
								SMEGAISAlcCode::Delete($arCurrentAlccode["ID"]);
					}
					if(count($alccodes) > $dbResultList->SelectedRowsCount())
						for($keyAlccode = $dbResultList->SelectedRowsCount(); $keyAlccode < count($alccodes); $keyAlccode++)
							SMEGAISAlcCode::Add(array(
								"ALCCODE" => trim($alccodes[$keyAlccode]),
								"PRODUCT_ID" => $PRODUCT_ID,
								"CREATED_BY" => $userId,
								"MODIFIED_BY" => $userId,
								"STORE_ID" => 0,
							));
				}
			}

			//save measure & purchasing price
			$arProductUpdate = array("PURCHASING_CURRENCY" => "RUB");
			if(isset($_REQUEST["PROPERTY"]["CAT_MEASURE"]))
				$arProductUpdate["MEASURE"] = $_REQUEST["PROPERTY"]["CAT_MEASURE"];
			if(isset($_REQUEST["PROPERTY"]["CAT_PURCHASING_PRICE"][0]))
				$arProductUpdate["PURCHASING_PRICE"] = $_REQUEST["PROPERTY"]["CAT_PURCHASING_PRICE"][0];
			if(!empty($arProductUpdate))
			{
				$arProductUpdate["ID"] = $PRODUCT_ID;
				CCatalogProduct::Add($arProductUpdate);
			}

			//save amount
			$amount_request = $_REQUEST["PROPERTY"]["CAT_AMOUNT"][0];
			if(isset($amount_request) && CModule::IncludeModule("yadadya.shopmate"))
			{
				$price_name = SMShops::getUserPriceName();
				if(stripos($price_name, SMShops::$shop_prefix) !== false)
				{
					$shopID = substr($price_name, strlen(SMShops::$shop_prefix));
					$arStoreProductFields = array(
						"PRODUCT_ID" => $PRODUCT_ID,
						"STORE_ID" => $shopID,
						"AMOUNT" => $amount_request,
					);
					CCatalogStoreProduct::UpdateFromForm($arStoreProductFields);
				}
			}

			//save price
			$price_request = $_REQUEST["PROPERTY"]["CAT_PRICE"][0];
			if(isset($price_request) && CModule::IncludeModule("yadadya.shopmate"))
			{
				$arFields = Array(
					"PRODUCT_ID" => $PRODUCT_ID,
					"CATALOG_GROUP_ID" => SMShops::getUserPrice(),
					"PRICE" => $price_request,
					"CURRENCY" => "RUB",
				);
				$res = CPrice::GetList(array(), array("PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => SMShops::getUserPrice()));
				if ($arr = $res->Fetch())
					CPrice::Update($arr["ID"], $arFields);
				else
					CPrice::Add($arFields);
			}

			//save shelf life
			$shelf_life_request = $_REQUEST["PROPERTY"]["CAT_SHELF_LIFE"][0];
			$dnc_type_code_request = $_REQUEST["PROPERTY"]["CAT_DNC_TYPE_CODE"][0];
			if((isset($shelf_life_request) || isset($dnc_type_code_request)) && CModule::IncludeModule("yadadya.shopmate"))
			{
				$arFields = Array(
					"PRODUCT_ID" => $PRODUCT_ID,
				);
				if(isset($shelf_life_request))
					$arFields["SHELF_LIFE"] = $shelf_life_request;
				if(isset($dnc_type_code_request))
					$arFields["DNC_TYPE_CODE"] = $dnc_type_code_request;
				$res = SMProduct::GetList(array(), array("PRODUCT_ID" => $PRODUCT_ID));
				if ($arr = $res->Fetch())
					SMProduct::Update($PRODUCT_ID, $arFields);
				else
					SMProduct::Add($arFields);
			}
		}
	}
}