<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Import barcodes");
global $USER;
if(!$USER->IsAdmin()) die();
$file = $_SERVER["DOCUMENT_ROOT"]."/upload/barcodes.xml";
$arResult = array();
if (file_exists($file)) {
	$xml = simplexml_load_file($file);

	/*foreach($xml->Объект as $item)
	{
		$xml_id = "";
		$arItem = array();
		if((string) $item["ИмяПравила"] == "ШтрихкодыНоменклатуры")
		{
			foreach($item->Свойство as $prop)
			{
				if((string) $prop["Имя"] == "Номенклатура")
				{
					foreach($prop->Ссылка->Свойство as $link_prop)
					{
						if((string) $link_prop["Имя"] == "{УникальныйИдентификатор}")
						{
							$xml_id = (string) $link_prop->Значение;
						}
						$arItem["Свойства"][(string) $prop["Имя"]][(string) $link_prop["Имя"]] = (string) $link_prop->Значение;
					}
				}
				else
				{
					$arItem["Свойства"][(string) $prop["Имя"]] = (string) $prop->Значение;
				}
			}
		}
		elseif((string) $item["ИмяПравила"] == "Номенклатура")
		{
			foreach ($item->Ссылка->Свойство as $prop) 
			{
				if((string) $prop["Имя"] == "{УникальныйИдентификатор}")
				{
					$xml_id = (string) $prop->Значение;
				}
				$arItem["Ссылка"][(string) $prop["Имя"]] = (string) $prop->Значение;
			}
			foreach($item->Свойство as $prop)
			{
				if((string) $prop["Имя"] == "ЕдиницаИзмерения")
				{
					foreach($prop->Ссылка->Свойство as $link_prop)
					{
						$arItem["Свойства"][(string) $prop["Имя"]][(string) $link_prop["Имя"]] = (string) $link_prop->Значение;
					}
				}
				else
				{
					$arItem["Свойства"][(string) $prop["Имя"]] = (string) $prop->Значение;
				}
			}
		}

		$arResult[] = $arItem;
	}*/

	foreach($xml->Объект as $item)
	{
		//print_p((string) $item["ИмяПравила"]);
		if((string) $item["ИмяПравила"] == "ШтрихкодыНоменклатуры")
		{
			$xml_id = "";
			$barcode = "";
			foreach($item->Свойство as $prop)
			{
				if((string) $prop["Имя"] == "Штрихкод")
				{
					$barcode = (string) $prop->Значение;
				}
				elseif((string) $prop["Имя"] == "Номенклатура")
				{
					foreach($prop->Ссылка->Свойство as $link_prop)
					{
						if((string) $link_prop["Имя"] == "{УникальныйИдентификатор}")
						{
							$xml_id = (string) $link_prop->Значение;
						}
					}
				}
			}
			if(!empty($xml_id) && !empty($barcode))
				$arResult[$xml_id]['barcodes'][] = $barcode;
		}
		elseif((string) $item["ИмяПравила"] == "Номенклатура")
		{
			$xml_id = "";
			$measure = "";
			foreach ($item->Ссылка->Свойство as $prop) 
			{
				if((string) $prop["Имя"] == "{УникальныйИдентификатор}")
				{
					$xml_id = (string) $prop->Значение;
				}
			}
			foreach($item->Свойство as $prop)
			{
				if((string) $prop["Имя"] == "ЕдиницаИзмерения")
				{
					foreach($prop->Ссылка->Свойство as $link_prop)
					{
						if((string) $link_prop["Имя"] == "Код")
						{
							$measure = (string) $link_prop->Значение;
						}
					}
				}
			}
			if(!empty($xml_id) && !empty($measure))
				$arResult[$xml_id]['measure'] = $measure;
		}
	}
} else {
	exit('Не удалось открыть файл test.xml.');
}

if(!empty($arResult) && CModule::IncludeModule("iblock") && CModule::IncludeModule("yadadya.shopmate"))
{
	$_REQUEST["CURRENT_SAVE"] = "Y";
	$convertMeasure = array(
		717 => 4, //кг.
		138 => 1, //м.
		113 => 2, //м3.
		450 => 0, //сут.
		142 => 0, //т.
		559 => 5, //шт.
		416 => 5, //шт.
	);
	$rsItem = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => 2, "XML_ID" => array_keys($arResult)), false, false, array("ID", "XML_ID"));
	while($arItem = $rsItem->GetNext())
 	{
 		$arFields = array("ID" => $arItem["ID"]);
		unset($_REQUEST["PROPERTY"]["CAT_BARCODE"], $_REQUEST["PROPERTY"]["CAT_MEASURE"]);
 		if(!empty($arResult[$arItem["XML_ID"]]["barcodes"]))
 			$_REQUEST["PROPERTY"]["CAT_BARCODE"] = $arResult[$arItem["XML_ID"]]["barcodes"];
 		if(!empty($arResult[$arItem["XML_ID"]]["measure"]))
 			$_REQUEST["PROPERTY"]["CAT_MEASURE"] = $convertMeasure[$arResult[$arItem["XML_ID"]]["measure"]];
 		SMProductions::CustomSave($arFields);
 	}
}
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>