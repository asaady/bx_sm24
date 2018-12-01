<?
// пример файла .left.menu_ext.php
use Yadadya\Shopmate\Rights;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if (Rights::GetUserPermission("products") >= "R" || Rights::GetUserPermission("overhead") >= "R")
		$aMenuLinksExt[] = Array(
			"Товары | Накладные", 
			"/products/", 
			Array(), 
			Array("ICON" => "book"), 
			"" 
		);
	if (Rights::GetUserPermission("cash") >= "R")
		$aMenuLinksExt[] = Array(
			"Касса", 
			"/cash/", 
			Array(), 
			Array("ICON" => "barcode"), 
			"" 
		);
	if (Rights::GetUserPermission("finance") >= "R")
		$aMenuLinksExt[] = Array(
			"Финансы", 
			"/finance/", 
			Array(), 
			Array("ICON" => "money"), 
			"" 
		);
	if (Rights::GetUserPermission("fabrica") >= "R")
		$aMenuLinksExt[] = Array(
			"Производство", 
			"/fabrica/", 
			Array(), 
			Array("ICON" => "cutlery"), 
			"" 
		);
	if (Rights::GetUserPermission("contractor") >= "R")
		$aMenuLinksExt[] = Array(
			"Поставщики", 
			"/contractors/", 
			Array(), 
			Array("ICON" => "truck"), 
			"" 
		);
	if (Rights::GetUserPermission("client") >= "R")
		$aMenuLinksExt[] = Array(
			"Клиенты", 
			"/clients/", 
			Array(), 
			Array("ICON" => "users"), 
			"" 
		);
	if (Rights::GetUserPermission("personal") >= "R")
		$aMenuLinksExt[] = Array(
			"Сотрудники", 
			"/personal/", 
			Array(), 
			Array("ICON" => "sitemap"), 
			"" 
		);

	if (!$USER->IsAuthorized())
	{
		$aMenuLinksExt[] = Array(
			"Вход", 
			"/user/", 
			Array(), 
			Array("ICON" => "sign-in"), 
			"" 
		);
		$aMenuLinksExt[] = Array(
			"Регистрация", 
			"/register/", 
			Array(), 
			Array("ICON" => "edit"), 
			"" 
		);
	}
	else
		$aMenuLinksExt[] = Array(
			"Личный кабинет", 
			"/user/", 
			Array(), 
			Array("ICON" => "user"), 
			"" 
		);

	if (Rights::GetUserPermission("settings") >= "R")
		$aMenuLinksExt[] = Array(
			"Настройки", 
			"/settings/", 
			Array(), 
			Array("ICON" => "gear"), 
			"" 
		);
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>