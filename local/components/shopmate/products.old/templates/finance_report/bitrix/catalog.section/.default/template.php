<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<table class="table table-hover mb30">
	<thead>
		<tr>
			<th>#</th>
			<th>Название товара</th>
			<th>Общее кол-во на складе</th>
			<th>Приход</th>
			<th>Расход</th>
			<th>Средняя закупочная цена</th>
			<th>Стандартная цена на продажу</th>
			<th>Срок годности</th>
			<th>Маржинальность</th>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<tr>
			<td><?=$arItem["ID"]?></td>
			<td><?=$arItem["NAME"]?></td>
			<td><?=(!empty($arItem["CAT_AMOUNT"]) ? $arItem["CAT_AMOUNT"]." ".$arItem["CATALOG_MEASURE_NAME"] : " - ")?></td>
			<td><?=(!empty($arItem["CAT_AMOUNT_IN"]) ? $arItem["CAT_AMOUNT_IN"]." ".$arItem["CATALOG_MEASURE_NAME"] : " - ")?></td>
			<td><?=(!empty($arItem["CAT_AMOUNT_OUT"]) ? $arItem["CAT_AMOUNT_OUT"]." ".$arItem["CATALOG_MEASURE_NAME"] : " - ")?></td>
			<td><?=(!empty($arItem["CATALOG_PURCHASING_PRICE"]) ? PriceFormat($arItem["CATALOG_PURCHASING_PRICE"])."/".$arItem["CATALOG_MEASURE_NAME"] : " - ")?></td>
			<td><?=(!empty($arItem["CATALOG_PRICE"]) ? PriceFormat($arItem["CATALOG_PRICE"])."/".$arItem["CATALOG_MEASURE_NAME"] : " - ")?></td>
			<td><?=(!empty($arItem["END_DATE"]) ? FormatDateFromDB($arItem["END_DATE"], "SHORT") : " - ")?></td>
			<td><?=(!empty($arItem["CATALOG_PURCHASING_PRICE"]) ? $arItem["CATALOG_PRICE"] * 100 / $arItem["CATALOG_PURCHASING_PRICE"]." %" : " - ")?></td>
		</tr>
	<?endforeach?>
	</tbody>
</table>
<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>