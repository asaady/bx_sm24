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
		<h5 class="lg-title mb10">Продажи за период</h5>
		<?if(!empty($arResult["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arResult["BACK_URL"]?>" data-loadto="pichart_cooperative">все категории</a></p><?endif?>
		<div class="piechart flotGraph" data-loadto="pichart_cooperative">
			<div class="piechart__data">
			<?foreach($arResult["ELEMENTS"] as $arElement):?>
				<div class="piechart__data__label" data-val="<?=abs($arElement["SALE"])?>" data-url="<?=$arElement["URL"]?>"><?=$arElement["NAME"]?><br><?=PriceFormat($arElement["SALE"])?></div>
			<?endforeach?>
			</div>
		</div>
	  </div><!-- col-md-6 -->
	  <div class="col-md-6 mb30">
		<h5 class="lg-title mb10">Прибыль за период</h5>
		<?if(!empty($arResult["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arResult["BACK_URL"]?>" data-loadto="pichart_cooperative">все категории</a></p><?endif?>
		<div class="piechart flotGraph" data-loadto="pichart_cooperative">
			<div class="piechart__data">
			<?foreach($arResult["ELEMENTS"] as $arElement):?>
				<div class="piechart__data__label" data-val="<?=abs($arElement["PROFIT"])?>" data-url="<?=$arElement["URL"]?>"><?=$arElement["NAME"]?><br><?=PriceFormat($arElement["PROFIT"])?></div>
			<?endforeach?>
			</div>
		</div>
	</div><!-- col-md-6 -->
</div><!-- row -->
<hr />
<div class="row">
	<?if(!empty($arParams["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arParams["BACK_URL"]?>" data-loadto="pichart_cooperative">категорию</a></p><?endif?>
	<table class="table mb30">
		<thead>
			<tr>
			<?foreach($arResult["SORTS"] as $arSort):?>
				<th><a href="#" data-url="<?=$arSort["URL"]?>" data-loadto="pichart_cooperative"><?=$arSort["NAME"]?></a></th>
			<?endforeach?>
				<?/*<th>Наименование</th>
				<th>Продажи за период</th>
				<th>Закупки за период</th>
				<th>Фактическая прибыль</th>
				<th>Прайсовая прибыль</th>*/?>
			</tr>
		</thead>
		<tbody>
		<?foreach($arResult["ELEMENTS"] as $key => $arElement):?>
			<tr>
				<td><a href="#" data-url="<?=$arElement["URL"]?>" data-loadto="pichart_cooperative"><?=$arElement["NAME"]?></a></td>
				<td><?=PriceFormat($arElement["SALE"])?> (<?=PriceFormat($arElement["SALE_PREV"])?>)</td>
				<td><?=PriceFormat($arElement["PURCHASE"])?> (<?=PriceFormat($arElement["PURCHASE_PREV"])?>)</td>
				<td><?=PriceFormat($arElement["PROFIT"])?> (<?=PriceFormat($arElement["PROFIT_PREV"])?>)</td>
				<td><?=PriceFormat($arElement["PRICE_PROFIT"])?> (<?=PriceFormat($arElement["PRICE_PROFIT_PREV"])?>)</td>
			</tr>
		<?endforeach?>
		</tbody>
	</table>
	<div class="row">
		<div class="col-md-12">
			<?=$arResult["NAV_STRING"]?>
		</div>
	</div>
</div>