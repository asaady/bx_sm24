<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

/** @var PageNavigationComponent $component */
$component = $this->getComponent();

$this->setFrameMode(true);
?>

<?if($arResult["PAGE_COUNT"] > 1):?>

<?/*if($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]):?>
	<button class="btn btn-default btn-lg btn-block" data-pager="" href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]+1))?>" data-loading-text="Загрузка...">Еще</button>
	<br clear="all">
<?endif*/?>
<ul class="pagination pagination-lg mt10">
<?if ($arResult["CURRENT_PAGE"] > 1):?>
	<?if ($arResult["CURRENT_PAGE"] > 2):?>
		<li><a href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]-1))?>"><i class="fa fa-angle-left"></i></a></li>
	<?else:?>
		<li><a href="<?=htmlspecialcharsbx($arResult["URL"])?>"><i class="fa fa-angle-left"></i></a></li>
	<?endif?>
		<li><a href="<?=htmlspecialcharsbx($arResult["URL"])?>">1</a></li>
<?else:?>
		<li class="disabled"><span><i class="fa fa-angle-left"></i></span></li>
		<li class="active"><span>1</span></li>
<?endif?>

<?$page = $arResult["START_PAGE"] + 1;
while($page <= $arResult["END_PAGE"]-1):?>
	<?if ($page == $arResult["CURRENT_PAGE"]):?>
		<li class="active"><span><?=$page?></span></li>
	<?else:?>
		<li><a href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($page))?>"><?=$page?></a></li>
	<?endif?>
	<?$page++?>
<?endwhile?>

<?if($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]):?>
	<?if($arResult["PAGE_COUNT"] > 1):?>
		<li><span>&#8230</span></li>
		<li><a href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["PAGE_COUNT"]))?>"><?=$arResult["PAGE_COUNT"]?></a></li>
	<?endif?>
		<li><a href="<?=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]+1))?>"><i class="fa fa-angle-right"></i></a></li>
<?else:?>
	<?if($arResult["PAGE_COUNT"] > 1):?>
		<li class="active"><span><?=$arResult["PAGE_COUNT"]?></span></li>
	<?endif?>
		<li class="disabled"><span><i class="fa fa-angle-right"></i></span></li>
<?endif?>
</ul>

<?endif;?>