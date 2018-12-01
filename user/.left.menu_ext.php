<?
// пример файла .left.menu_ext.php
use Yadadya\Shopmate\Rights;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if ($USER->IsAuthorized())
	{
		$aMenuLinksExt[] = Array(
			"Профиль", 
			"profile/", 
			Array(), 
			Array(), 
			"" 
		);
	}
	
	if (Rights::GetUserPermission("useroverhead", false, true) >= "R")
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

}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>