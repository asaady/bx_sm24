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
		<h1>Баланс</h1>
	</div>
<?endif?>
	<?=bitrix_sessid_post()?>
<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
	<div class="form-group<?if ($propertyID == "TYPE"):?> hidden<?endif?>">
		<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-sm-8">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>						
		</div>
	</div>
<?endforeach;?>
</form>