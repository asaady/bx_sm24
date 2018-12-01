<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

//echo "<pre>"; print_r($arResult);echo "</pre>";

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>

<?if($arResult["NavPageCount"] > 1):?>
	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
	<button class="btn btn-default btn-lg btn-block" data-pager="" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-loading-text="Загрузка...">Еще</button>
	<br clear="all">
	<?endif?>
<ul class="pagination pagination-lg mt10">
	<li class="disabled"><a href="#"><i class="fa fa-angle-left"></i></a></li>
	<?if($arResult["nStartPage"] > 1):?>
		<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
		<li><span>&#8230</span></li>
	<?endif?>
	<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>
		<li<?if($arResult["nStartPage"] == $arResult["NavPageNomer"]):?> class="active"<?endif?>><a href="<?if($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?><?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?><?else:?><?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?><?endif?>"><?=$arResult["nStartPage"]?></a></li>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>
	<?if($arResult["nEndPage"] < $arResult["NavPageCount"]):?>
		<li><span>&#8230</span></li>
		<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li>
	<?endif?>
	<li class="disabled"><a href="#"><i class="fa fa-angle-right"></i></a></li>
</ul>
<?endif;?>