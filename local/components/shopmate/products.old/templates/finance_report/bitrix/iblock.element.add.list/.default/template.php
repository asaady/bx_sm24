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

<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>

<?/*<a href="<?=$arParams["EDIT_URL"]?>?edit=Y" class="btn btn-primary pull-right">Добавить товар</a>*/?>
<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "", $arParams, $arParams["COMPONENT"], array('HIDE_ICONS' => 'Y'));?>