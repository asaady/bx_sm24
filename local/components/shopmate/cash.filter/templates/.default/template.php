<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
$this->setFrameMode(false);
use Yadadya\Shopmate\Components\Template,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<form action="<?=POST_FORM_ACTION_URI?>" method="get" enctype="multipart/form-data">
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
	<div class="row mb20">
		<div class="col-lg-2">
			<a class="btn btn-default btn-bordered" href="<?=$arResult["FILTER_DATE"]["TODAY"]?>">Все покупки дня</a>
		</div>
		<label class="control-label col-lg-1 padding10">или</label>
		<div class="col-lg-3">
			<select data-placeholder="Выберите период" class="form-control width100p" onchange="if(this.value) top.location.href=this.value;">
				<option value="">Выберите период</option>
				<option value="<?=$arResult["FILTER_DATE"]["WEEK"]?>">Текущая неделя</option>
				<option value="<?=$arResult["FILTER_DATE"]["MONTH"]?>">Текущий месяц</option>
				<option value="<?=$arResult["FILTER_DATE"]["QUARTER"]?>">Текущий квартал</option>
			</select>
		</div>
	<?foreach (array("DATE_FROM", "DATE_TO") as $propertyID):
		$arProperty = $arResult["PROPERTY_LIST"][$propertyID];?> 
		<div class="col-lg-3">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
		<?unset($arResult["PROPERTY_LIST"][$propertyID]);?>
	<?endforeach;?>
	</div>

	<div class="row">
	<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
		<div class="col-lg-3">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	<?endforeach;?>
		<div class="col-md-3" style="float:right;">
			<div class="btn-list clearfix">
			<?if (strlen($arParams["LIST_URL"]) > 0):?>
				<input class="btn btn-primary pull-right" type="submit" name="filter" value="Найти" />
			<?endif?>
			<?if (strlen($arParams["LIST_URL"]) > 0 && !empty($_REQUEST["filter"])):?>
				<input class="btn btn-danger pull-right"
					type="button"
					name="iblock_cancel"
					value="Сбросить"
					onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"].($_REQUEST["DEDUCTED"] == "N" ? "?DEDUCTED=N&filter=Y" : ""))?>';"
				>
			<?endif?>
			</div>
		</div>
	</div>
<?endif?>
</form>