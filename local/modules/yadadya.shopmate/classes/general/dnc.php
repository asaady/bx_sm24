<?
IncludeModuleLangFile(__FILE__);

class SMDnc
{
	function Cron($rewrite_all = false)
	{
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
		CModule::IncludeModule("yadadya.shopmate");

		$stores = SMDncDB::getStoreList();

		foreach($stores as $store_id)
		{
			$cashboxes = array();
			$res = SMDncDB::getCashboxByStore($store_id);
			while($ob = $res->GetNext())
				$cashboxes[] = $ob['CASHBOX'];

			$nofile = false;
			foreach($cashboxes as $cashbox)
			{
				$goods_file = $_SERVER['DOCUMENT_ROOT'] . '/dnc/goods_' . $store_id . '_' . $cashbox . '.txt';
				if(!file_exists($goods_file))
				{
					$nofile = true;
					break;
				}
			}

			if(SMDncDB::issetNewStoreProducts($store_id) || $nofile)
			{
				$text = SMDnc::GetStoreGoodsExport($store_id, $rewrite_all);

				if(!empty($text))
				{
					foreach($cashboxes as $cashbox)
					{
						$goods_file = $_SERVER['DOCUMENT_ROOT'] . '/dnc/goods_' . $store_id . '_' . $cashbox . '.txt';
						$goods_file_name = 'goods_' . $store_id . '_' . $cashbox . '.txt';
						SMDncDB::addToCashboxMonitor(array("CASHBOX" => $cashbox, "SHOP" => $store_id, "EXCHANGE_FLAG" => "N", "CASHBOX_ANSWER" => "", "EXCHANGE_ERROR_FLAG" => "N", "EXCHANGE_TIME" => ""));
						// добавили файл выгрузки для каждой кассы
						file_put_contents($goods_file, $text);
						SMDnc::uploadTo1CFTP("93.185.192.56", "Elyon", "El770770", $goods_file, $goods_file_name);
						SMDncDB::markStoerProducts($store_id);
					}
				}
			}
		}

		return "SMDnc::Cron();";
	}
	
	public static function uploadTo1CFTP($server, $username, $password, $local_file, $remote_file){
		// connect to server
		$connection = ftp_connect($server);

		// login
		if (@ftp_login($connection, $username, $password)){
			// successfully connected
		}else{
			return false;
		}

		ftp_put($connection, $remote_file, $local_file, FTP_BINARY);
		ftp_close($connection);
		return true;
	}

	function RewriteCron($store_id = 0)
	{
		SMDncDB::deleteAllProducts();

		$undefinedProducts = \Yadadya\Shopmate\Products::getUndefinedProducts();
		$stores = empty($store_id) ? \Yadadya\Shopmate\Shops::getStores(true) : (array) $store_id;
		foreach ($undefinedProducts as $undefinedProduct)
			foreach ($stores as $store) 
				SMDncDB::addProduct(array("PRODUCT_ID" => $undefinedProduct, "STORE_ID" => $store));

		$parametrs = array(
			'select' => array('SHOP_ID', 'PRODUCT_ID'),
		);
		if (!empty($store_id))
			$parametrs["filter"]["SHOP_ID"] = $store_id;
		$result = \Yadadya\Shopmate\Internals\ShopProductHistoryTable::getList($parametrs);
		while ($row = $result->fetch())
			SMDncDB::addProduct(array("PRODUCT_ID" => $row['PRODUCT_ID'], "STORE_ID" => $row['SHOP_ID']));

		/*$stores = empty($store_id) ? \Yadadya\Shopmate\Shops::getStores(true) : (array) $store_id;
		$parameters = array(
			"select" => array("ID"),
			"filter" => array("IBLOCK_ID" => \Yadadya\Shopmate\Options::getCatalogID())
		);
		$result = \Bitrix\Iblock\ElementTable::getList($parameters);
		while ($row = $result->fetch())
			foreach ($stores as $store) 
				SMDncDB::addProduct(array("PRODUCT_ID" => $row['ID'], "STORE_ID" => $store));*/

		SMDnc::Cron(true);
		
		return "SMDnc::RewriteCron(".$store_id.");";
	}

