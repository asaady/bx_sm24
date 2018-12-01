<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?if(!empty($arResult)):?>
	<?foreach($arResult as $keyItem => $arItem):
		$arItem["TARGET"] = substr(strstr($arItem["LINK"], "#"), 1);
		if(strpos($arItem["LINK"], "#") !== false) $arItem["LINK"] = strstr($arItem["LINK"], "#", true);?>
		<a href="<?=(!empty($arItem["TARGET"]) ? "#" : $arItem["LINK"])?>"<?if($arItem["SELECTED"]):?> class="active"<?endif?><?if(!empty($arItem["TARGET"])):?> data-toggle="modal" data-target="#<?=$arItem["TARGET"]?>"<?endif?>><?=$arItem["TEXT"]?></a>
	<?if(!empty($arItem["TARGET"])):?>
		<div class="modal fade bs-example-modal-lg-checkout modal_iframe" id="<?=$arItem["TARGET"]?>" data-src="<?=$arItem["LINK"]?>" tabindex="1" data-show="true" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
					<h4 class="modal-title"><?=$arItem["TEXT"]?></h4>
				</div>
				<div class="modal-body modal_iframe__block">
				</div>
				</div>
			</div>
		</div>
	<?endif?>
		&nbsp;&nbsp;&nbsp;
	<?endforeach?>
		<?/*<a href="/cash/">Чеки</a>&nbsp;&nbsp;&nbsp;
		<a href="#" data-toggle="modal" data-target="#Xreport">X-отчет</a> 
		<div class="modal fade bs-example-modal-lg-checkout modal_iframe" id="Xreport" data-src="<?=$APPLICATION->GetCurPageParam("device_action=Xreport", array("device_action"))?>" tabindex="1" data-show="true" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
					<h4 class="modal-title">X-отчет</h4>
				</div>
				<div class="modal-body modal_iframe__block">
				</div>
				</div>
			</div>
		</div>
		&nbsp;&nbsp;&nbsp;
		<a href="#" data-toggle="modal" data-target="#posSettlement">Сверка итогов</a> 
		<div class="modal fade bs-example-modal-lg-checkout modal_iframe" id="posSettlement" data-src="<?=$APPLICATION->GetCurPageParam("device_action=posSettlement", array("device_action"))?>" tabindex="-1" data-show="true" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
					<h4 class="modal-title">Сверка итогов</h4>
				</div>
				<div class="modal-body modal_iframe__block">
				</div>
				</div>
			</div>
		</div>
		&nbsp;&nbsp;&nbsp;
		<a href="#" data-toggle="modal" data-target="#repeatDocument">Повтор операции</a> 
		<div class="modal fade bs-example-modal-lg-checkout modal_iframe" id="repeatDocument" data-src="<?=$APPLICATION->GetCurPageParam("device_action=repeatDocument", array("device_action"))?>" tabindex="-1" data-show="true" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
					<h4 class="modal-title">Повтор операции</h4>
				</div>
				<div class="modal-body modal_iframe__block">
				</div>
				</div>
			</div>
		</div>*/?>
<?endif?>