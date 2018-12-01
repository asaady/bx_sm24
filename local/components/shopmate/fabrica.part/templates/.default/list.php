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
$this->setFrameMode(false);
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="btn-list clearfix">
			<a class="btn btn-primary" href="<?=$arParams["EDIT_URL"]?>?edit=Y&merge=Y"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span>Добавить партию приготовления</a>
			<a class="btn btn-primary" href="<?=$arParams["EDIT_URL"]?>?edit=Y&split=Y"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span>Добавить партию разделения</a>
		</div>
	</div>
	<!-- panel --> 
</div>
<div class="panel panel-body">
	<div class="mb30"> 
		<h1><?$APPLICATION->ShowTitle(false)?></h1>
	</div>
	<div class=" mb30">
		<?$APPLICATION->IncludeComponent($component->getName().".list", "", $arParams, $component);?>
	</div>
</div>