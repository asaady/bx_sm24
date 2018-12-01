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

<?if($arResult["OPEN_PAYMENT"]):?>
<div class="modal fade bs-example-modal-lg-checkout modal_open" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<?/*<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>*/?>
			<h4 class="modal-title">Оплата</h4>
		</div>
		<div class="modal-body">
			<iframe src="<?=$APPLICATION->GetCurPageParam("pay_system_blank=html", array())?>" style="border: none;" height="250" width="350"></iframe>
		</div>
		</div>
	</div>
</div>
<?endif?>
<?if($arResult["OPEN_CANCEL"]):?>
<div class="modal fade bs-example-modal-lg-checkout modal_open" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<?/*<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>*/?>
			<h4 class="modal-title">Возврат денег</h4>
		</div>
		<div class="modal-body">
			<iframe src="<?=$APPLICATION->GetCurPageParam("cancel_blank=html", array())?>" style="border: none;"></iframe>
		</div>
		</div>
	</div>
</div>
<?endif?>
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = "Информация успешно добавлена.";?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = "Информация успешно изменена.";?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<div class=" mb30 new-check">
	<h1><?if($arParams["ID"] <= 0):?>Новый чек<?else:?>Чек № <?=$arParams["ID"]?><?endif?></h1>
</div>
<form class="panel panel-body save_alert form_switch" name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<?foreach ($arResult["PROPERTY_LIST"] as $propertyID => $arProperty):?> 
	<?if ($arProperty["PROPERTY_TYPE"] == "H"):?>
		<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
	<?elseif ($arProperty["PROPERTY_TYPE"] == "SUBLIST"):?>
		<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
	<?elseif ($propertyID == "USER_ID"):?>
		<div class="ml10 mb30" <?if($propertyID == 'PRICE_SUMM') echo ' style="display: none;"; '?>>
			<label class="control-label"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
			<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
		</div>
	<?else:?>
		<div class="form-group">
			<label class="control-label col-sm-4"><?=$arProperty["TITLE"]?><?if($arProperty["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></label>
			<div class="col-sm-8">
				<?Template::ShowInput($propertyID, $arProperty, $arResult["ITEM"][$propertyID]);?>
			</div>
		</div>
	<?endif?>
	<?endforeach?>
	<div style="height: 165px;"></div>
<?if($arResult["ITEM"]["CANCELED"] != "Y"):?>
<div class="floated-buttons-wrapper">
	<div class="mb30 keyboard_block" style="display: none;">
		<h3>Поменять кол-во товара в текущей строке</h3>
		<div class="btn-list">
			<button class="btn btn-info btn-bordered btn-lg kb_p">1</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">2</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">3</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">4</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">5</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">6</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">7</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">8</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">9</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">0</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">00</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">.</button>
			<button class="btn btn-info btn-bordered btn-lg kb_bs">Bksp</button>
		</div>
	</div>
	<div class="row btn-list">
		<div class="col-lg-8">
	<?if($arResult["CAN_EDIT"] == "Y"):?>
		<?if (strlen($arParams["LIST_URL"]) > 0):?>
			<?if($arParams["ID"] <= 0):?>
				<input type="hidden" name="submit" value="Y" />
				<input class="btn btn-primary btn-lg" type="submit" name="submit" value="Продать в долг" />
				<input class="btn btn-default btn-lg" type="submit" name="nodeducted" value="Сохранить черновик" />
			<?else:?>
				<?if($arResult["ITEM"]["PAYED"] != "Y" || empty($arResult["ITEM"]["PAYED"])):?>
					<input class="btn btn-primary btn-lg" type="submit" name="apply" value="Провести и оплатить" />
				<?endif?>
				<a href="<?=$APPLICATION->GetCurPageParam("print=Y")?>" target="_blank" class="btn btn-default btn-lg">Печать чека</a>
			<?endif;?>
		<?endif?>	
	<?endif?>
		</div>
		<div class="col-lg-4">
		<?if (strlen($arParams["LIST_URL"]) > 0 && $_REQUEST["iframe"] != "y"):?>
			<input class="btn btn-danger btn-lg"
				type="button"
				name="iblock_cancel"
				value="<?if($arParams["ID"] <= 0):?>Отменить операцию<?else:?>Вернуться в список без сохранения<?endif?>"
				onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
			>
		<?endif?>
		</div>
	</div>
	<?/*<div class="row btn-list">
		<div class="col-lg-10">
		<?if($arResult["ITEM"]["PAYED"] != "Y"):?>
			<?foreach($arResult["PAY_SYSTEM"] as $arPaySystem):?>
			<button class="btn btn-primary btn-lg" type="submit" name="pay_system" value="<?=$arPaySystem["ID"]?>"><?=$arPaySystem["NAME"];?></button>
			<?endforeach?>
		<?else:?>
			<input class="btn btn-success btn-lg" type="submit" name="apply" value="Сохранить изменения" />
			<?if($arParams["ID"] > 0 && $arResult["ITEM"]["CANCELED"] != "Y"):?>
			<input class="btn btn-danger"
				type="submit"
				name="order_cancel"
				value="Вернуть все позиции"
			>
			<?endif?>
		<?endif?>
		</div>
		<div class="col-lg-2">
		<?if($arParams["ID"] <= 0):?>
			<input class="btn pull-right width100p btn-danger btn-lg"
				type="button"
				name="refresh"
				value="Отменить операцию"
				onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"]."?edit=Y")?>';"
			>
		<?endif?>
		</div>
	</div>
	*/?>
	<div id="priceMirrorBlock">
		<label id='priceMirrorLabel'>Сумма</label><br/>
		<label id="pricemirror"/>0</label>
	</div>
</div>
<?endif?>
</form>
<script type="text/javascript">
$(function(){

	setInterval(function(){
		if($('.calc_summ__result').val() != '')
			$('#pricemirror').text($('.calc_summ__result').val());
	}, 500);

});
</script>