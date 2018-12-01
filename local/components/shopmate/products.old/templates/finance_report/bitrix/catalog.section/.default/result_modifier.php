<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main;
$arResult = array_merge($arParams["TEMPLATE_RESULT"], $arResult);
$arProperties = $_REQUEST["PROPERTY"];
foreach($arProperties as $prop => $arProperty)
	$arProperties[$prop] = is_array($arProperty) ? current($arProperty) : $arProperty;
if(!empty($arResult["ITEMS"]))
{
	$products_id = array();
	foreach($arResult["ITEMS"] as $arItem)
		$products_id[] = $arItem["ID"];
	$oProductStore = new CCatalogStoreProduct();
	$dbProductStore = $oProductStore->getList(
		array(), 
		array(
			"PRODUCT_ID" => $products_id,
			"STORE_ID" => SMShops::getUserShop()
		),
		false,
		false,
		array("ID", "PRODUCT_ID", "STORE_ID", "AMOUNT")
	);
	while($arProductStore = $dbProductStore->Fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arProductStore["PRODUCT_ID"])
			{
				$arResult["ITEMS"][$keyItem]["CAT_AMOUNT"] = $arProductStore["AMOUNT"];
				break;
			}

	/*$arFilterAmount = array("STORE_ID" => SMShops::getUserShop(), "PRODUCT_ID" => $products_id);
	if(!empty($arProperties["LAST_OVERHEAD"]))
	{
		$arFilterAmount[">=TIMESTAMP_X"] = $arProperties["LAST_OVERHEAD"]." 00:00:00";
		$arFilterAmount["<=TIMESTAMP_X"] = $arProperties["LAST_OVERHEAD"]." 23:59:59";
	}
	if(!empty($arProperties["LAST_OVERHEAD_FROM"]))
		$arFilterAmount[">=TIMESTAMP_X"] = $arProperties["LAST_OVERHEAD_FROM"]." 13:37:00";
	if(!empty($arProperties["LAST_OVERHEAD_TO"]))
		$arFilterAmount["<=TIMESTAMP_X"] = $arProperties["LAST_OVERHEAD_TO"]." 23:59:59";

	$arFilterAmountIn = $arFilterAmount;
	$arFilterAmountIn[">AMOUNT"] = 0;
	$rsAmount = \Yadadya\Shopmate\FinanceReport::getProdAmount(array(
		'select' => array("PRODUCT_ID", new Main\Entity\ExpressionField("SUMM", "SUM(%s)", "AMOUNT")),
		'group' => array("PRODUCT_ID"),
		'filter' => $arFilterAmountIn,
	));
	while($arAmount = $rsAmount->fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arAmount["PRODUCT_ID"])
			{
				$arResult["ITEMS"][$keyItem]["CAT_AMOUNT_IN"] = $arAmount["SUMM"];
				break;
			}

	$arFilterAmountOut = $arFilterAmount;
	$arFilterAmountOut["<AMOUNT"] = 0;
	$rsAmount = \Yadadya\Shopmate\FinanceReport::getProdAmount(array(
		'select' => array("PRODUCT_ID", new Main\Entity\ExpressionField("SUMM", "SUM(%s)", "AMOUNT")),
		'group' => array("PRODUCT_ID"),
		'filter' => $arFilterAmountOut,
	));
	while($arAmount = $rsAmount->fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arAmount["PRODUCT_ID"])
			{
				$arResult["ITEMS"][$keyItem]["CAT_AMOUNT_OUT"] = abs($arAmount["SUMM"]);
				break;
			}*/
	$arFilterAmount = array("ID" => $products_id, "STORE_ID" => SMShops::getUserShop());
	if(!empty($arProperties["LAST_OVERHEAD"]))
	{
		$arFilterAmount["DATE_FROM"] = $arProperties["LAST_OVERHEAD"]." 00:00:00";
		$arFilterAmount["DATE_TO"] = $arProperties["LAST_OVERHEAD"]." 23:59:59";
	}
	if(!empty($arProperties["LAST_OVERHEAD_FROM"]))
		$arFilterAmount["DATE_FROM"] = $arProperties["LAST_OVERHEAD_FROM"]." 13:37:00";
	if(!empty($arProperties["LAST_OVERHEAD_TO"]))
		$arFilterAmount["DATE_TO"] = $arProperties["LAST_OVERHEAD_TO"]." 23:59:59";
	$result = \Yadadya\Shopmate\FinanceReport::calcElements(array(
		"select" => array("ID", "SALE_QUANTITY", "PURCHASE_AMOUNT"),
		"filter" => $arFilterAmount
	));
	while($arAmount = $result->fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arAmount["ID"])
			{
				$arResult["ITEMS"][$keyItem]["CAT_AMOUNT_IN"] = abs($arAmount["PURCHASE_AMOUNT"]);
				$arResult["ITEMS"][$keyItem]["CAT_AMOUNT_OUT"] = abs($arAmount["SALE_QUANTITY"]);
				break;
			}

	$arFilterPrice = array("STORE_ID" => SMShops::getUserShop(), "PRODUCT_ID" => $products_id);
	if(!empty($arProperties["LAST_OVERHEAD"]))
	{
		$arFilterPrice[">=DATE"] = $arProperties["LAST_OVERHEAD"]." 00:00:00";
		$arFilterPrice["<=DATE"] = $arProperties["LAST_OVERHEAD"]." 23:59:59";
	}
	if(!empty($arProperties["LAST_OVERHEAD_FROM"]))
		$arFilterPrice[">=DATE"] = $arProperties["LAST_OVERHEAD_FROM"]." 13:37:00";
	if(!empty($arProperties["LAST_OVERHEAD_TO"]))
		$arFilterPrice["<=DATE"] = $arProperties["LAST_OVERHEAD_TO"]." 23:59:59";

	$arFilterPPrice = $arFilterPrice;
	$arFilterPPrice["<=CATALOG_GROUP_ID"] = 0;
	$rsPrice = \Yadadya\Shopmate\FinanceReport::getProdPrice(array(
		'select' => array("PRODUCT_ID", new Main\Entity\ExpressionField("AVERAGE", "AVG(%s)", "PRICE")),
		'group' => array("PRODUCT_ID"),
		'filter' => $arFilterPPrice,
	));
	while($arPrice = $rsPrice->fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arPrice["PRODUCT_ID"])
			{
				$arResult["ITEMS"][$keyItem]["CATALOG_PURCHASING_PRICE"] = $arPrice["AVERAGE"];
				break;
			}

	$arFilterCPrice = $arFilterPrice;
	$arFilterCPrice[">CATALOG_GROUP_ID"] = 0;
	$rsPrice = \Yadadya\Shopmate\FinanceReport::getProdPrice(array(
		'select' => array("PRODUCT_ID", new Main\Entity\ExpressionField("AVERAGE", "AVG(%s)", "PRICE")),
		'group' => array("PRODUCT_ID"),
		'filter' => $arFilterCPrice,
	));
	while($arPrice = $rsPrice->fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arPrice["PRODUCT_ID"])
			{
				$arResult["ITEMS"][$keyItem]["CATALOG_PRICE"] = $arPrice["AVERAGE"];
				break;
			}

	$rsDocElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("ELEMENT_ID" => $products_id, "STORE_ID" => SMShops::getUserShop()), false, false, array("ELEMENT_ID"));
	while($arDocElement = $rsDocElement->Fetch())
		foreach($arResult["ITEMS"] as $keyItem => $arItem)
			if($arItem["ID"] == $arPrice["PRODUCT_ID"])
			{
				$arResult["ITEMS"][$keyItem]["CATALOG_PRICE"] = $arPrice["AVERAGE"];
				break;
			}

	$docs_id = array();
	$arDocFilter = array("DOC_TYPE" => "A");
	if(!empty($arProperties["LAST_OVERHEAD"]))
	{
		$arDocFilter[">=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD"]." 00:00:00";
		$arDocFilter["<=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD"]." 23:59:59";
	}
	if(!empty($arProperties["LAST_OVERHEAD_FROM"]))
		$arDocFilter[">=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD_FROM"]." 00:00:00";
	if(!empty($arProperties["LAST_OVERHEAD_TO"]))
		$arDocFilter["<=DATE_DOCUMENT"] = $arProperties["LAST_OVERHEAD_TO"]." 23:59:59";
	$rsDocs = CCatalogDocs::getList(array(), $arDocFilter, false, false, array("ID"));
	while($arDocs = $rsDocs->Fetch())
		$docs_id[] = $arDocs["ID"];
	if(!empty($docs_id))
	{
		$rsDocElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("ELEMENT_ID" => $products_id, "STORE_ID" => SMShops::getUserShop(), "DOC_ID" => $docs_id), false, false, array("ELEMENT_ID", "END_DATE"));
		while($arDocElement = $rsDocElement->Fetch())
			foreach($arResult["ITEMS"] as $keyItem => $arItem)
				if($arItem["ID"] == $arDocElement["ELEMENT_ID"])
				{
					$arResult["ITEMS"][$keyItem]["END_DATE"] = $arDocElement["END_DATE"];
					break;
				}
	}
}
unset($arResult["TEMPLATE_RESULT"]);?>