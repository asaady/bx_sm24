<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$chapters = array();
foreach($arResult["ITEMS"] as $arItem)
	if(!empty($arItem["SECTION_ID"]) && $arItem["ITEM_ID"] > 0)
		$chapters[$arItem["SECTION_ID"]][] = $arItem["ITEM_ID"];
foreach($chapters as $chapter => $chapter_ids)
	if($chapter == "overhead")
	{
		$arElements = array();
		$rsElements = CCatalogDocs::getList(array("ID" => "ASC"), array("SITE_ID" => SITE_ID, "DOC_TYPE" => "A", "ID" => $chapter_ids));
		while ($arElement = $rsElements->NavNext(false))
			$arElements[$arElement["ID"]] = htmlspecialcharsex($arElement);

		$arSMElements = array();
		$rsElements = SMDocs::getList(array(), array("DOC_ID" => $chapter_ids));
		while ($arElement = $rsElements->Fetch())
		{
			$doc_id = $arElement["DOC_ID"];
			unset($arElement["ID"], $arElement["DOC_ID"]);
			$arSMElements[$doc_id] = $arElement;
		}
		foreach ($arElements as $keyElement => $arElement) 
			if(!empty($arSMElements[$arElement["ID"]]))
				$arElements[$keyElement] = array_merge($arElement, $arSMElements[$arElement["ID"]]);

		$arResult["OVERHEAD"] = $arElements;
	}

foreach($arResult["ITEMS"] as $keyItem => $arItem)
	if(!empty($arItem["SECTION_ID"]) && $arItem["ITEM_ID"] > 0)
	{
		if($arItem["SECTION_ID"] == "overhead")
			$arResult["ITEMS"][$keyItem]["TITLE"] = "Накладная".(!empty($arResult["OVERHEAD"][$arItem["ITEM_ID"]]["NUMBER_DOCUMENT"]) ? " №".$arResult["OVERHEAD"][$arItem["ITEM_ID"]]["NUMBER_DOCUMENT"] : "").(!empty($arResult["OVERHEAD"][$arItem["ITEM_ID"]]["DATE_DOCUMENT"]) ? " от ".FormatDateFromDB($arResult["OVERHEAD"][$arItem["ITEM_ID"]]["DATE_DOCUMENT"], "SHORT") : "");
		if($arItem["SECTION_ID"] == "cash")
			$arResult["ITEMS"][$keyItem]["TITLE"] = "Чек".(!empty($arResult["OVERHEAD"][$arItem["ITEM_ID"]]["NUMBER_DOCUMENT"]) ? " №".$arResult["OVERHEAD"][$arItem["ITEM_ID"]]["NUMBER_DOCUMENT"] : " №".$arItem["ITEM_ID"]).(!empty($arResult["OVERHEAD"][$arItem["ITEM_ID"]]["DATE_DOCUMENT"]) ? " от ".FormatDateFromDB($arResult["OVERHEAD"][$arItem["ITEM_ID"]]["DATE_DOCUMENT"], "SHORT") : "");
	}
?>