<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?if(!empty($arResult)):?>
	<ul class="nav nav-tabs nav-primary">
	<?foreach($arResult as $keyItem => $arItem):?>
        <li<?if($arItem["SELECTED"]):?> class="active"<?endif?>><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a></li>
	<?endforeach?>
	</ul>
<?endif?>