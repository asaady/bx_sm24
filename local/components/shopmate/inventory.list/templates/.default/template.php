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
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = "Инвентаризация успешно добавлена.";?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = "Инвентаризация успешно изменена.";?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<table class="table mb30">
	<thead>
		<tr>
			<th><a href="<?=$arResult["SORTS"]["ID"]["URL"]?>">ID</a></th>
			<th><a href="<?=$arResult["SORTS"]["DATE"]["URL"]?>">Дата активности</a></th>
			<th><a href="<?=$arResult["SORTS"]["PRODUCTS_CNT"]["URL"]?>">Кол-во позиций</a></th>
			<th><a href="<?=$arResult["SORTS"]["COMMENT"]["URL"]?>">Комментарий</a></th>
			<th><a href="<?=$arResult["SORTS"]["ACTIVE"]["URL"]?>">Активна</a></th>
			<?if($arResult["CAN_EDIT"]):?><th></th><?endif?>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<tr>
			<td><?=$arItem["ID"]?></td>
			<td><?=$arItem["DATE"]?><?if($arItem["PRIMARY"] == "Y"):?> (первичная)<?endif?></td>
			<td><?=$arItem["PRODUCTS_CNT"]?></td>
			<td><?=$arItem["COMMENT"]?></td>
			<td><?=$arItem["ACTIVE"]?></td>
			<?if($arResult["CAN_EDIT"]):?><td><a class="btn pull-right btn-default btn-xs btn-bordered" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>"><?if($arItem["ACTIVE"] == "Y"):?>редактировать<?else:?>посмотреть<?endif?></a></td><?endif?>
		</tr>
	<?endforeach?>
	</tbody>
</table>
<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>