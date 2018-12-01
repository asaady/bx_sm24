<?
// пример файла .left.menu_ext.php
use Yadadya\Shopmate\Rights;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if (Rights::GetUserPermission("financereport") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Отчеты", 
			"reports/", 
			Array(), 
			Array(), 
			"" 
		);
	}
	if (Rights::GetUserPermission("financemoney") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"ДДС", 
			"flow_of_funds/", 
			Array(), 
			Array(), 
			"" 
		);
	}
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>