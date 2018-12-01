<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
	if(!empty($_REQUEST["file_name"]))
	{
		$file_name = $_REQUEST["file_name"];
		$data_max = 150;
		unlink($file_name.".csv");
?>
<script type="text/javascript">
	if(window.jQuery) {
		$.ajax({
			//url: '<?=$file_name?>.html',
			data: {
				open_file: '<?=$file_name?>.html'
			},
			success: function(data) {
				var $document = $(data),
					find_left = {
						articul: '28',
						name: '99',
						measure: '311',
						barcode: '337',
						ratio: '452',
						property: '461',
					},
					section = 'other',
					arElem = {},
					tmpResult = [],
					arResult = [],
					data_max = <?=$data_max?>;
				//$result = $document.filter('[id="f6"][style*="left:24pt"],[id="f5"][style*="left:28pt"],[id="f5"][style*="left:99pt"],[id="f5"][style*="left:311pt"],[id="f6"][style*="left:337pt"],[id="f6"][style*="left:452pt"],[id="f6"][style*="left:461pt"]');
				$result = $document.filter('[id^=f]');
				$('.import_load').show();
				$result.each(function() {
					var $block = $(this),
						id = $block.attr('id'),
						style = $block.attr('style');
					if(id == "f6" && style.indexOf("left:24pt") > -1) {
						section = $block.text().trim();
					}
					else if(id == "f5") {
						$.each(find_left, function(field, left) {
							if(style.indexOf("left:"+left+"pt") > -1 || (field == "measure" && style.indexOf("left:313pt") > -1)) {
								if(field == "articul") {
									if(Object.keys(arElem).length > 0) {
										tmpResult.push(arElem);
										if(tmpResult.length >= data_max) {
											arResult.push(tmpResult);
											tmpResult = [];
										}
									}
									arElem = {section: section}
								}
								if(arElem[field] != undefined)
									arElem[field] += ' ' + $block.text().trim();
								else
									arElem[field] = $block.text().trim();
							}
						});
					}
				});
				if (tmpResult.length > 0) {
					arResult.push(tmpResult);
					tmpResult = [];
				}

				var start = arResult.length, finish = 0;
				for (var i = 0; i < start; i++) {
					window.setTimeout(function() {
						tmpResult = arResult.shift();
						$.ajax({
							type: 'POST',
							data: {
								save_ramen: 'Y',
								data: tmpResult
							},
							success: function(data) {
								finish++;
								if(start <= finish)
								{
									$('.import_load').hide();
									alert('That\'s all!!!');
								}
							}
						});
					}, i*2000);
				}
			}
		});
	}
</script>
<div class="import_load text-center" style="display:none;"><img src="<?=SITE_TEMPLATE_PATH?>/static/images/loaders/loader7.gif"></div>
<?
	}
	elseif($_REQUEST["save_ramen"] == "Y")
	{
		global $APPLICATION;
		$APPLICATION->RestartBuffer();
		$fp = fopen($file_name.".csv", "a");
		foreach ($_REQUEST["data"] as $arElem) 
		{
			fputcsv($fp,  array(
				$arElem["section"],
				$arElem["articul"],
				addslashes($arElem["name"]),
				$arElem["measure"],
				$arElem["barcode"],
				$arElem["ratio"],
				$arElem["property"],
			));
		}
		fclose($fp);
		die();
	}
	elseif(!empty($_REQUEST["open_file"]))
	{
		global $APPLICATION;
		$APPLICATION->RestartBuffer();
		echo iconv('CP1251', 'UTF-8', file_get_contents($_REQUEST["open_file"]));
		die();
	}
	elseif(!empty($_REQUEST["import_file"]) && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
	{
		$file_name = $_REQUEST["import_file"];
		$file = $file_name.".csv";
		$arResult = array();
		$fElements = $fSections = $fBarcodes = array();
		$sElements = $sSections = $sBarcodes = array();

		if (($handle = fopen($file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
		    {
		    	//$data[0] = substr($data[0], strpos($data[0], ". ") + 2);
		    	if(!empty($data[0]) && !in_array($data[0], $fSections)) $fSections[] = $data[0];
		    	$barcodes = array();
		    	if(!empty($data[4]))
		    		$barcodes = explode(" ", $data[4]);
		    		foreach ($barcodes as $barcode) 
		    			if(!in_array($barcode, $fBarcodes))
		    				$fBarcodes[] = $barcode;
		    	if(!empty($data[2]) && !in_array($data[2], $fElements)) $fElements[] = $data[2];
		    	$arResult[] = array(
					"section" => $data[0],
					"articul" => $data[1],
					"name" => $data[2],
					"measure" => $data[3],
					"barcode" => $barcodes,
					"ratio" => $data[5],
					"property" => $data[6],
		    	);
		    }
		    fclose($handle);
		}

		if(!empty($fSections))
		{
			$oSection = new CIBlockSection;
			$rsSections = $oSection->GetList(Array(), array("NAME" => $fSections), false, array("ID", "NAME"));
			while($arSection = $rsSections->Fetch())
				$sSections[$arSection["NAME"]] = $arSection["ID"];
			foreach ($fSections as $section) 
			{
				if($sSections[$section] <= 0)
				{
					$ID = $oSection->Add(Array(
						"ACTIVE" => "Y",
						"IBLOCK_SECTION_ID" => 195,
						"IBLOCK_ID" => 2,
						"NAME" => $section,
					));
					$sSections[$section] = $ID;
				}
			}
		}

		if(!empty($fBarcodes))
		{
			$oBarcode = new CCatalogStoreBarCode;
			$rsBarcodes = $oBarcode->GetList(Array(), array("BARCODE" => $fBarcodes), false, false, array("PRODUCT_ID", "BARCODE"));
			while($arBarcode = $rsBarcodes->Fetch())
				$sBarcodes[$arBarcode["BARCODE"]] = $arBarcode["PRODUCT_ID"];
		}

		if(!empty($fElements))
		{
			$oElement = new CIBlockElement;
			$rsElements = $oElement->GetList(Array(), array("NAME" => $fElements), false, false, array("ID", "NAME"));
			while($arElement = $rsElements->Fetch())
				$sElements[$arElement["NAME"]] = $arElement["ID"];
		}

		if(!empty($arResult))
		{
			$_REQUEST["CURRENT_SAVE"] = "Y";
			foreach($arResult as $arElement) 
			{
				unset($_REQUEST["PROPERTY"]["CAT_BARCODE"]);
				foreach ($arElement["barcode"] as $barcode) 
					if($sBarcodes[$barcode] > 0)
					{
						$arElement["id"] = $sBarcodes[$barcode];
						break;
					}
				if($sElements[$arElement["name"]] > 0)
					$arElement["id"] = $sElements[$arElement["name"]];
				if($arElement["id"] > 0)
				{
					$oBarcode = new CCatalogStoreBarCode;
					$barcodes = array();
					$rsBarcodes = $oBarcode->getList(array("ID" => "ASC"), array("PRODUCT_ID" => $arElement["id"]), false, false, array("PRODUCT_ID", "BARCODE"));
					while($arBarcode = $rsBarcodes->Fetch())
						$barcodes[] = $arBarcode["BARCODE"];
					$update = false;
					foreach ($arElement["barcode"] as $barcode) 
						if(!in_array($barcode, $barcodes))
						{
							$update = true;
							$barcodes[] = $barcode;
						}
					if($update)
					{
						$_REQUEST["PROPERTY"]["CAT_BARCODE"] = $barcodes;
						$arFields = array("ID" => $arElement["id"]);
						SMProductions::CustomSave($arFields);
					}
				}
				else
				{
					$oElement = new CIBlockElement;
					$arLoadProductArray = Array(
						"MODIFIED_BY" => 1,
						"IBLOCK_SECTION_ID" => $sSections[$arElement["section"]],
						"IBLOCK_ID" => 2,
						"NAME" => $arElement["name"],
						"PROPERTY_VALUES" => array("CML2_ARTICLE" => $arElement["articul"]),
						"ACTIVE" => "Y",
					);
					$_REQUEST["PROPERTY"]["CAT_BARCODE"] = $arElement["barcode"];
					$_REQUEST["PROPERTY"]["CAT_MEASURE"] = $arElement["measure"] == "кг" ? 4 : 5;
					$arElement["id"] = $oElement->Add($arLoadProductArray);
					$arFields = array("ID" => $arElement["id"]);
					SMProductions::CustomSave($arFields);
				}
			}
		}
	}


	/*if(class_exists("phpQuery"))
	{
		$find_left = array(
			//"section" => "24",
			"articul" => "28",
			"name" => "99",
			"measure" => "311",
			"ratio" => "452",
			"property" => "461",
		);
		$section = "other";
		$tmpResult = array();
		$arElem = array();
		$file = file_get_contents("2.html");
		$document = phpQuery::newDocument(iconv('CP1251', 'UTF-8', $file));
		//$result = $document->find("[id=f6][style*=left:24pt],[id=f5][style*=left:28pt],[id=f5][style*=left:99pt],[id=f5][style*=left:311pt],[id=f6][style*=left:337pt],[id=f6][style*=left:452pt],[id=f6][style*=left:461pt]");
		$result = $document->find("[id^=f]");
		foreach ($result as $cell) 
		{
			$block = pq($cell);
			$id = $block->attr("id");
			$style = $block->attr("style");
			if($id == "f6" && strpos($style, "left:24pt") !== false)
				$section = trim($cell->textContent);
			elseif($id == "f5")
			{
				foreach ($find_left as $field => $left) 
				{
					if(strpos($style, "left:".$left."pt") !== false)
					{
						if($field == "articul")
						{
							if(!empty($arElem))
								$tmpResult[] = $arElem;
							$arElem = array("section" => $section, $field => trim($cell->textContent));
						}
						else
							$arElem[$field] .= (!empty($arElem[$field]) ? " " : "").trim($cell->textContent);
						break;
					}
				}
			}
		}
		if(!empty($arElem))
			$tmpResult[] = $arElem;
		if(!empty($tmpResult))
		{
			$fp = fopen("2.csv", "w");
			foreach ($tmpResult as $fields) 
			{
				$fields["name"] = addslashes($fields["name"]);
				fputcsv($fp, $fields);
			}
			fclose($fp);
		}
	}*/
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>