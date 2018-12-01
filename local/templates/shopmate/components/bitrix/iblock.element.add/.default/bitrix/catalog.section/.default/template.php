<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?//print_p($arResult["ITEMS"])?>
<div class="row table-bordered container-fluid">
	<div class="col-md-10 col-md-offset-1">
		<div class="row text-center">
			<div class="col-md-3">
				Наименование
			</div>
			<div class="col-md-1">
				Наличие на складе
			</div>
			<div class="col-md-2">
				Закупочная цена
			</div>
			<div class="col-md-2">
				Стандартная цена на продажу
			</div>
			<?/*<div class="col-md-2">
				Срок годности
			</div>*/?>
			<div class="col-md-1">
				Оплата
			</div>
			<div class="col-md-1">
				
			</div>
		</div>
	</div>
	<div class="col-md-10 col-md-offset-1">
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<div class="row table-bordered">
			<div class="col-md-3">
				<?=$arItem["NAME"]?>
			</div>
			<div class="col-md-1">
				<?=$arItem["CATALOG_QUANTITY"]?> <?=$arItem["CATALOG_MEASURE_NAME"]?>
			</div>
			<div class="col-md-2">
				<?=PriceFormat($arItem["CATALOG_PURCHASING_PRICE"])?>
			</div>
			<div class="col-md-2">
				<?=$arItem["MIN_PRICE"]["PRINT_VALUE"]?>
			</div>
			<?/*<div class="col-md-2">
				<?=$arItem["PROPERTIES"]["EXPIRATION_DATE"]["VALUE"]?>
			</div>*/?>
			<div class="col-md-1">
				<p class="bg-success">100%</p>
				<?/*<p class="bg-warning">...</p>
				<p class="bg-danger">...</p>*/?>
			</div>
			<div class="col-md-1">
			<?if ($arResult["CAN_EDIT"] == "Y"):?>
				<a class="btn btn-default" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>">редактировать</a>
			<?endif?>
			</div>
		</div>
	<?endforeach?>
	</div>
</div>