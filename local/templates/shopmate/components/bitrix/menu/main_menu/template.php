<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?if(!empty($arResult)):?>
	<ul class="nav nav-pills nav-stacked" data-spy="scroll" data-target=".navbar">
	<?foreach($arResult as $arItem):?>
		<li class="<?/*if(!empty($arItem["MENU"])):?>parent<?endif*/?><?if($arItem["SELECTED"]):?> active<?endif?>">
			<a href="<?=$arItem["LINK"]?>"><?if (!empty($arItem["PARAMS"]["ICON"])):?><i class="fa fa-<?=$arItem["PARAMS"]["ICON"]?>"></i> <?endif?><span><?=$arItem["TEXT"]?></span></a>
		<?if(!empty($arItem["MENU"])):?>
			<ul class="children" style="display: block;">
			<?foreach($arItem["MENU"] as $arItem2):?>
				<li<?if($arItem2["SELECTED"]):?> class="active"<?endif?>>
					<a href="<?=$arItem2["LINK"]?>"><?=$arItem2["TEXT"]?></a>
				<?if(!empty($arItem2["MENU"])):?>
					<ul class="children"  style="display: block;">
					<?foreach($arItem2["MENU"] as $arItem3):?>
						<li<?if($arItem3["SELECTED"]):?> class="active"<?endif?>><a style="padding-left: 50px;" href="<?=$arItem3["LINK"]?>"><?=$arItem3["TEXT"]?></a></li>
					<?endforeach?>
					</ul>
				<?endif?>
				</li>
			<?endforeach?>
			</ul>
		<?endif?>
		</li>
	<?endforeach?>
	</ul>
<?endif?>