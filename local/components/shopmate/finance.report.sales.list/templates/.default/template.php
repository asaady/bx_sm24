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
global ${$arParams["FILTER_NAME"]};
$this->setFrameMode(false);?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<?\Yadadya\Shopmate\FinanceReport::updateBlock();?>
<div class="row mb20 ajax_block" id="report_ajax">
<?if ($_REQUEST["ajax_link"] == "Y") $APPLICATION->RestartBuffer();?>
<h4><?=$arResult["BACK_URL"]["NAME"]?></h4>
<?if (!empty($arResult["BACK_URL"]["PARENT_URL"])):?>
	<a href="<?=$arResult["BACK_URL"]["PARENT_URL"]?>" data-loadto="report_ajax">вернуться в <?=$arResult["BACK_URL"]["PARENT_NAME"]?></a><br><br>
<?endif?>
<table class="table table-hover mb30">
	<thead>
		<tr>
		<?foreach ($arResult["SORTS"] as $arSort):?>
			<th><a href="<?=$arSort["URL"]?>" data-loadto="report_ajax"><?=(!empty($arSort["NAME"]) ? $arSort["NAME"] : $arSort["FIELD"])?></a></th>
		<?endforeach?>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
		<tr ondblclick="window.location = '<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>';">
		<?foreach ($arResult["SORTS"] as $arSort):?>
			<td>
			<?if ($arSort["FIELD"] == "SECTIONS_NAME"):?>
				<a href="<?=$APPLICATION->GetCurPageParam("SECTION=".$arItem["ID"], array("SECTION"))?>" data-loadto="report_ajax"><?=$arItem[$arSort["FIELD"]]?></a>
			<?elseif ($arSort["FIELD"] == "ELEMENTS_NAME"):?>
				<a href="<?=$APPLICATION->GetCurPageParam("ELEMENT=".$arItem["ID"], array("SECTION"))?>" data-loadto="report_ajax"><?=$arItem[$arSort["FIELD"]]?></a>
			<?elseif ($arSort["FIELD"] == "ORDER_NAME"):?>
				<a href="<?=$APPLICATION->GetCurPageParam("ORDER=".$arItem["ORDER_ID"], array("SECTION"))?>" data-loadto="report_ajax"><?=$arItem[$arSort["FIELD"]]?></a>
			<?else:?>
				<?=$arItem[$arSort["FIELD"]]?>
			<?endif?>
			</td>
		<?endforeach?>
		</tr>
	<?endforeach?>
	</tbody>
</table>

<div class="row">
	<div class="col-md-12">
		<?=str_ireplace("href=", "data-loadto=\"report_ajax\" href=", $arResult["NAV_STRING"])?>
	</div>
</div>
<?if ($_REQUEST["ajax_block"] == "Y") die();?>
</div>