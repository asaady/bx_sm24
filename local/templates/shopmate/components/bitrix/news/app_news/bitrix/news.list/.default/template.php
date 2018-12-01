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
$this->setFrameMode(true);
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-btns">
			<a href="#" class="panel-minimize tooltips" data-toggle="tooltip" title="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i></a>
			<?/*<a href="#" class="panel-close tooltips" data-toggle="tooltip" title="" data-original-title="Close Panel"><i class="fa fa-times"></i></a>*/?>
		</div><!-- panel-btns -->
		<h4 class="panel-title"><?echo $arItem["NAME"]?></h4>
		<p><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></p>
	</div><!-- panel-heading -->
	<div class="panel-body">
		<p><?echo (empty($arItem["DETAIL_TEXT"]) ? $arItem["PREVIEW_TEXT"] : $arItem["DETAIL_TEXT"])?></p>
	</div><!-- panel-body -->
</div>
<?endforeach;?>

<?=$arResult["NAV_STRING"]?>