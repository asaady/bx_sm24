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
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="btn-list clearfix">
			<?$APPLICATION->IncludeComponent("shopmate:finance.money.filter", "", $arParams, $component);?>
			<a class="btn btn-default modal_noajax" href="<?=$arParams["EDIT_URL"]?>?edit=Y&CODE=balance">Баланс</a>
		<?if($_REQUEST["TYPE"] != "clearing"):?>
			<a class="btn btn-danger pull-right modal_noajax" href="<?=$arParams["EDIT_URL"]?>?edit=Y">Выдача наличных</a>
			<?if($_REQUEST["OUTGO"] != "contractor"):?><a class="btn btn-primary pull-right modal_noajax" href="<?=$arParams["EDIT_URL"]?>?edit=Y&add_price=Y">Внести наличные</a><?endif?>
		<?else:?>
			<form action="<?=POST_FORM_ACTION_URI?>" class="pull-right" method="post" enctype="multipart/form-data">
				<label class="control-label" style="display: inline-flex;">Загрузить выписку <?\Yadadya\Shopmate\Components\Template::ShowInput("1C_CLEARING", array("TITLE" => "Загрузить выписку", "PROPERTY_TYPE" => "F", "CLASS" => "btn btn-primary"));?></label>
			</form>
		<?endif?>
		</div>
	</div>
	<!-- panel --> 
</div>
<div class="panel panel-body">
	<div class="mb30"> 
		<h1><?$APPLICATION->ShowTitle(false)?></h1>
	</div>
	<div class=" mb30">
	<?if($_REQUEST["OUTGO"] == "contractor"):?>
		<?$APPLICATION->IncludeComponent("shopmate:finance.money.debt", "", $arParams, $component);?>
	<?elseif($_REQUEST["OUTGO"] == "cash"):?>
		<?$APPLICATION->IncludeComponent("shopmate:finance.money.debt.cash", "", $arParams, $component);?>
	<?else:?>
		<?$APPLICATION->IncludeComponent("shopmate:finance.money.list", "", $arParams, $component);?>
	<?endif?>
	</div>
</div>