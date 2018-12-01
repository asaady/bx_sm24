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
$this->setFrameMode(false);

use Yadadya\Shopmate\Components\Template;?>

<form class="cmpfilter" action="<?=POST_FORM_ACTION_URI?>" method="get" enctype="multipart/form-data">
	<?/*<p>Мои фильтры:&nbsp;&nbsp; <a href="#">Товары этого месяца</a>&nbsp;&nbsp;<a href="#">Неоплаченные товары</a></p>*/?>
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
	<div class="row mb20">
	<?foreach ($arResult["PROPERTY_LIST_GROUP"][0] as $propertyID):?> 
		<div class="col-lg-3">
			<?Template::ShowInput($propertyID, $arResult["PROPERTY_LIST"][$propertyID], $arResult["ITEM"][$propertyID]);?>
		</div>
	<?endforeach;?>
	</div>
	<div class="row">
		<div class="col-md-9">
		<?foreach ($arResult["PROPERTY_LIST_GROUP"][2] as $key => $propertyID):?> 
			<div class="<?if($arResult["PROPERTY_LIST"][$propertyID]["LIST_TYPE"] == "C"):?>ckbox ckbox-primary <?endif?>inline-block<?if($key > 0):?> ml10<?endif?>">
				<?Template::ShowInput($propertyID, $arResult["PROPERTY_LIST"][$propertyID], $arResult["ITEM"][$propertyID]);?>
			</div>
		<?endforeach;?>
			<!-- rdio --> 
		</div>
		<div class="col-md-3">
			<div class="btn-list clearfix">
				<?/*<a href="<?=$APPLICATION->GetCurPageParam("print=Y", array("print"))?>" class="btn pull-right modal_ajax" data-title="Идет печать..." data-backdrop="static">Печать</a>*/?>
			<?if (strlen($arParams["LIST_URL"]) > 0):?>
				<input class="btn btn-primary pull-right" type="submit" name="filter" value="Найти" />
			<?endif?>
			<?if (strlen($arParams["LIST_URL"]) > 0 && !empty($_REQUEST["filter"])):?>
				<input class="btn btn-danger pull-right"
					type="button"
					name="iblock_cancel"
					value="Сбросить"
					onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
				>
			<?endif?>
			</div>
		</div>
	</div>
<?endif?>
</form>

