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
$this->setFrameMode(false);
use Yadadya\Shopmate\Components\Template,
	Bitrix\Main\Localization\Loc;?>

<div class="panel panel-primary widget-messaging" id="panel_ajax">
<?if ($_REQUEST["ajax_link"] == "Y") $APPLICATION->RestartBuffer();?>
	<div class="panel-heading"> 
		<!-- pull-right -->
		<h3 class="panel-title">Транзакции</h3>
	</div> 
	<div class="panel">
		<table class="table table-hover">
			<thead>
				<tr>
				<?foreach ($arResult["SORTS"] as $arSort):?>
					<th><a href="<?=$arSort["URL"]?>" data-loadto="panel_ajax"><?=(!empty($arSort["NAME"]) ? $arSort["NAME"] : $arSort["FIELD"])?></a></th>
				<?endforeach?>
				</tr>
			</thead>
			<tbody>
				<?foreach($arResult["ITEMS"] as $key => $arItem):?>
					<tr<?/* ondblclick="window.location = '<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arItem["ID"]?>';"*/?>>
					<?foreach ($arResult["SORTS"] as $arSort):?>
						<td><?=$arItem[$arSort["FIELD"]]?></td>
					<?endforeach?>
					</tr>
				<?endforeach?>
				</tbody>
		</table>
		<div class="row">
			<div class="col-md-12">
				<?=str_ireplace("href=", "data-loadto=\"panel_ajax\" href=", $arResult["NAV_STRING"])?>
			</div>
		</div>
	</div>
<?if ($_REQUEST["ajax_block"] == "Y") die();?>
</div>