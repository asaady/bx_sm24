<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if ($USER->IsAdmin() || SM::GetUserPermission("products") >= "R")
		$aMenuLinksExt[] = Array(
			"Товары", 
			"/products/", 
			Array(), 
			Array(), 
			"" 
		);
	if ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "R")
		$aMenuLinksExt[] = Array(
			"Накладные", 
			"/products/overhead/", 
			Array(), 
			Array(), 
			"" 
		);
	if ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "R")
		$aMenuLinksExt[] = Array(
			"Алкогольная продукция", 
			"/products/egais/", 
			Array(), 
			Array(), 
			"" 
		);
	if ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "R")
		$aMenuLinksExt[] = Array(
			"Инвентаризация", 
			"/products/inventory/", 
			Array(), 
			Array(), 
			"" 
		);

}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>