<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
$this->setFrameMode(false);?>
<div class="panel panel-body">
	<div class="mb30"> 
		<h1><?$APPLICATION->ShowTitle(false)?></h1>
	</div>
	<?$APPLICATION->IncludeComponent("shopmate:oldcash.filter", "", $arParams, $component, array('HIDE_ICONS' => 'Y'));?>
	<div class=" mb30">
		<?$APPLICATION->IncludeComponent("shopmate:oldcash.list", "", $arParams, $component);?>
	</div>
</div>