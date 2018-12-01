<?//if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==thue) die();?>
<?//print_p($arResult);?>
<h1>Товарная накладная № <?=$arResult["ELEMENT"]["NUMBER_DOCUMENT"]?> от <?=$arResult["ELEMENT"]["DATE_DOCUMENT"]?></h1>
<h3>Поставщик: <?=$arResult["PROPERTY_LIST_FULL"]["CONTRACTOR_ID"]["ENUM"][$arResult["ELEMENT"]["CONTRACTOR_ID"][0]["VALUE"]]["VALUE"]?></h1>

<table border="1">
	<thead>
		<tr>
			<th>№</th>
			<th>Товар</th>
			<th>Единица измерения</th>
			<th>Вид упаковки</th>
			<th>Количество по накладной</th>
			<?/*<th>Фактическое количество</th>*/?>
			<th>Цена</th>
			<th>Сумма без учета НДС</th>
			<th>НДС, %</th>
			<th>Сумма с учетом НДС</th>
		</tr>
	</thead>
	<tbody>
	<?foreach($arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["ENUM"] as $n => $arElement):?>
		<tr>
			<td><?=($n+1)?></td>
			<td><?=$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["arResult"]["PROPERTY_LIST_FULL"]["ELEMENT_ELEMENT_ID"]["ENUM"][$arElement["ELEMENT_ELEMENT_ID"]]["VALUE"]?></td>
			<td><?=$arResult["PROPERTY_LIST_FULL"]["ELEMENT"]["arResult"]["PROPERTY_LIST_FULL"]["ELEMENT_MEASURE"]["ENUM"][$arElement["ELEMENT_MEASURE"]]["VALUE"]?></td>
			<td><?=$arElement["ELEMENT_PACK"]?></td>
			<td><?=$arElement["ELEMENT_DOC_AMOUNT"]?></td>
			<?/*<td><?=$arElement["ELEMENT_AMOUNT"]?></td>*/?>
			<td><?=$arElement["ELEMENT_PURCHASING_PRICE"]?></td>
			<td><?=$arElement["ELEMENT_PURCHASING_SUMM_WITHOUT_NDS"]?></td>
			<td><?=floatval($arElement["ELEMENT_PURCHASING_NDS"])?></td>
			<td><?=$arElement["ELEMENT_PURCHASING_SUMM"]?></td>
		</tr>
	<?endforeach?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4">Итого</td>
			<td><?=$arResult["ELEMENT"]["TOTAL_DOC_AMOUNT"]?></td>
			<?/*<td><?=$arResult["ELEMENT"]["TOTAL_DOC_AMOUNT"]?></td>*/?>
			<td>&nbsp;</td>
			<td><?=$arResult["ELEMENT"]["TOTAL_PURCHASING_SUMM_WITHOUT_NDS"]?></td>
			<td>&nbsp;</td>
			<td><?=$arResult["ELEMENT"]["TOTAL_SUMM"]?></td>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
	window.print();
</script>