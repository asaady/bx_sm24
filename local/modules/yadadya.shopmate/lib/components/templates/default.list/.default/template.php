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
	Bitrix\Main\Localization\Loc;
$sortIndex = [];
foreach ($arResult["SORTS"] as $key => $row) 
    $sortIndex[$key]  = $row["SORT"];
array_multisort($sortIndex, SORT_ASC, $arResult["SORTS"]);
$currentSort = $component->getOrder();
?>
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = "Информация успешно добавлена.";?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = "Информация успешно изменена.";?>
<?if($_REQUEST["successMessage"] == "DELETE") $arResult["MESSAGE"] = "Информация успешно далена.";?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<form action="<?=POST_FORM_ACTION_URI?>" name="list_form" method="post" enctype="multipart/form-data">
<table class="table table-hover mb30 fixed_list">
	<thead>
		<tr>
		<?foreach ($arResult["SORTS"] as $arSort):?>
			<th><?if (!empty($arSort["URL"])):?><a href="<?=$arSort["URL"]?>"><?else:?><span><?endif?><?=(!empty($arSort["NAME"]) ? $arSort["NAME"] : $arSort["FIELD"])?> <?if (array_key_exists($arSort["FIELD"], $currentSort)):?><span class="glyphicon glyphicon-chevron-<?=($currentSort[$arSort["FIELD"]] == "DESC" ? "down" : "up")?>"></span><?endif?><?if (!empty($arSort["URL"])):?></a><?else:?></span><?endif?></th>
		<?endforeach?>
		<?if ($arResult["CAN_EDIT"] == "Y" || $arResult["CAN_DELETE"] == "Y"):?>
			<th></th>
		<?endif?>
		</tr>
	</thead>
	<tbody data-pager_block="<?=md5($templateFile)?>">
	<?foreach($arResult["ITEMS"] as $key => $arItem):?>
	<?if (!empty($arItem["ROW_TITLE"])):?>
		<tr class="row_title">
			<td colspan="<?=count($arResult["SORTS"])?>"><?=$arItem["ROW_TITLE"]?></td>
		</tr>
	<?endif?>
		<tr<?if ($arResult["CAN_EDIT"] == "Y" || $arResult["CAN_DELETE"] == "Y"):?> ondblclick="window.location = '<?=$arParams["EDIT_URL"].(strpos($arParams["EDIT_URL"], "?") === false ? "?" : "&")?>edit=Y&amp;CODE=<?=$arItem["ID"]?>';"<?endif?>>
		<?foreach ($arResult["SORTS"] as $arSort):?>
			<td><?if ($arSort["EDIT_LINK"] == "Y"):?><a href="<?=$arParams["EDIT_URL"].(strpos($arParams["EDIT_URL"], "?") === false ? "?" : "&")?>edit=Y&amp;CODE=<?=$arItem["ID"]?>"><?endif?><?=$arItem[$arSort["FIELD"]]?><?if ($arSort["EDIT_LINK"] == "Y"):?></a><?endif?><?if (!empty($arSort["INPUT"])):?><?Template::ShowInput($arSort["FIELD"]."[".$arItem["ID"]."]", $arSort["INPUT"]);?><?endif?></td>
		<?endforeach?>
		<?if ($arResult["CAN_EDIT"] == "Y" || $arResult["CAN_DELETE"] == "Y"):?>
			<td>
				<div class="form-group" style="margin-bottom: 0; float: right; display: inline-flex;">
					<?if($arResult["VIEW_EDIT"] != "N"):?><a class="btn btn-default btn-xs glyphicon <?if($arResult["CAN_EDIT"] == "Y"):?>glyphicon-pencil<?else:?>glyphicon-search<?endif?>" href="<?=$arParams["EDIT_URL"].(strpos($arParams["EDIT_URL"], "?") === false ? "?" : "&")?>edit=Y&amp;CODE=<?=$arItem["ID"]?>" title="<?if($arResult["CAN_EDIT"] == "Y"):?>редактировать<?else:?>посмотреть<?endif?>"></a><?endif?>
					<?if($arResult["VIEW_DELETE"] != "N" && $arResult["CAN_DELETE"] == "Y" && $arItem["CAN_DELETE"] != "N"):?><a class="btn btn-danger btn-xs glyphicon glyphicon-trash" href="<?=$arParams["EDIT_URL"].(strpos($arParams["EDIT_URL"], "?") === false ? "?" : "&")?>delete=Y&amp;CODE=<?=$arItem["ID"]?>" title="удалить"></a><?endif?>
				</div>
			</td>
		<?endif?>
		</tr>
	<?endforeach?>
	</tbody>
<?if (!empty($arResult["ITEM"])):?>
	<tfoot>
		<tr>
		<?foreach ($arResult["SORTS"] as $arSort):?>
			<td><?=$arResult["ITEM"][$arSort["FIELD"]]?></td>
			<?unset($arResult["ITEM"][$arSort["FIELD"]]);
		endforeach?>
			<th></td>
		</tr>
	</tfoot>
<?endif?>
</table>

<?if (!empty($arResult["ITEM"]))
	foreach ($arResult["ITEM"] as $propertyID => $propertyValue):?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?=$propertyID?></label>
		<div class="col-sm-8">
			<?=$propertyValue?>
		</div>
	</div>
	<?endforeach?>
</form>
<?if ($arParams["NAV_ON_PAGE"] !== false):?>
<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
		<?if (empty($arResult["NAV_STRING"])):?>
			<button class="btn btn-default btn-lg btn-block" data-pager="<?=md5($templateFile)?>" href="<?=$APPLICATION->GetCurPageParam($component->pageNavigation->getId()."=page-".($component->pageNavigation->getCurrentPage()+1), [$component->pageNavigation->getId()]);?>" data-loading-text="Загрузка...">Еще</button>
			<br clear="all">
		<?endif?>
	</div>
</div>
<?endif?>