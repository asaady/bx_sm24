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
<div class="panel panel-body">
<?if($USER->IsAuthorized()):?>
	<div class="row mb30">
		<div class="col-lg-8">
			<h3>Кассир: <?=$USER->GetFullName()?></h3>
		</div>
		<div class="col-lg-4 open-close-shift">
		<?$ECRModeStatus = $APPLICATION->get_cookie("ECR_MODE_STATUS");
		if($ECRModeStatus == 2 || $ECRModeStatus == 3):?>
			<a class="btn btn-warning pull-right" href="#" data-toggle="modal" data-target="#Zreport">Закрытие смены</a>
			<div class="modal fade bs-example-modal-lg-checkout modal_iframe" id="Zreport" data-src="<?=$APPLICATION->GetCurPageParam("device_action=Zreport", array("device_action"))?>" tabindex="-1" data-show="true" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
						<h4 class="modal-title">Закрытие смены</h4>
					</div>
					<div class="modal-body modal_iframe__block">
					</div>
					</div>
				</div>
			</div>
		<?else:?>
			<a class="btn btn-warning pull-right" href="#" data-toggle="modal" data-target="#Openshift">Открытие смены</a>
			<div class="modal fade bs-example-modal-lg-checkout modal_iframe" id="Openshift" data-src="<?=$APPLICATION->GetCurPageParam("device_action=Openshift", array("device_action"))?>" tabindex="-1" data-show="true" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
						<h4 class="modal-title">Открытие смены</h4>
					</div>
					<div class="modal-body modal_iframe__block">
					</div>
					</div>
				</div>
			</div>
		<?endif?>
		</div>

	</div>
<?else:
	$APPLICATION->AuthForm("");
endif?>
	<?$APPLICATION->IncludeComponent("shopmate:oldcash.detail", "", $arParams, $component);?>
</div>