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
<form action="/products/egais/?bitrix_include_areas=Y" method="get" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-10">
			<div class="ckbox ckbox-primary inline-block">
					<input id="accepted_y" value="Y" name="accepted_y" placeholder="Показывать принятую продукцию" style="display:inline;width:auto;" type="checkbox" onclick="form.submit()"
					<?if(!empty($_REQUEST["accepted_y"]) && !empty($_REQUEST["accepted_n"])):?> checked <?elseif(empty($_REQUEST["accepted_y"]) && empty($_REQUEST["accepted_n"])):?> checked 
					<?
					elseif($_REQUEST["accepted_y"] == "Y"):?> checked <?endif?>>
					</input>
				<label for="accepted_y">
					Показывать принятую продукцию
				</label>				
			</div>
			<div class="ckbox ckbox-primary inline-block m110">
					<input id="accepted_n" value="N" name="accepted_n" placeholder="Показывать непринятую продукцию" style="display:inline;width:auto;" type="checkbox"  onclick="form.submit()" 					
					<?if(!empty($_REQUEST["accepted_y"]) && !empty($_REQUEST["accepted_n"])):?> checked <?elseif(empty($_REQUEST["accepted_y"]) && empty($_REQUEST["accepted_n"])):?> checked 
					<?
					elseif($_REQUEST["accepted_n"] == "N"):?> checked <?endif?>>
					</input>
				<label for="accepted_n">
					Показывать непринятую продукцию
				</label>
			</div>
		</div>
	</div>
</form>
<table class="table table-hover mb30">
	<thead>
		<tr>
			<th>Номер накладной</th>
			<th>Кол-во наименований</th>
			<th>Общее кол-во товаров</th>
			<th>Общая цена</th>
			<th>Дата накладной</th>
			<th>Принято</th>
			<th></th>
		</tr>
	</thead>
	<tbody id="egais" data-pager_block="">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<tr>
			<td><?=$arItem["NUMBER"]?></td>
			<td><?=$arItem["CNT"]?></td>
			<td><?=$arItem["TOTAL_QUANTITY"]?></td>
			<td><?=PriceFormat($arItem["TOTAL"])?></td>
			<td><?=$arItem["DATE"]?></td>
			<td><?=($arItem["ACCEPTED"] == "Y" ? "Принято" : "Не принято")?></td>
			<td>
			<?if ($arResult["CAN_EDIT"] == "Y"):?>
				<a class="btn pull-right btn-default btn-xs btn-bordered" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>">редактировать</a>
			<?endif?>
				<?/*<button class="btn pull-right btn-default btn-xs btn-bordered">Посмотреть</button>*/?>
			</td>
		</tr>
	<?endforeach?>
	</tbody>
</table>

<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>