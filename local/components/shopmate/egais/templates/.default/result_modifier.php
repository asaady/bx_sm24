<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
SMEGAIS::updateOptOut();?>
<div class="egais_update">
	<div class="egais_update__load text-center" style="display:none;"><img src="<?=SITE_TEMPLATE_PATH?>/static/images/loaders/loader7.gif"></div>
	<div class="egais_update__reload alert alert-info fade in nomargin" style="display:none;">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<h4>Внимание!</h4>
		<p>Пришли новые документы от ЕГАИС. </p>
		<p><a href="<?=$APPLICATION->GetCurPageParam()?>" type="button" class="btn btn-info">Обновить</a></p>
	</div>
</div>
