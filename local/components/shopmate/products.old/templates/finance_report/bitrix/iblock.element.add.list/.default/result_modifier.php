<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arParams = array_merge($arParams, Array(
	"TEMPLATE_RESULT" => $arResult
));
if(CModule::IncludeModule("yadadya.shopmate"))
{
	$docFilter = array(
        "LOGIC" => "OR"
    );
	$rsDocs = CCatalogDocs::getList(array("ID" => "ASC"), array("SITE_ID" => SITE_ID, "DOC_TYPE" => "A", "STATUS" => "Y", "!PRODUCTS_ELEMENT_ID" => false), false, false, array("PRODUCTS_ELEMENT_ID"));
	while($arDoc = $rsDocs->Fetch())
		$docFilter[0]["ID"][] = $arDoc["PRODUCTS_ELEMENT_ID"];
	$SHOP_ID = SMShops::getUserShop();
	$docFilter[1]["!CATALOG_STORE_AMOUNT_".$SHOP_ID] = false;
	$arUsers = CGroup::GetGroupUser(SMShops::getUserGroup());
	$docFilter[2]["CREATED_BY"] = $arUsers;
	$docFilter[2]["CREATED_BY"][] = $USER->GetID();

	if(!empty($docFilter))
	{
		if(empty($arParams["FILTER_NAME"]))
			$arParams["FILTER_NAME"] = "arrDocsFilter";
		global ${$arParams["FILTER_NAME"]};
		${$arParams["FILTER_NAME"]}[] = $docFilter;
	}
}
?>