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
			<th><a href="<?=$arResult["SORTS"]["ID"]["URL"]?>">ID</a></th>
			<th><a href="<?=$arResult["SORTS"]["DATE"]["URL"]?>">Дата</a></th>
			<th><a href="<?=$arResult["SORTS"]["SUMM"]["URL"]?>">Сумма</a></th>
			<th><a href="<?=$arResult["SORTS"]["REASON"]["URL"]?>">Основание</a></th>
			<?if(empty(${$arParams["FILTER_NAME"]}["TYPE"])):?><th><a href="<?=$arResult["SORTS"]["TYPE"]["URL"]?>">Тип</a></th><?endif?>
			<th><a href="<?=$arResult["SORTS"]["MEMBERS"]["URL"]?>">Кому выдано</a></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<tr>
			<td><?=$arItem["ID"]?></td>
			<td><?=$arItem["DATE"]?></td>
			<td><?=PriceFormat($arItem["SUMM"])?></td>
			<td><?if(!empty($arItem["REASON_URL"])):?><a href="<?=$arItem["REASON_URL"]?>" target="_blank" class="modal_noajax"><?endif?><?=$arItem["REASON"]?><?if(!empty($arItem["REASON_URL"])):?></a><?endif?></td>
			<?if(empty(${$arParams["FILTER_NAME"]}["TYPE"])):?><td><?=($arItem["TYPE"] == "cash" ? "Наличные" : ($arItem["TYPE"] == "clearing" ? "Безналичные" : "-"))?></td><?endif?>
			<td><?if(!empty($arItem["MEMBERS_URL"])):?><a href="<?=$arItem["MEMBERS_URL"]?>" target="_blank" class="modal_noajax"><?endif?><?=$arItem["MEMBERS"]?><?if(!empty($arItem["MEMBERS_URL"])):?></a><?endif?></td>
			<td class="btn-list">
				<?if($arResult["CAN_DELETE"] == "Y"):?><a class="btn pull-right btn-danger btn-xs btn-bordered" href="<?=$arParams["EDIT_URL"]?>?delete=Y&amp;CODE=<?=$arItem["ID"]?>">Удалить</a><?endif?>
				<a class="btn pull-right btn-default btn-xs btn-bordered" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;<?if ($arItem["OUTGO"] == "deposit"):?>add_price=Y&amp;<?endif?>CODE=<?=$arItem["ID"]?>"><?if($arResult["CAN_EDIT"] == "Y"):?>редактировать<?else:?>посмотреть<?endif?></a>
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