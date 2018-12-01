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
global ${$arParams["FILTER_NAME"]};
$this->setFrameMode(false);?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<table class="table table-hover mb30">
	<thead>
		<tr>
			<th><a href="<?=$arResult["SORTS"]["ID"]["URL"]?>">Кому мы должны</a></th>
			<th><a href="<?=$arResult["SORTS"]["MEMBERS"]["URL"]?>">Кому мы должны</a></th>
			<th><a href="<?=$arResult["SORTS"]["SUMM"]["URL"]?>">Сумма</a></th>
			<th><a href="<?=$arResult["SORTS"]["REASON"]["URL"]?>">Накладная</a></th>
			<th><a href="<?=$arResult["SORTS"]["TOTAL_SUMM"]["URL"]?>">Общая сумма накладной</a></th>
			<th><a href="<?=$arResult["SORTS"]["DATE"]["URL"]?>">Дата последнего платежа</a></th>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<tr>
			<td><?=$arItem["ID"]?></td>
			<td><?if(!empty($arItem["MEMBERS_URL"])):?><a href="<?=$arItem["MEMBERS_URL"]?>" target="_blank" class="modal_noajax"><?endif?><?=$arItem["MEMBERS"]?><?if(!empty($arItem["MEMBERS_URL"])):?></a><?endif?></td>
			<td><?=PriceFormat($arItem["SUMM"])?></td>
			<td><?if(!empty($arItem["REASON_URL"])):?><a href="<?=$arItem["REASON_URL"]?>" target="_blank" class="modal_noajax"><?endif?><?=$arItem["REASON"]?><?if(!empty($arItem["REASON_URL"])):?></a><?endif?></td>
			<td><?=PriceFormat($arItem["TOTAL_SUMM"])?></td>
			<td><?=$arItem["DATE"]?></td>
		</tr>
	<?endforeach?>
	</tbody>
</table>

<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>