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
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = "Инвентаризация успешно добавлена.";?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = "Инвентаризация успешно изменена.";?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form class="panel panel-body save_alert" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<div class="mb30">
		<h1><?if($arParams["ID"] <= 0):?>Новая инвентаризация<?else:?>Инвентаризация от <?=$arParams["DATE"]?><?endif?></h1>
	</div>
	<?=bitrix_sessid_post()?>
<?if(is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
	<?foreach($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?>
		<div class="form-group">
		<?if($arProperty["PROPERTY_TYPE"] == "SUBLIST"):?>
			<?if ($arParams["ID"] > 0 && $propertyID == "PRODUCTS"):?>
				<div data-load_url="<?=$APPLICATION->GetCurPageParam("load_products=Y")?>"></div>
			<?endif?>
			<?if ($propertyID == "PRODUCTS" && $_REQUEST["load_products"] == "Y") $APPLICATION->RestartBuffer();?>
			<?if ($_REQUEST["load_products"] == "Y" || $arResult["ITEM"]["ACTIVE"] != "N"):?>
				<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
			<?endif?>
			<?if ($propertyID == "PRODUCTS" && $_REQUEST["load_products"] == "Y") die();?>
		<?else:?>
			<div class="ml10 mb30">
				<label class="control-label"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
				<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
			</div>
		<?endif?>
		</div>
	<?endforeach;?>
<?endif?>
	<div>
		<div class="btn-list">
		<?if($arResult["ITEM"]["ACTIVE"] != "N"):?>
			<input class="btn btn-primary btn-lg" type="submit" name="submit" value="Сохранить и закончить инвентаризацию" />
			<input class="btn btn-lg" type="submit" name="apply" value="Сохранить промежуток" />
		<?endif?>
		<?if(strlen($arParams["LIST_URL"]) > 0):?>
			<input class="btn btn-danger btn-lg"
				type="button"
				name="iblock_cancel"
				value="Отмена"
				onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
			>
		<?endif?>
		</div>
	</div>
</form>