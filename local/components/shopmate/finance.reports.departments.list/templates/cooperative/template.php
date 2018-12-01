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
<?$arResult["NAV_STRING"] = str_ireplace("href=", "data-loadto=\"pichart_cooperative\" href=", $arResult["NAV_STRING"]);?>
<div class="row">
	<div class="col-md-6 mb30">
		<?if(!empty($arParams["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arParams["BACK_URL"]?>" data-loadto="pichart_cooperative">категорию</a></p><?endif?>
		<table class="table mb30">
			<thead>
				<tr>
					<th>Наименование</th>
					<th>Продажи</th>
				</tr>
			</thead>
			<tbody>
			<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
				<tr>
					<td><?=$arItem["NAME"]?></td>
					<td><?=PriceFormat($arItem["SALE"])?> (<?=PriceFormat($arItem["SALE_PREV"])?>)</td>
				</tr>
			<?endforeach?>
			</tbody>
		</table>
		<div class="row">
			<div class="col-md-12">
				<?=$arResult["NAV_STRING"]?>
			</div>
		</div>
	  </div><!-- col-md-6 -->
	  <div class="col-md-6 mb30">
	  	<?if(!empty($arParams["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arParams["BACK_URL"]?>" data-loadto="pichart_cooperative">категорию</a></p><?endif?>
		<table class="table mb30">
			<thead>
				<tr>
					<th>Наименование</th>
					<th>Прибыль</th>
				</tr>
			</thead>
			<tbody>
			<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
				<tr>
					<td><?=$arItem["NAME"]?></td>
					<td><?=PriceFormat($arItem["PROFIT"])?> (<?=PriceFormat($arItem["PROFIT_PREV"])?>)</td>
				</tr>
			<?endforeach?>
			</tbody>
		</table>
		<div class="row">
			<div class="col-md-12">
				<?=$arResult["NAV_STRING"]?>
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
		<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
			<tr>
				<td><?=$arItem["NAME"]?></td>
				<td><?=PriceFormat($arItem["SALE"])?> (<?=PriceFormat($arItem["SALE_PREV"])?>)</td>
				<td><?=PriceFormat($arItem["PURCHASE"])?> (<?=PriceFormat($arItem["PURCHASE_PREV"])?>)</td>
				<td><?=PriceFormat($arItem["PROFIT"])?> (<?=PriceFormat($arItem["PROFIT_PREV"])?>)</td>
				<td><?=PriceFormat($arItem["PRICE_PROFIT"])?> (<?=PriceFormat($arItem["PRICE_PROFIT_PREV"])?>)</td>
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