<?
IncludeModuleLangFile(__FILE__);

class SMStoreBarcode
{
	function getProductsByBarcode($barcode, $store_id = 0, $key = false)
	{
		$products = array();
		$wpb = array("90", "23", "26");
		if(CModule::IncludeModule("catalog") && !empty($barcode))
		{
			$barcode = (array) $barcode;
			$arBarcodeFilter = array();
			foreach($barcode as $kb => $bc)
			{
				$arBarcodeFilter[] = $bc;
				if(in_array(substr($bc, 0, 2), $wpb) && strlen($bc) == 13)
					foreach ($wpb as $fb)
						$arBarcodeFilter[] = $fb.substr($bc, 2, -6)."00000_";
			}
			$rsBarCode = CCatalogStoreBarCode::getList(array(), array("~BARCODE" => $arBarcodeFilter), false, false, array("PRODUCT_ID", "BARCODE"));
			while($arBarCode = $rsBarCode->Fetch())
			{
				$bcc = $arBarCode["BARCODE"];
				if(!in_array($bcc, $barcode))
					foreach($barcode as $kb => $bc)
						if(in_array(substr($bc, 0, 2), $wpb) && strlen($bc) == 13)
							foreach ($wpb as $fb)
								if(stripos($bc, $fb.substr($bc, 2, -6)."00000") === 0)
									$bcc = $bc;
				$products[$bcc] = $arBarCode["PRODUCT_ID"];
			}
		}
		return $key ? $products : array_values($products);
	}

	function getUndefinedBarcodes()
	{
		return array("123456", "4007704007706",  "7077070770778", "5007705007704", "6007706007702");
	}

	function getUndefinedProducts()
	{
		return self::getProductsByBarcode(self::getUndefinedBarcodes());
	}
}