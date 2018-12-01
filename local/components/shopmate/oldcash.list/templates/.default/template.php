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
<?//print_p($arResult);?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<table class="table table-striped">
	<tr>
		<th>Номер чека</th>
		<th>ID клиента</th>
		<th>ФИО/Компания</th>
		<th>Телефон</th>
		<th>Сумма покупки</th>
		<th>Дата покупки</th>
		<th>Статус</th>
		<th></th>
	</tr>
<?foreach($arResult["ELEMENTS"] as $key => $arItem):?>
	<tr>
		<td><?=$arItem["ID"]?></td>
		<td><?=$arItem["USER_ID"]?></td>
		<td><?=$arItem["USER_LAST_NAME"]?> <?=$arItem["USER_NAME"]?></td>
		<td><?=$arResult["USERS"][$arItem["USER_ID"]]["PERSONAL_PHONE"]?></td>
		<td><?=PriceFormat($arItem["PRICE"])?></td>
		<td><?=$arItem["DATE_INSERT_FORMAT"]?></td>
		<td><?=($arItem["CANCELED"] == "Y" ? "<div class=\"text-danger\">Возврат</div>" : $arResult["STATUSES"][$arItem["STATUS_ID"]]["NAME"])?></td>
		<td>
			<div class="clearfix">
				<?/*<button class="btn btn-bordered btn-default btn-xs pull-right"  data-toggle="modal" data-target=".bs-example-modal-lg-checkout">Посмотреть</button>*/?>
			<?if ($arResult["CAN_EDIT"] == "Y"):?>
				<a class="btn btn-bordered btn-default btn-xs pull-right" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>">Редактировать</a>
			<?endif?>
			</div>
		</td>
	</tr>
<?endforeach?>
</table>
<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>