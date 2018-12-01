<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>

<div class="alert alert-<?=($arParams["STYLE"] != "errortext" ? "success" : "danger")?>">
	<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	<?=htmlspecialcharsback($arParams["MESSAGE"])?>
</div>