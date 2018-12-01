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
?>
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = "Информация успешно добавлена.";?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = "Информация успешно изменена.";?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form class="panel panel-body save_alert form_switch" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
<?if($_REQUEST["iframe"] != "y"):?>
	<div class="mb30">
		<h1><?if($_REQUEST["add_price"] == "Y"):?>Внесение наличных<?elseif($arParams["ID"] <= 0):?>Новая выплата<?else:?>Выплата от <?=$arResult["ITEM"]["DATE"]?><?endif?></h1>
	</div>
<?endif?>
	<?=bitrix_sessid_post()?>
<?foreach ($arResult["PROPERTY_LIST_GROUP"][0] as $propertyID):
	$arProperty = $arResult["PROPERTY_LIST"][$propertyID];?> 
	<div class="form-group<?if ($propertyID == "TYPE"):?> hidden<?endif?>">
	<?if ($propertyID == "OUTGO"):?>
		<div class="btn-list form_switch__vals" data-toggle="buttons">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	<?else:?>
		<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-sm-8">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>		
		</div>
	<?endif?>
	</div>
<?endforeach?>

<?foreach ($arResult["PROPERTY_LIST_GROUP"][1] as $outgo => $prop_list):?>
	<fieldset class="form_switch__case price_ins" data-case="<?=$outgo?>">
	<?foreach ($prop_list as $propertyID):
		$arProperty = $arResult["PROP_LIST"][$outgo][$propertyID];?> 
		<div class="form-group">
			<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
			<div class="col-sm-8">
				<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>		
			</div>
		</div>
	<?endforeach?>
	</fieldset>
<?endforeach?>
<?/*foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
	<div class="form-group<?if ($propertyID == "TYPE"):?> hidden<?endif?>">
		<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-sm-8">
		<?if ($propertyID == "OUTGO"):?>
			<div class="btn-list" data-toggle="buttons">
		<?endif?>
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>		
		<?if ($propertyID == "OUTGO"):?>
			</div>
		<?endif?>					
		</div>
	</div>
<?endforeach;*/?>
	<div class="form-group">
	<?if (strlen($arParams["LIST_URL"]) > 0):?>
		<input class="btn btn-primary btn-lg" type="submit" name="<?if($_REQUEST["iframe"] != "y"):?>submit<?else:?>apply<?endif?>" value="Отправить" />
	<?endif?>	
	<?if (strlen($arParams["LIST_URL"]) > 0 && $_REQUEST["iframe"] != "y"):?>
		<input class="btn btn-danger btn-lg"
			type="button"
			name="iblock_cancel"
			value="Отмена"
			onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
		>
	<?endif?>
	</div>
</form>