	function BadCron($store_id = 0)
	{
		self::RewriteCron($store_id);
		return "SMDnc::BadCron(".$store_id.");";
	}

	public static function GetStoreGoodsExport($store_id = 0, $rewrite_all = false)
	{
		$text = "";
		if($store_id > 0)
		{
			CModule::IncludeModule("iblock");
			CModule::IncludeModule("catalog");
		
			// Получаем все сохраненные в таблице id товаров
			$PRODUCT_IDS = array();

			$r = SMDncDB::getStoreProductsList($store_id);
			while($ob = $r->GetNext())
				$PRODUCT_IDS[] = $ob['PRODUCT_ID'];

			//SMDncDB::deleteAllProducts();
		
			if(!empty($PRODUCT_IDS) > 0)
			{
				$total = array();
			
				$REPLACE_WARES = $ADD_SECTIONS = true;
				$CLEAR_DATABASE = $rewrite_all;
				$GET_ALL_PRODUCTS = $rewrite_all;
				$res = SMDnc::GetSections('', $store_id, $total, $GET_ALL_PRODUCTS);
				SMDnc::GetProductsById($PRODUCT_IDS, $store_id, $total);
			
				$record_count = 4;
				if($CLEAR_DATABASE)
				{
					$record_count += 1;
				}
				if($ADD_SECTIONS)
				{
					$record_count += 1;
					foreach($total as $k => $v)
					{
						if(is_numeric($k))
							$record_count += count(explode("\r\n", $total[$k])) - 1;
					}
				}
				if($REPLACE_WARES)
				{
					$record_count += 1;
					$record_count += count(explode("\r\n", $total['PRODUCTS']));
				}
			
				$text .= "!!!DNCUPLOAD\r\n";
				$text .= "#UPLOADED_STATE\r\n";
				$text .= "not loaded\r\n";
				$text .= "#RECORD_COUNT\r\n";
				$text .= $record_count."\r\n";
			
				if($CLEAR_DATABASE)
					$text .= "!!!CLEARWAREDATABASE\r\n";
			
				if($ADD_SECTIONS)
				{
					$text .= "!!!ADDWAREGROUPS\r\n";
					foreach($total as $k => $v)
					{
						if(is_numeric($k))
							$text .= $total[$k];
					}
				}
				if($REPLACE_WARES)
				{
					$text .= "!!!REPLACEWARES\r\n";
					$text .= $total['PRODUCTS'];
				}
			
				$text .= "!!!DNCUPLOAD_END";
			
				$text = htmlspecialchars_decode($text);
			
				$text = mb_convert_encoding($text, "Windows-1251");
			}
		}
		return $text;
	}

