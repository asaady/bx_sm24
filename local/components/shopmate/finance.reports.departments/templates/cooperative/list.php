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
		<div data-toggle="buttons" style="position: absolute; right: 20px;">
			<label class="btn btn-link" data-toggle="collapse" data-target="#filter">
				<input type="checkbox"> Фильтр
			</label>
		</div>
		<h1><?$APPLICATION->ShowTitle(false)?></h1>
	</div>
	<div id="filter" class="collapse out">
		<?$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.filter", "", $arParams, $component);?>
	</div>
	<hr />
	<?\Yadadya\Shopmate\FinanceReport::updateBlock();?>
	<?$APPLICATION->SetCurPage($componentPath.".graph/ajax.php");?>
	<div class="row mb20" data-load_url="<?=$APPLICATION->GetCurPageParam("template=".$templateName, array("template"));?>" style="position: relative; min-height: 60px;" id="pichart_cooperative">
		<?//$APPLICATION->IncludeComponent("shopmate:finance.reports.departments.graph", "", $arParams, $component);?>
	</div>
	<?$APPLICATION->reinitPath();?>
</div>