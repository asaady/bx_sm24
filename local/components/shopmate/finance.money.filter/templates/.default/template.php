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
$this->setFrameMode(false);?>

<?foreach ($arResult["FILTER"] as $filter):?>
	<a class="btn btn-default<?if(empty(array_diff_assoc($arResult["ITEM"], $filter["CONDITION"]))):?> active<?endif?>" href="<?=$arParams["LIST_URL"]?>?<?=http_build_query($filter["CONDITION"])?>&filter=Y"><?=$filter["TITLE"]?></a>
<?endforeach?>