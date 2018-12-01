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
$this->setFrameMode(false);?>

<div class="form-group mb20">
	<label class="control-label  col-lg-3">Сумма долга</label>
	<div class="col-md-9"><input type="text" class="form-control" value="<?=$arResult["CREDIT_SUMM"]?>" readonly></div>
	
</div>
<div class="panel panel-primary widget-messaging">
	<div class="panel-heading"> 
		<!-- pull-right -->
		<h3 class="panel-title">Транзакции</h3>
	</div> 
	<div class="panel">
		<table class="table table-hover">
			<tbody>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<tr>
					<td><?=FormatDateFromDB($arItem["TIMESTAMP_X"], "FULL")?></td>
					<td><?=$arItem["TITLE"]?></td>
					<td>оплачено: <br><?=PriceFormat($arItem["PAYED_PRICE"] > 0 ? $arItem["PAYED_PRICE"] : 0, 0, $arItem["PAYED_CURRENCY"])?></td>
					<td>долг: <br><?=PriceFormat($arItem["CREDIT_PRICE"] > 0 ? $arItem["CREDIT_PRICE"] : 0, 0, $arItem["CREDIT_CURRENCY"])?></td>
				</tr>
			<?endforeach?>
			</tbody>
		</table>
		<div class="row">
			<div class="col-md-12">
				<?=$arResult["NAV_STRING"]?>
			</div>
		</div>
	</div>
</div>