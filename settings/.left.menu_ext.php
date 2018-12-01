<?
// пример файла .left.menu_ext.php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if ($USER->IsAdmin() || \Yadadya\Shopmate\Rights::GetUserPermission("settings") >= "R")
	{
		$aMenuLinksExt[] = Array(
			"Права доступа", 
			"rights/", 
			Array(), 
			Array(), 
			"" 
		);
	}
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>