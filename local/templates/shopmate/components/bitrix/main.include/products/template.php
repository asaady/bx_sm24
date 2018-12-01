<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);
use Yadadya\Shopmate\Components;
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="btn-list clearfix">
		<?if(Components\Overhead::userCanRead()):?>
			<a class="btn btn-default" href="/products/overhead/?edit=Y">Оприходовать товар</a>
		<?endif?>
		<?if(Components\Products::userCanRead()):?>
			<?/*<button class="btn btn-default">Импортировать товары</button>*/?>
			<a class="btn btn-primary pull-right" href="/products/?edit=Y">Добавить товар</a>
		<?endif?>
	<?if(Components\Inventory::userCanRead()):
		if($inventory = Components\Inventory::searchActiveInventory())://searchPrimaryInventory?>
			<a class="btn btn-default" href="/products/inventory/?edit=Y&CODE=<?=$inventory["ID"]?>">Продолжить <?=($inventory["PRIMARY"] == "Y" ? "начальную " : "")?>инвентаризацию от <?=$inventory["DATE"]?></a>
		<?else:?>
			<a class="btn btn-default" href="/products/inventory/?edit=Y"><?=(Components\Inventory::searchPrimaryInventory() ? "Новая" : "Начальная")?> инвентаризация</a>
		<?endif?>
	<?endif?>
		</div> 
	</div>
    <!-- panel -->  
</div>
<div class="row">
	<div class="col-sm-12">
		<?$APPLICATION->IncludeComponent("bitrix:menu", "tabs_menu", Array(
			"ROOT_MENU_TYPE" => "left",	// Тип меню для первого уровня
			"MAX_LEVEL" => "1",	// Уровень вложенности меню
			"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
			"MENU_CACHE_TYPE" => "N",	// Тип кеширования
			"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
			"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
			"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
			"CHILD_MENU_TYPE" => "product",	// Тип меню для остальных уровней
			"DELAY" => "N",	// Откладывать выполнение шаблона меню
			"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
			),
			false
		);?>
		<?/*<ul class="nav nav-tabs nav-primary">
			<li class="active"><a href="#tab-products" data-toggle="tab"><strong>Товары</strong></a></li>
			<li><a href="#tab-waybills" data-toggle="tab"><strong>Накладные</strong></a></li>
		</ul>*/?>
		<!-- Tab panes -->
		<div class="tab-content tab-content-primary mb30">
		    <div class="tab-pane active" id="tab-products">
		    <?if($arResult["FILE"] <> ''):?>
		    	<?include($arResult["FILE"]);?>
		    <?endif?>
		    </div>
		    <!-- tab-pane -->
		</div>
		<!-- tab-content --> 
	</div>
</div>