	public static function GetProductsById($ids, $store_id, &$total)
	{
		$products = array();

		$res = \Yadadya\Shopmate\BitrixInternals\StoreBarcodeTable::GetList(array(
			"select" => array("ID", "BARCODE", "PRODUCT_ID"),
			"filter" => array("PRODUCT_ID" => $ids)
		));
		while($row = $res->fetch())
			$barcodes[$row["PRODUCT_ID"]][] = $row["BARCODE"];

		$res = \Yadadya\Shopmate\Products::getList(array("select" => array("ID", "NAME", "IBLOCK_SECTION_ID", "AMOUNT" => "CSTORE_PRODUCT.AMOUNT", /*"MEASURE" => "CPRODUCT.MEASURE", "PURCHASING_PRICE" => "SMSTORE_PRODUCT.PURCHASING_PRICE", "PURCHASING_CURRENCY" => "SMSTORE_PRODUCT.PURCHASING_CURRENCY", "CATALOG_GROUP_ID" => "CGROUP.ID", */"PRICE" => "CPRICE.PRICE", /*"CURRENCY" => "CPRICE.CURRENCY", */"END_DATE" => "SMSTORE_PRODUCT.END_DATE", "DNC_TYPE_CODE" => "SMPRODUCT.DNC_TYPE_CODE"), "filter" => array("ID" => $ids, "STORE_ID" => $store_id)));

		while ($row = $res->fetch())
		{
			$row["BARCODE"] = (array) $barcodes[$row["ID"]];
			$row["ARTICLES"] = array();

			$measure = 0;
			foreach ($row["BARCODE"] as $barcode) 
			{
				if (\Yadadya\Shopmate\Products::isWBarcode($barcode))
					$measure = 1;

				$article = substr($barcode, 2, 5);
				if(!in_array($article, $row["ARTICLES"]))
					$row["ARTICLES"][] = $article;
			}

			$products[] = array(
				"ID" => $row['ID'], 
				"NAME" => $row['NAME'],
				"BARCODES" => $row["BARCODE"],
				"CATALOG_STORE_AMOUNT" => $row['AMOUNT'],
				"MEASURE" => $measure
			);

			if($row["ARTICLES"][0] != '' && $row["PRICE"] != '' && $row["PRICE"] > 0)
			{
				$arFields['NAME'] = substr(str_replace(array(";", "\"", "'"), array(",", "", ""), htmlspecialchars_decode(trim(preg_replace("/\s{2,}/"," ",$row['NAME'])))), 0, 100); 
				// внутренний код товара
				if($measure)
					$total['PRODUCTS'] .= intval($row["ARTICLES"][0]) . ';';
				else
					$total['PRODUCTS'] .= $row['ID'] . '00000;';
				// штрихкоды
				$total['PRODUCTS'] .= implode(',', $row["BARCODE"]) . ';';
				// коэффициент, название, текст для чека
				$total['PRODUCTS'] .= '1.000;' . $row['NAME'] . ';' . $row['NAME'] . ';';
				// артикул
				$total['PRODUCTS'] .= $row["ARTICLES"][0] . ';';//$arFields['PROPERTY_CML2_ARTICLE_VALUE'] . ';';
				// цена
				$total['PRODUCTS'] .= $row["PRICE"] . ';';
				// остаток
				$total['PRODUCTS'] .= $row['AMOUNT'] . ';';
				// флаги товара (весовой, разрешена продажа, разрешен возврат, разрешены отрицательные остатки
				// без ввода количества разрешена регистрация, можно списывать остатки, редактировать цену,
				// разрешен ввод количества вручную
				$total['PRODUCTS'] .= $measure.',1,1,1,1,1,1,1;';
				// минимальная цена, срок годности, код родительскго раздела, код налоговой группы, 
				// код набора доп. характеристик, номер секции, код диапазона ограничения продаж по времени
				$total['PRODUCTS'] .= '0;;'.$row["IBLOCK_SECTION_ID"].";0;1;0;0;";
				// код вида товара Дэнси
				$total['PRODUCTS'] .= ";;;".$row["DNC_TYPE_CODE"].";\r\n";
			}
		}

		/*$dnc_type = array();
		$rsDNCType = SMProduct::GetList(array(), array("PRODUCT_ID" => $ids), false, false, array("PRODUCT_ID", "DNC_TYPE_CODE"));
		while($arDNCType = $rsDNCType->Fetch())
			$dnc_type[$arDNCType["PRODUCT_ID"]] = $arDNCType["DNC_TYPE_CODE"];
			
		$arSelect = Array("ID", "NAME", "PROPERTY_CML2_ARTICLE", "IBLOCK_SECTION_ID");
		$arFilter = Array("IBLOCK_ID"=>2, "ACTIVE_DATE"=>"Y", "ID" => $ids);
		$oElement = new CIBlockElement;
		$res = $oElement->GetList(Array(), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$dbBarCode = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $arFields['ID']), false, false, array());

			$barcodes = array();
			$articles = array();

			// переписываем внутренний код с id на артикул, т.к. Дэнси не воспринимает весовой товар
			// извлекаем артикул товара из штрихкода
			// в случае если штрихкодов больше 1, то проверяем артикулы
			// и если нет сходства, то выводим ошибку с указанием товара

			while($arBarCode = $dbBarCode->GetNext())
			{
				$barcodes[] = $arBarCode['BARCODE'];
				$article = substr($arBarCode['BARCODE'], 2, 5);
				if(!in_array($article, $articles))
					$articles[] = $article;
			}

			if(count($articles) > 1)
			{
				//echo 'У товара не может быть два и более артикула.<br/>';
				//echo 'У товара с ID = ' . $arFields['ID'] . ' артикулы: ' . implode(', ', $articles) . '<br/><br/>';
			}
			if(count($articles) == 0)
			{
				//echo 'У товара с ID = ' . $arFields['ID'] . ' нет артикулов<br/><br/>';
			}

			// if(in_array($arFields['CATALOG_MEASURE'], array(5, 6, 10)))
			// 	$measure = 0; // невесовое
			// else
			// 	$measure = 1; // весовое 
			if(in_array(substr($barcodes[0], 0, 2), \Yadadya\Shopmate\Products::getWBarcodePref()) && strlen($barcodes[0]) == 13)
				$measure = 1;
			else
				$measure = 0;

			$products[] = array(
				"ID" => $arFields['ID'], 
				"NAME" => $arFields['NAME'],
				"BARCODES" => $barcodes,
				"CATALOG_STORE_AMOUNT" => $arFields['CATALOG_STORE_AMOUNT_'.$store_id],
				"MEASURE" => $measure
			);

			$dbProductPrice = CPrice::GetListEx(
				array(),
				array("PRODUCT_ID" => $arFields['ID'], "CATALOG_GROUP_CODE" => "SHOP_ID_".$store_id),
				false,
				false,
				array()
			);

			$price = '';

			while($prod_price = $dbProductPrice->GetNext())
			{
				$price = $prod_price['PRICE'];
			}

			if($articles[0] != '' && $price != '' && $price > 0)
			{
				$arFields['NAME'] = substr(str_replace(array(";", "\"", "'"), array(",", "", ""), htmlspecialchars_decode(trim(preg_replace("/\s{2,}/"," ",$arFields['NAME'])))), 0, 100); 
				// внутренний код товара
				if($measure)
					$total['PRODUCTS'] .= intval($articles[0]) . ';';
				else
					$total['PRODUCTS'] .= $arFields['ID'] . '00000;';
				// штрихкоды
				$total['PRODUCTS'] .= implode(',', $barcodes) . ';';
				// коэффициент, название, текст для чека
				$total['PRODUCTS'] .= '1.000;' . $arFields['NAME'] . ';' . $arFields['NAME'] . ';';
				// артикул
				$total['PRODUCTS'] .= $articles[0] . ';';//$arFields['PROPERTY_CML2_ARTICLE_VALUE'] . ';';
				// цена
				$total['PRODUCTS'] .= $price . ';';
				// остаток
				$total['PRODUCTS'] .= $arFields["CATALOG_STORE_AMOUNT_".$store_id] . ';';
				// флаги товара (весовой, разрешена продажа, разрешен возврат, разрешены отрицательные остатки
				// без ввода количества разрешена регистрация, можно списывать остатки, редактировать цену,
				// разрешен ввод количества вручную
				$total['PRODUCTS'] .= $measure.',1,1,1,1,1,1,1;';
				// минимальная цена, срок годности, код родительскго раздела, код налоговой группы, 
				// код набора доп. характеристик, номер секции, код диапазона ограничения продаж по времени
				$total['PRODUCTS'] .= '0;;'.$arFields["IBLOCK_SECTION_ID"].";0;1;0;0;";
				// код вида товара Дэнси
				$total['PRODUCTS'] .= ";;;".$dnc_type[$arFields["ID"]].";\r\n";
			}
		}*/

		return $products;		
	}

