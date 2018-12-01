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

use Yadadya\Shopmate\Components\Template;
?>
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = "Накладная успешно добавлена.";?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = "Накладная успешно изменена.";?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>

<form class="panel panel-body save_alert" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<div class="mb30">
		<h1><?if($arParams["ID"] <= 0):?>Новая накладная<?else:?>Накладная №<?=$arResult["ITEM"]["NUMBER_DOCUMENT"]?> от <?=$arResult["ITEM"]["DATE_DOCUMENT"]?><?endif?></h1>
	</div>
	<?=bitrix_sessid_post()?>
	<div class="form-group form-horizontal">
	<?foreach ($arResult["PROPERTY_LIST_GROUP"][0] as $propertyID):
		$arProperty = $arResult["PROPERTY_LIST"][$propertyID];?> 
		<label class="control-label col-lg-1 col-xs-12 col-sm-3 col-md-3"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-lg-<?=($propertyID == 'CONTRACTOR_ID')?3:2;?> col-sm-9 col-xs-12 col-md-9">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	<?endforeach;?>
		<?/*<div class="col-lg-2 col-sm-9 col-xs-12 col-md-9">
			<a class="btn btn-primary pull-right" href="/contractors/?edit=Y&is_ajax=Y">Новый поставщик</a>
		</div>*/?>
	</div>
<?foreach ($arResult["PROPERTY_LIST_GROUP"][1] as $propertyID):
	$arProperty = $arResult["PROPERTY_LIST"][$propertyID];?>
<?if($arProperty["PROPERTY_TYPE"] == "SUBLIST" || $arProperty["PROPERTY_TYPE"] == "H"):?>
	<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?else:?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-sm-8">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>		
		</div>
	</div>
<?endif?>
<?endforeach;?>
	<div>
		<?$propertyID = $arResult["PROPERTY_LIST_GROUP"][2][0];
		$arProperty = $arResult["PROPERTY_LIST"][$propertyID];?>
		<label class="control-label"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="row">
			<div class="col-lg-4 col-sm-5">
				<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
			</div>
			<div class="col-lg-2 col-sm-1">
			</div>
			<div class="col-lg-6 col-sm-6">
			<?/*if (strlen($arParams["LIST_URL"]) > 0):?>
				<input class="btn btn-primary btn-lg" type="submit" name="submit" value="Провести накладную" />
			<?endif*/?>
			<?foreach ($arResult["BUTTONS"] as $arButton):?>
				<?Template::ShowButton($arButton);?>
			<?endforeach?>
				<?/*<input class="btn btn-primary btn-lg" type="submit" name="apply" value="Применить" />*/?>
			<?/*if (strlen($arParams["LIST_URL"]) > 0):?>
				<input class="btn btn-danger btn-lg"
					type="button"
					name="iblock_cancel"
					value="Отмена"
					onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
				>
			<?endif*/?>
			</div>
		</div>
	</div>
</form>