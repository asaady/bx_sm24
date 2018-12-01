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
<div class="panel panel-body">
<?if($_REQUEST["iframe"] != "y"):?>
	<div class="mb30">
		<h1><?if($arParams["ID"] <= 0):?><?=Loc::getMessage("TITLE_ADD", array("#SECTION_NAME#" => Loc::getMessage("SITE_SECTION_NAME")))?><?else:?><?=Loc::getMessage("TITLE_UPDATE", array("#SECTION_NAME#" => Loc::getMessage("SITE_SECTION_NAME")))?><?endif?></h1>
	</div>
<?endif?>
	<?=bitrix_sessid_post()?>
<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
<?if ($arProperty["PROPERTY_TYPE"] == "H"):?>
	<?Template::PrintInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?elseif ($arProperty["PROPERTY_TYPE"] == "SUBLIST"):?>
	<?Template::PrintInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
<?else:?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
		<div class="col-sm-8">
			<?Template::PrintInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	</div>
<?endif?>
<?endforeach?>
</div>
<script type="text/javascript">
	window.print();
</script>