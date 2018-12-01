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
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<?/*<a href="<?=$arParams["EDIT_URL"]?>?edit=Y" class="btn btn-primary pull-right">Добавить поставщика</a>*/?>
<table class="table mb30">
	<thead>
		<tr>
			<th>Наименование</th>
			<th>Продажи за период</th>
			<th>Закупки за период</th>
			<th>Фактическая прибыль</th>
			<th>Прайсовая прибыль</th>
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
<?/*<div class="row table-bordered container-fluid">
	<div class="col-md-10 col-md-offset-1">
		<div class="row text-center">
			<div class="col-md-3">
				Название компании поставщика
			</div>
			<div class="col-md-2">
				Тип поставляемой продукции
			</div>
			<div class="col-md-2">
				Наша скидка у поставщика
			</div>
			<div class="col-md-2">
				Дата последней закупки
			</div>
			<div class="col-md-1">
				Наша задолженность перед поставщиком
			</div>
			<div class="col-md-1">
				
			</div>
		</div>
	</div>
	<div class="col-md-10 col-md-offset-1">
	<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
		<div class="row table-bordered">
			<div class="col-md-3">
				<?=($arItem["PERSON_TYPE"] == 2 ? $arItem["COMPANY"]." (".$arItem["PERSON_NAME"].")" : $arItem["PERSON_NAME"])?>
			</div>
			<div class="col-md-2">
				<?=$arItem["PRODUCTION"]?>
			</div>
			<div class="col-md-2">
				<?=IntVal($arItem["DISCOUNT"])?>%
			</div>
			<div class="col-md-2">
				<?=$arItem["LAST_DATE_DOCUMENT"]?>
			</div>
			<div class="col-md-1">
				<p class="bg-<?if($arItem["DEBT"] > 50000):?>danger<?elseif($arItem["DEBT"] > 5000):?>warning<?else:?>success<?endif?>"><?=PriceFormat($arItem["DEBT"])?></p>
			</div>
			<div class="col-md-1">
			<?if ($arResult["CAN_EDIT"] == "Y"):?>
				<a class="btn btn-default" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>">редактировать</a>
			<?endif?>
			</div>
		</div>
	<?endforeach?>
	</div>
</div>*/?>