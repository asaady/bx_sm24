<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);
$newCnt = count($arResult["ITEMS"]);
?>
<div class="btn-group btn-group-list btn-group-notification notify__panel">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-info-circle"></i> <span class="badge"<?if ($newCnt <= 0):?> style="display: none;"<?endif?>><?=$newCnt?></span> </button>
	<div class="dropdown-menu pull-right">
		<h5>Новости приложения</h5>
		<ul class="media-list dropdown-list">
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<li class="media"> 
				<div class="media-body"> 
					<strong><?echo $arItem["NAME"]?></strong> <br />
					<?echo $arItem["PREVIEW_TEXT"];?>
					<small class="date"><i class="fa fa-calendar"></i> <?echo $arItem["DISPLAY_ACTIVE_FROM"]?></small> 
				</div>
			</li>
		<?endforeach?>
		</ul>
		<div class="dropdown-footer text-center"> <a href="/app_news/" class="link">Смотреть все новости</a> </div>
	</div>
	<!-- dropdown-menu --> 
</div>
<!-- btn-group -->