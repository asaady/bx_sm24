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

<?if($_REQUEST["CODE"] > 0):?>
<div class="row">
	<div class="col-md-7">
		<?$APPLICATION->IncludeComponent($component->getName().".detail", "", $arParams, $component);?>
	</div>
	<div class="col-md-5">
		<?global $arrCashFilter;
		$arrCashFilter["USER_ID"] = $_REQUEST["CODE"];?>
		<?$APPLICATION->IncludeComponent(
			"shopmate:cash.list", 
			"panel", 
			array(
				"FILTER_NAME" => "arrCashFilter", 
				"SELECT" => array("ID", "ACCOUNT_NUMBER", "PRICE", "SUM_NOPAID", "DATE"),
				"EDIT_URL" => "/cash/"
			),
			$component
		);?>
	</div>
</div>
<?else:?>
<?$APPLICATION->IncludeComponent($component->getName().".detail", "", $arParams, $component);?>
<?endif?>