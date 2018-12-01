<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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

<div class="row">
	<div class="col-md-6 mb30">
	<?if($_REQUEST["graph"] == "SALE") $APPLICATION->RestartBuffer();?>
		<h5 class="lg-title mb10">Продажи за период</h5>
		<?if(!empty($arResult["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arResult["BACK_URL"]?>&graph=SALE">все категории</a></p><?endif?>
		<div class="piechart flotGraph">
			<div class="piechart__data">
			<?foreach($arResult["ELEMENTS"] as $arElement):?>
				<div class="piechart__data__label" data-val="<?=abs($arElement["SALE"])?>" data-url="<?=$arElement["URL"]?>&graph=SALE"><?=$arElement["NAME"]?><br><?=PriceFormat($arElement["SALE"])?></div>
			<?endforeach?>
			</div>
		</div>
	<?if($_REQUEST["graph"] == "SALE") die()?>
	  </div><!-- col-md-6 -->
	  <div class="col-md-6 mb30">
	<?if($_REQUEST["graph"] == "PROFIT") $APPLICATION->RestartBuffer();?>
		<h5 class="lg-title mb10">Прибыль за период</h5>
		<?if(!empty($arResult["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arResult["BACK_URL"]?>&graph=PROFIT">все категории</a></p><?endif?>
		<div class="piechart flotGraph">
			<div class="piechart__data">
			<?foreach($arResult["ELEMENTS"] as $arElement):?>
				<div class="piechart__data__label" data-val="<?=abs($arElement["PROFIT"])?>" data-url="<?=$arElement["URL"]?>&graph=PROFIT"><?=$arElement["NAME"]?><br><?=PriceFormat($arElement["PROFIT"])?></div>
			<?endforeach?>
			</div>
		</div>
	<?if($_REQUEST["graph"] == "PROFIT") die()?>
	</div><!-- col-md-6 -->
</div><!-- row -->