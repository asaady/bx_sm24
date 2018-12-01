<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
$arParents = array();
$arLevels = array();
foreach($arResult as $keyItem => $arItem)
{
	$arParents[$arItem["DEPTH_LEVEL"]] = $keyItem;
	$arItem["PARENT"] = $arItem["DEPTH_LEVEL"] > 1 ? $arParents[$arItem["DEPTH_LEVEL"] - 1] : 0;
	$arLevels[$arItem["DEPTH_LEVEL"]][$keyItem] = $arItem;
}
$arLevels = array_values($arLevels);
for($lvl = count($arLevels) - 1; $lvl >= 0; $lvl--)
{
	$curLevel = $arLevels[$lvl];
	$prvLevel = &$arLevels[$lvl-1];
	foreach($curLevel as $kl => $arLvl)
		$prvLevel[$arLvl["PARENT"]]["MENU"][] = $arLvl;

}
$arResult = array_values($arLevels[0]);
unset($arLevels);?>