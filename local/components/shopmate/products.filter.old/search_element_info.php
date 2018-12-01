<?define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);
$arResult = array();
$ID = IntVal($_REQUEST["pid"]);
$USER_ID = IntVal($_REQUEST["uid"]);
if($ID > 0 && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	if($USER_ID <= 0)
		$USER_ID = SMUser::SimpleBuyerAdd();

	$IBLOCK_ID =  CIBlockElement::GetIBlockByID($ID);

	$arUserGroups = array();

	$res = CUser::GetUserGroupList(empty($USER_ID) ? false : $USER_ID);
	while ($arGroup = $res->Fetch())
		$arUserGroups[] = $arGroup["GROUP_ID"];

	//$arPriceGroups = CCatalogGroup::GetGroupsPerms($arUserGroups, array());

	$arFilter = array("PRODUCT_ID" => $ID);
	$arFilter["CATALOG_GROUP_ID"] = SMShops::getUserPrice();

	$dbPrice = CPrice::GetListEx(
		array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
		$arFilter,
		false,
		false,
		array("ID", "PRODUCT_ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
	);

	while ($arPrice = $dbPrice->Fetch())
	{
		CCatalogDiscountSave::Disable();
		$arDiscounts = CCatalogDiscount::GetDiscount($ID, $IBLOCK_ID, $arPrice["CATALOG_GROUP_ID"], $arUserGroups, "N", SITE_ID);
		CCatalogDiscountSave::Enable();
		$arPrice["DISCOUNT_PRICE"] = empty($arDiscounts) ? $arPrice['PRICE'] : CCatalogProduct::CountPriceWithDiscount($arPrice["PRICE"], $arPrice["CURRENCY"], $arDiscounts);

		if($arPrice["DISCOUNT_PRICE"] > 0 && ($arResult["DISCOUNT_PRICE"] <= 0 || $arPrice["DISCOUNT_PRICE"] < $arResult["DISCOUNT_PRICE"]))
		{
			$arResult["PRICE"] = $arPrice["PRICE"];
			$arResult["DISCOUNT_PRICE"] = $arPrice["DISCOUNT_PRICE"];
		}
	}

	/*$SHOP_ID = SMShops::getUserShop();

	$rsProps = CCatalogStore::GetList(array('SORT' => 'ASC'), array("PRODUCT_ID" => $ID, "ACTIVE" => "Y", "ID" => $SHOP_ID), false, false, array("PRODUCT_AMOUNT"));
	if($arProp = $rsProps->GetNext())
		$arResult["CAT_AMOUNT"] = $arProp["PRODUCT_AMOUNT"];*/

	$rsProps = CCatalogProduct::GetList(array(), array("ID" => $ID), false, false, array("PURCHASING_PRICE"));
	if($arProp = $rsProps->GetNext())
		$arResult["PURCHASING_PRICE"] = $arProp["PURCHASING_PRICE"];

	$arResult["PURCHASING_PRICES"] = array();
	$rsProps = CCatalogStoreDocsElement::getList(array("ID" => "DESC"), array("ELEMENT_ID" => $ID, "STORE_TO" => SMShops::getUserShop(), ">PURCHASING_PRICE" => 0));
	while($arProp = $rsProps->Fetch()) 
		$arResult["PURCHASING_PRICES"][] = $arProp["PURCHASING_PRICE"];
	$arResult["PURCHASING_PRICES"] = json_encode($arResult["PURCHASING_PRICES"]);
	//print_p($arResult);
}
echo json_encode($arResult);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();?>