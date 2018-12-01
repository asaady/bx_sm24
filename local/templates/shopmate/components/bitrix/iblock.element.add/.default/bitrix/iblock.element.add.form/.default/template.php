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

<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<div class="row">
	<div class="col-md-6">
	<?//if($arResult["ELEMENT"]["ID"] > 0):?>
		<h1>Редактирование - <?=$arResult["ELEMENT"]["NAME"]?></h1>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.element", "", $arParams, $arParams["COMPONENT"], array('HIDE_ICONS' => 'Y'));?>
	<?/*else:?>
		<h1>Добавление товара</h1>
		<?include($_SERVER["DOCUMENT_ROOT"].str_ireplace("iblock.element.add.form", "catalog.element", $templateFolder)."/template.php");?>
	<?endif*/?>
	</div>
	<div class="col-md-6">
		<h2>История изменений</h2>
	</div>
 </div>