	public static function GetProducts($section_id, $store_id, &$total)
	{
		$products = array();

		$arSelect = Array("ID", "NAME", "PROPERTY_CML2_ARTICLE");
		$arFilter = Array("IBLOCK_ID"=>2, "ACTIVE_DATE"=>"Y", "SECTION_ID" => $section_id, ">CATALOG_STORE_AMOUNT_".$store_id => 0);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$dbBarCode = CCatalogStoreBarCode::getList(array(), array("PRODUCT_ID" => $arFields['ID']), false, false, array());

			$barcodes = array();
			$articles = array();

			// переписываем внутренний код с id на артикул, т.к. Дэнси не воспринимает весовой товар
			// извлекаем артикул товара из штрихкода
			// в случае если штрихкодов больше 1, то проверяем артикулы
			// и если нет сходства, то выводим ошибку с указанием товара

			while($arBarCode = $dbBarCode->GetNext())
			{
				$barcodes[] = $arBarCode['BARCODE'];
				$article = substr($arBarCode['BARCODE'], 2, 5);
				if(!in_array($article, $articles))
					$articles[] = $article;
			}

			if(count($articles) > 1)
			{
				//echo 'У товара не может быть два и более артикула.<br/>';
				//echo 'У товара с ID = ' . $arFields['ID'] . ' артикулы: ' . implode(', ', $articles) . '<br/><br/>';
			}
			if(count($articles) == 0)
			{
				//echo 'У товара с ID = ' . $arFields['ID'] . ' нет артикулов<br/><br/>';
			}

			if(in_array($arFields['CATALOG_MEASURE'], array(5, 6, 10)))
				$measure = 0; // невесовое
			else
				$measure = 1; // весовое 

			$products[] = array(
				"ID" => $arFields['ID'], 
				"NAME" => $arFields['NAME'], 
				"BARCODES" => $barcodes,
				"CATALOG_STORE_AMOUNT" => $arFields['CATALOG_STORE_AMOUNT_'.$store_id],
				"MEASURE" => $measure
			);

			//$price = CatalogGetPriceTableEx($arFields['ID']);
			//$price = $price['MATRIX'][19][0]['PRICE'];

			$dbProductPrice = CPrice::GetListEx(
				array(),
				array("PRODUCT_ID" => $arFields['ID'], "CATALOG_GROUP_CODE" => "SHOP_ID_".$store_id),
				false,
				false,
				array()
			);

			$price = '';

			while($prod_price = $dbProductPrice->GetNext())
			{
				$price = $prod_price['PRICE'];
			}

			if($articles[0] != '')
			{
				// внутренний код товара
				if($measure)
					$total['PRODUCTS'] .= intval($articles[0]) . ';';
				else
					$total['PRODUCTS'] .= $arFields['ID'] . '00000;';
				// штрихкоды
				$total['PRODUCTS'] .= implode(',', $barcodes) . ';';
				// коэффициент, название, текст для чека
				$total['PRODUCTS'] .= "1.000;" . $arFields['NAME'] . ';' . $arFields['NAME'] . ';';
				// артикул
				$total['PRODUCTS'] .= $articles[0] . ';';//$arFields['PROPERTY_CML2_ARTICLE_VALUE'] . ';';
				// цена
				$total['PRODUCTS'] .= $price . ';';
				// остаток
				$total['PRODUCTS'] .= $arFields["CATALOG_STORE_AMOUNT_".$store_id] . ';';
				// флаги товара (весовой, разрешена продажа, разрешен возврат, разрешены отрицательные остатки
				// без ввода количества разрешена регистрация, можно списывать остатки, редактировать цену,
				// разрешен ввод количества вручную
				$total['PRODUCTS'] .= $measure.',1,1,1,1,1,1,1;';
				// минимальная цена, срок годности, код родительскго раздела, код налоговой группы, 
				// код набора доп. характеристик, номер секции, код диапазона ограничения продаж по времени
				$total['PRODUCTS'] .= '0;;'.$section_id.";0;1;0;0;\r\n";
			}
		}

