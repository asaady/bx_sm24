<?
// пример файла .left.menu_ext.php
use Yadadya\Shopmate\Rights;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if (Rights::GetUserPermission("product") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Товары", 
			".", 
			Array(), 
			Array(), 
			"" 
		);
	}
	if (Rights::GetUserPermission("section") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Разделы", 
			"section/", 
			Array(), 
			Array(), 
			"" 
		);
	}
	if (Rights::GetUserPermission("pricelist") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Прайслисты", 
			"pricelist/", 
			Array(), 
			Array(), 
			"" 
		);
	}
	if (Rights::GetUserPermission("overhead") >= "R")
	{
		$cnt = \Yadadya\Shopmate\UserOverhead::getNewCnt();
		$aMenuLinksExt[] = Array(
			"Накладные".($cnt > 0 ? " <div class=\"label label-warning pull-right\">".$cnt."</div>" : ""), 
			"overhead/", 
			Array(), 
			Array(), 
			"" 
		);
	}
	/*if (Rights::GetUserPermission("useroverhead") >= "R")
	{
		$cnt = \Yadadya\Shopmate\UserOverhead::getNewCnt();
		$aMenuLinksExt[] = Array(
			"Входящие накладные".($cnt > 0 ? " <div class=\"label label-warning pull-right\">".$cnt."</div>" : ""), 
			"/user/overhead/", 
			Array(), 
			Array(), 
			"" 
		);
	}*/
	if (Rights::GetUserPermission("egais") >= "R")
		$aMenuLinksExt[] = Array(
			"Алкогольная продукция", 
			"egais/", 
			Array(), 
			Array(), 
			"" 
		);
	if (Rights::GetUserPermission("inventory") >= "R")
		$aMenuLinksExt[] = Array(
			"Инвентаризация", 
			"inventory/", 
			Array(), 
			Array(), 
			"" 
		);

}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>