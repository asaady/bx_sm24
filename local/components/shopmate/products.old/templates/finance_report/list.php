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
$this->setFrameMode(false);?>

<?$arParams["COMPONENT"] = $component;?>
<div class="row">
	<div class="col-sm-12">
		<?/*<ul class="nav nav-tabs nav-primary">
			<li class="active"><a href="#tab-products" data-toggle="tab"><strong>Товары</strong></a></li>
			<li><a href="#tab-waybills" data-toggle="tab"><strong>Накладные</strong></a></li>
		</ul>*/?>
		<!-- Tab panes -->
		<div class="tab-content tab-content-primary mb30">
		    <div class="tab-pane active" id="tab-products">
				<?$APPLICATION->IncludeComponent("shopmate:products.filter.old", "", $arParams, $component, array('HIDE_ICONS' => 'Y'));?>
				<?$APPLICATION->IncludeComponent("bitrix:iblock.element.add.list", "", $arParams, $component, array('HIDE_ICONS' => 'Y'));?>
		    </div>
		    <!-- tab-pane -->
		</div>
		<!-- tab-content --> 
	</div>
</div>