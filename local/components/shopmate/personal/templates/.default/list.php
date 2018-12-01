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
<div class="panel panel-default">
	<div class="panel-body">
		<div class="btn-list clearfix">
			<a class="btn btn-primary" href="<?=$arParams["EDIT_URL"]?>?edit=Y"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span>Добавить сотрудника</a>
		</div>
	</div>
	<!-- panel --> 
</div>
<div class="panel panel-body">
	<div class="mb30">
		<h1><?$APPLICATION->ShowTitle(false)?></h1>
	</div>
	<div class="row mb20">
		<?$APPLICATION->IncludeComponent("shopmate:personal.list", "", $arParams, $component);?>
	</div>
</div>