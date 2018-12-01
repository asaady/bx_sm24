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
	<?$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.filter", "", $arParams, $component);?>
	<hr />
	<?$APPLICATION->SetCurPage($componentPath.".graph/ajax.php");?>
	<div class="row mb20" data-load_url="<?=$APPLICATION->GetCurPageParam("", array());?>" style="position: relative; min-height: 60px;">
		<?//$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.graph", "", $arParams, $component);?>
	</div>
	<?$APPLICATION->reinitPath();?>
	<hr />
	<div class="row mb20">
		<?$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.list", "", $arParams, $component);?>
	</div>
</div>