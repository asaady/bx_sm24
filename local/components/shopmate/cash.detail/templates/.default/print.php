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
	Bitrix\Main\Localization\Loc;?>

<h1>Чек № <?=$arParams["ID"]?></h1>
<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
	<?if ($arProperty["PROPERTY_TYPE"] == "H"):?>
		<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
	<?elseif ($arProperty["PROPERTY_TYPE"] == "SUBLIST"):?>
		<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		<br>
	<?else:?>
		<table border="0">
			<tr>
				<td><?=$arProperty["TITLE"]?></td>
				<td><?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?></td>
			</tr>
		</table>
	<?endif?>
<?endforeach?>
<script type="text/javascript">
	window.print();
	window.close();
</script>