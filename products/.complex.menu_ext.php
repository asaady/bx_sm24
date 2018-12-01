<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION, $USER;

$aMenuLinksExt = array();

use Yadadya\Shopmate\Components;

if (CModule::IncludeModule("yadadya.shopmate"))
{
	if (Components\Overhead::userCanRead())
		$aMenuLinksExt[] = Array(
			"Оприходовать товар", 
			"/products/overhead/?edit=Y", 
			Array(), 
			Array(), 
			"" 
		);
	if (Components\Inventory::userCanRead())
	{
		if ($inventory = Components\Inventory::searchActiveInventory())
			$aMenuLinksExt[] = Array(
				"Продолжить " . ($inventory["PRIMARY"] == "Y" ? "начальную " : "") . "инвентаризацию от " . $inventory["DATE"], 
				"/products/inventory/?edit=Y&CODE=" . $inventory["ID"], 
				Array(), 
				Array(), 
				"" 
			);
		else
			$aMenuLinksExt[] = Array(
				(Components\Inventory::searchPrimaryInventory() ? "Новая" : "Начальная") . " инвентаризация", 
				"/products/inventory/?edit=Y",
				Array(), 
				Array(), 
				"" 
			);
	}

}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>