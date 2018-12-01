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
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = Loc::getMessage("MESSAGE_ADD");?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = Loc::getMessage("MESSAGE_UPDATE");?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form class="panel panel-body<?if($arResult["CAN_EDIT"] == "Y"):?> save_alert<?endif?> form_switch load_info" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
<?if($_REQUEST["iframe"] != "y"):?>
	<div class="mb30">
		<h1><?if($arParams["ID"] <= 0):?><?=Loc::getMessage("TITLE_ADD", array("#SECTION_NAME#" => Loc::getMessage("SITE_SECTION_NAME")))?><?else:?><?=Loc::getMessage("TITLE_UPDATE", array("#SECTION_NAME#" => Loc::getMessage("SITE_SECTION_NAME")))?><?endif?></h1>
	</div>
<?endif?>
	<?=bitrix_sessid_post()?>
<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
<?if ($arProperty["PROPERTY_TYPE"] == "H"):?>
	<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?elseif ($arProperty["PROPERTY_TYPE"] == "SUBLIST"):?>
	<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?else:?>
	<fieldset class="form-group"<?if($arProperty["REFRESH_ID"] > 0):?> data-rfrsh_id="<?=$arProperty["REFRESH_ID"]?>"<?endif?>>
		<label class="control-label col-sm-4"<?if($arProperty["REFRESH_TITLE_ID"] > 0):?> data-rfrsh_title_id="<?=$arProperty["REFRESH_TITLE_ID"]?>"<?if(!empty($arProperty["REFRESH_TITLE"])):?> data-rfrsh_title="<?=$arProperty["REFRESH_TITLE"]?>"<?endif?><?endif?>>
		<?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?>
		<img style="width:25px; height:25px; display:none;" src="/local/components/shopmate/client.detail/templates/.default/loader.gif">
		</label>
		<div class="col-sm-8">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	</fieldset>
<?endif?>
<?endforeach?>
	<div class="form-group">
<?if($arResult["CAN_EDIT"] == "Y"):?>
	<?if (strlen($arParams["LIST_URL"]) > 0 && $_REQUEST["ajax"] != "y"):?>
		<input class="btn btn-primary btn-lg" type="submit" name="submit" value="<?=Loc::getMessage("BUTTON_SAVE")?>" />
	<?endif?>	
		<input class="btn btn-lg" type="submit" name="apply" value="<?=Loc::getMessage("BUTTON_APPLY")?>" />
<?endif?>
	<?if (strlen($arParams["LIST_URL"]) > 0 && $_REQUEST["iframe"] != "y" && $_REQUEST["ajax"] != "y"):?>
		<input class="btn btn-danger btn-lg"
			type="button"
			name="iblock_cancel"
			value="Отмена"
			onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
		>
	<?endif?>
	</div>
</form>