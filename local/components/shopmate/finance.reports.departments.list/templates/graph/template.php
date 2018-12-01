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
<?if(!empty($arParams["BACK_URL"])):?><p class="mb15">Показать <a href="#" data-url="<?=$arParams["BACK_URL"]?>">категорию</a></p><?endif?>
<table class="table mb30">
	<thead>
		<tr>
			<th>Наименование</th>
			<th><?if($_REQUEST["graph"] == "SALE"):?>Продажи<?elseif($_REQUEST["graph"] == "PURCHASE"):?>Закупки<?elseif($_REQUEST["graph"] == "PROFIT"):?>Прибыль<?endif?></th>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
		<tr>
			<td><?=$arItem["NAME"]?></td>
			<td><?=PriceFormat($arItem[$_REQUEST["graph"]])?> (<?=PriceFormat($arItem[$_REQUEST["graph"]."_PREV"])?>)</td>
		</tr>
	<?endforeach?>
	</tbody>
</table>
<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>