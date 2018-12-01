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
<form class="panel panel-body save_alert form_switch" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
<?if($_REQUEST["iframe"] != "y"):?>
	<div class="mb30">
		<h1><?if ($arParams["ID"] <= 0):?>Новая накладная<?else:?>Накладная №<?=$arResult["ITEM"]["NUMBER_DOCUMENT"]?> от <?=$arResult["ITEM"]["DATE_DOCUMENT"]?><?endif?></h1>
	</div>
<?endif?>
	<?=bitrix_sessid_post()?>
<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
<?if ($arProperty["PROPERTY_TYPE"] == "H"):?>
	<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?elseif ($arProperty["PROPERTY_TYPE"] == "SUBLIST"):?>
	<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?else:?>
	<?if($propertyID == "DESCRIPTION"):?>
	
	<?endif?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-sm-8">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	</div>
<?endif?>
<?endforeach?>
	<div class="form-group">
<?if($arResult["CAN_EDIT"] == "Y"):?>
		<input type="hidden" name="<?=(strlen($arParams["LIST_URL"]) > 0 ? "submit" : "apply")?>" value="act" />
		<input class="btn btn-lg" type="submit" name="message" value="Отправить комментарий" />
	<?if ($arResult["SHOW_EDIT_BTN"] == "Y"):?>
		<input class="btn btn-lg" type="submit" name="<?=(strlen($arParams["LIST_URL"]) > 0 ? "submit" : "apply")?>" value="Отправить накладную" />
	<?endif?>
	<?if ($arResult["SHOW_ACCEPT_BTN"] == "Y"):?>
		<input class="btn btn-lg" type="submit" name="act_accept" value="Подтвердить акт <?=($arResult["ITEM"]["STATUS"] == "A" ? "соответствия" : ($arResult["ITEM"]["STATUS"] == "C" ? "расхождения" : ($arResult["ITEM"]["STATUS"] == "R" ? "отказа" : "")))?>" />
	<?endif?>
	<?if ($arResult["SHOW_ACT_BTN"] == "Y"):?>
		<input class="btn btn-primary btn-lg" type="submit" name="accepted" value="Акт соответствия" />
		<input class="btn btn-warning btn-lg" type="submit" name="changed" value="Акт расхождения" />
		<input class="btn btn-danger btn-lg" type="submit" name="rejected" value="Акт отказа" />
	<?endif?>
<?endif?>
	<?if (strlen($arParams["LIST_URL"]) > 0 && $_REQUEST["iframe"] != "y"):?>
		<input class="btn btn-link btn-lg"
			type="button"
			name="iblock_cancel"
			value="Список накладных"
			onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
		>
	<?endif?>
	</div>
	<hr />
	<?foreach ($arResult["ITEM"]["CHAT"] as $arChat):?>
	<?=$arChat["NAME"]?> [<?=$arChat["DATE"]?>]:<br />
	<?=$arChat["DESCRIPTION"]?><br /><br />
	<?endforeach?>
</form>