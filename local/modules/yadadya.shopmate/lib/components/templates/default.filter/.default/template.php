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
use Yadadya\Shopmate\Components\Template,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<form action="<?=POST_FORM_ACTION_URI?>" method="get" enctype="multipart/form-data">
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):
	$cnt = 0;?>
	<div class="row mb20">
	<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
<?if ($cnt && !($cnt % 4) /* || $arProperty["NEW_LINE"] == "Y"*/):?>
	</div>
	<div class="row mb20">
<?endif?>
		<div class="col-lg-3">
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
		<?$cnt++;
	endforeach;?>
		<div class="col-md-3">
			<div class="btn-list clearfix">
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