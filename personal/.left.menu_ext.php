<?
// пример файла .left.menu_ext.php
use Yadadya\Shopmate\Rights;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if (Rights::GetUserPermission("personaldepartment") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Подразделения", 
			"departments/", 
			Array(), 
			Array(), 
			"" 
		);
	}
	if (Rights::GetUserPermission("personalposition") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Должности", 
			"positions/", 
			Array(), 
			Array(), 
			"" 
		);
	}
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>