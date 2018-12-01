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
$this->setFrameMode(true);?>
<?/*if (!empty($arResult["ITEMS"])):?>
<select name="SHOP_ID" id="SHOP_ID" onchange="if (this.value) window.location.href=this.value">
<?$frame = $this->createFrame("SHOP_ID")->begin("");?>
<?foreach ($arResult["ITEMS"] as $arItem):?>
	<option value="<?=$APPLICATION->GetCurPageParam("SHOP_ID=".$arItem["SHOP_ID"], array("SHOP_ID"))?>"<?if($arItem["SELECTED"]):?> selected<?endif?>><?=$arItem["NAME"]?></option>
<?endforeach?>
<?$frame->end();?>
</select>
<?endif*/?>
<?if (is_array($arResult["ITEMS"]) && count($arResult["ITEMS"] > 1)):?>
<div class="btn-group btn-group-option" style="float: right;">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> 
	<?foreach ($arResult["ITEMS"] as $arItem)
		if($arItem["SELECTED"]):?>
		<span class="user-name"><?=$arItem["NAME"]?></span> <i class="fa fa-caret-down"></i> 
		<?endif?>
	</button>
	<ul class="dropdown-menu pull-right" role="menu">
	<?$frame = $this->createFrame("SHOP_ID")->begin("");?>
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<li>
		<?if($arItem["SELECTED"]):?>
			<span><i class="glyphicon glyphicon-star"></i> <?=$arItem["NAME"]?></span>
		<?else:?>
			<a href="<?=$APPLICATION->GetCurPageParam("SHOP_ID=".$arItem["SHOP_ID"], array("SHOP_ID"))?>">
				<?=$arItem["NAME"]?></a>
		<?endif?>
		</li>
	<?endforeach?>
		<?/*<li><a href="#"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>
		<li class="divider"></li>
		<li><a href="#"><i class="glyphicon glyphicon-log-out"></i>Sign Out</a></li>*/?>
	<?$frame->end();?>
	</ul>
</div>
<?endif?>