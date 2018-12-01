<?
// пример файла .left.menu_ext.php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if ($USER->IsAdmin() || SM::GetUserPermission("cash") >= "R")
	{
		$aMenuLinksExt = array_merge($aMenuLinksExt, array(
			Array(
				"Касса", 
				"/cash/?edit=Y", 
				Array(), 
				Array(), 
				"" 
			),
			Array(
				"Чеки", 
				"/cash/", 
				Array(), 
				Array(), 
				"" 
			),
			Array(
				"X-отчет", 
				"/cash/?device_action=Xreport#Xreport", 
				Array(), 
				Array(), 
				"" 
			),
			Array(
				"Сверка итогов", 
				"/cash/?device_action=posSettlement#posSettlement", 
				Array(), 
				Array(), 
				"" 
			),
			Array(
				"Повтор операции", 
				"/cash/?device_action=repeatDocument#repeatDocument", 
				Array(), 
				Array(), 
				"" 
			),
			Array(
				"Провести инкассацию", 
				"/cash/?device_action=cashcollection#cashcollection", 
				Array(), 
				Array(), 
				"" 
			),
		));
	}
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>