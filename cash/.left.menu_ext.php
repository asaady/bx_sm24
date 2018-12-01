<?
// пример файла .left.menu_ext.php
use Yadadya\Shopmate\Rights;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if (Rights::GetUserPermission("cash") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Розница",
			"retail/", 
			Array(), 
			Array(), 
			"" 
		);
		$aMenuLinksExt[] = Array(
			"Продажа корпоративным клиентам",
			"corporate/", 
			Array(), 
			Array(), 
			"" 
		);
		$aMenuLinksExt[] = Array(
			"Чеки",
			"receipts/", 
			Array(), 
			Array(), 
			"" 
		);
		$aMenuLinksExt[] = Array(
			"Продажа собственнику",
			"self/", 
			Array(), 
			Array(), 
			"" 
		);
	}
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>