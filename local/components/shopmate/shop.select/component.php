<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(true);

if($USER->IsAuthorized() && CModule::IncludeModule("yadadya.shopmate"))
{
	if(!empty($_REQUEST["SHOP_ID"]))
		SMShops::setUserShopParams($_REQUEST["SHOP_ID"]);
	$arResult["ITEMS"] = SMShops::getUserShops();
	foreach($arResult["ITEMS"] as $keyGroup => $arGroup)
		if($arGroup["SHOP_ID"] == SMShops::getUserShop())
		{
			$arResult["ITEMS"][$keyGroup]["SELECTED"] = "Y";
			break;
		}
	
}

$this->IncludeComponentTemplate($componentPage);
?>