		return $products;
	}

	public static function GetSections($id, $store_id, &$total, $with_products = true)
	{
		$res = array();

		if($id)
			$rsSection = CIBlockSection::GetList(array(), array("SECTION_ID" => $id));
		else
			$rsSection = CIBlockSection::GetList(array(), array("DEPTH_LEVEL" => 1));

		while ($arSection = $rsSection->GetNext())
		{
			if($arSection['ID'] == 1) continue; // Убрали раздел "Отсутствующие"

			$total[$arSection['DEPTH_LEVEL']] .= $arSection['ID'] . ';' . $arSection["NAME"] . ';' . $arSection["NAME"] . ';';
			if($arSection['DEPTH_LEVEL'] != 1) $total[$arSection['DEPTH_LEVEL']] .= $arSection['IBLOCK_SECTION_ID'];
			$total[$arSection['DEPTH_LEVEL']] .= ";\r\n";

			$res[] = array(
				"ID" => $arSection['ID'], 
				"NAME" => $arSection['NAME'],
				"DEPTH_LEVEL" => $arSection['DEPTH_LEVEL'],
				"PARENT_SECTION" => $arSection['IBLOCK_SECTION_ID']
			);
		}

		foreach($res as $k => $v)
		{
			$res[$k]['CHILDREN'] = self::GetSections($v['ID'], $store_id, $total, $with_products);
			/*if($with_products)
				$res[$k]['PRODUCTS'] = self::GetProducts($v['ID'], $store_id, $total);*/
		}

		return $res;
	}

	function onElementUpdate($arFields)
	{
		if($arFields["ID"] > 0)
			SMDncDB::addProduct(array("PRODUCT_ID" => $arFields["ID"]));
	}

	function onProductUpdate($id, $arFields)
	{
		if($id > 0)
			SMDncDB::addProduct(array("PRODUCT_ID" => $id));
	}

	function onPriceUpdate($id, $arFields)
	{
		if(empty($arFields["PRODUCT_ID"]) || empty($arFields["CATALOG_GROUP_ID"]))
		{
			$tmpFields = CPrice::GetList(array(), array("ID" => $id), false, false, array("PRODUCT_ID", "CATALOG_GROUP_ID"))->Fetch();
			$arFields["PRODUCT_ID"]	= $tmpFields["PRODUCT_ID"];
			$arFields["CATALOG_GROUP_ID"]	= $tmpFields["CATALOG_GROUP_ID"];
		}

		$tmpFields = CCatalogGroup::GetList(array(), array("ID" => $arFields["CATALOG_GROUP_ID"]), false, false, array("XML_ID"))->Fetch();
		$shopID = substr($tmpFields["XML_ID"], strlen(SMShops::$shop_prefix));

		if($arFields["PRODUCT_ID"] > 0)
			SMDncDB::addProduct(array("PRODUCT_ID" => $arFields["PRODUCT_ID"], "STORE_ID" => $shopID));
	}

	function onAmountUpdate($id, $arFields)
	{
		if(empty($arFields["PRODUCT_ID"]))
			$arFields = CCatalogStoreProduct::GetList(array(), array("ID" => $id), false, false, array("PRODUCT_ID"))->Fetch();
		if($arFields["PRODUCT_ID"] > 0)
			SMDncDB::addProduct(array("PRODUCT_ID" => $arFields["PRODUCT_ID"]));
	}

	function onBarcodeUpdate($id, $arFields)
	{
		if(empty($arFields["PRODUCT_ID"]))
			$arFields = CCatalogStoreBarCode::GetList(array(), array("ID" => $id), false, false, array("PRODUCT_ID"))->Fetch();
		if($arFields["PRODUCT_ID"] > 0)
			SMDncDB::addProduct(array("PRODUCT_ID" => $arFields["PRODUCT_ID"]));
	}
}