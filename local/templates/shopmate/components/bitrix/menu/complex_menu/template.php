<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?if(!empty($arResult)):?>
<?foreach($arResult as $keyItem => $arItem):?>
<a class="btn btn-default<?if($arItem["SELECTED"]):?> active<?endif?>" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
<?endforeach?>
<?endif?>