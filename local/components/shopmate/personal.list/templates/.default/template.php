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
<?/*<a href="<?=$arParams["EDIT_URL"]?>?edit=Y" class="btn btn-primary pull-right">Добавить сотрудника</a>*/?>
<table class="table mb30">
	<thead>
		<tr>
			<th></th>
			<th>Подразделение</th>
			<th>Процент с продаж</th>
			<th>Дата выхода на работу</th>
			<th>Зарплата</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
		<tr>
			<td><?=$arItem["NAME"]?></td>
			<td><?=$arItem["DEPARTMENT"]?></td>
			<td><?=IntVal($arItem["RATE"])?> %</td>
			<td><?=$arItem["START_DATE"]?></td>
			<td ><?=PriceFormat($arItem["SALARY"])?></td> 
			<td><a class="btn pull-right btn-default btn-xs btn-bordered" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>">редактировать</a></td>
		</tr>
	<?endforeach?>
	</tbody>
</table>
<?/*<div class="row table-bordered container-fluid">
	<div class="col-md-10 col-md-offset-1">
		<div class="row text-center">
			<div class="col-md-3">
				&nbsp;
			</div>
			<div class="col-md-2">
				Подразделение
			</div>
			<div class="col-md-2">
				Процент с продаж
			</div>
			<div class="col-md-2">
				Дата выхода на работу
			</div>
			<div class="col-md-1">
				Зарплата
			</div>
			<div class="col-md-1">
				
			</div>
		</div>
	</div>
	<div class="col-md-10 col-md-offset-1">
	<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
		<div class="row table-bordered">
			<div class="col-md-3">
				<?=$arItem["NAME"]?>
			</div>
			<div class="col-md-2">
				<?=$arItem["DEPARTMENT"]?>
			</div>
			<div class="col-md-2">
				<?=$arItem["RATE"]?>%
			</div>
			<div class="col-md-2">
				<?=$arItem["START_DATE"]?>
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










<?/*

<table class="data-table">
<?if($arResult["NO_USER"] == "N"):?>
	<thead>
		<tr>
			<td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?=GetMessage("IBLOCK_ADD_LIST_TITLE")?></td>
		</tr>
	</thead>
	<tbody>
	<?if (count($arResult["ELEMENTS"]) > 0):?>
		<?foreach ($arResult["ELEMENTS"] as $arElement):?>
		<tr>
			<td><!--a href="detail.php?CODE=<?=$arElement["ID"]?>"--><?=$arElement["NAME"]?><!--/a--></td>
			<td><small><?=is_array($arResult["WF_STATUS"]) ? $arResult["WF_STATUS"][$arElement["WF_STATUS_ID"]] : $arResult["ACTIVE_STATUS"][$arElement["ACTIVE"]]?></small></td>
			<?if ($arResult["CAN_EDIT"] == "Y"):?>
			<td><?if ($arElement["CAN_EDIT"] == "Y"):?><a href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arElement["ID"]?>"><?=GetMessage("IBLOCK_ADD_LIST_EDIT")?><?else:?>&nbsp;<?endif?></a></td>
			<?endif?>
			<?if ($arResult["CAN_DELETE"] == "Y"):?>
			<td><?if ($arElement["CAN_DELETE"] == "Y"):?><a href="?delete=Y&amp;CODE=<?=$arElement["ID"]?>&amp;<?=bitrix_sessid_get()?>" onClick="return confirm('<?echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $arElement["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM")))?>')"><?=GetMessage("IBLOCK_ADD_LIST_DELETE")?></a><?else:?>&nbsp;<?endif?></td>
			<?endif?>
		</tr>
		<?endforeach?>
	<?else:?>
		<tr>
			<td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?=GetMessage("IBLOCK_ADD_LIST_EMPTY")?></td>
		</tr>
	<?endif?>
	</tbody>
<?endif?>
	<tfoot>
		<tr>
			<td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?if ($arParams["MAX_USER_ENTRIES"] > 0 && $arResult["ELEMENTS_COUNT"] < $arParams["MAX_USER_ENTRIES"]):?><a href="<?=$arParams["EDIT_URL"]?>?edit=Y"><?=GetMessage("IBLOCK_ADD_LINK_TITLE")?></a><?else:?><?=GetMessage("IBLOCK_LIST_CANT_ADD_MORE")?><?endif?></td>
		</tr>
	</tfoot>
</table>*/?>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>