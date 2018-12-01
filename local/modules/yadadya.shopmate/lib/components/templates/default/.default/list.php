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

$filterPath2Comp = CComponentEngine::MakeComponentPath($component->getName().".filter");
$filterComponentPath = getLocalPath("components".$filterPath2Comp);
$filterComponentFile = $_SERVER["DOCUMENT_ROOT"].$filterComponentPath."/class.php";
$issetFilter = file_exists($filterComponentFile) && is_file($filterComponentFile);

$issetTabs = false;
global $APPLICATION;
$curDir = $APPLICATION->GetCurDir();
$menu = new CMenu("tabs");
$menu->Init($curDir, "Y");
if (!empty($menu->arMenu))
	$issetTabs = true;
?>
<?if($arParams["ALLOW_EDIT"] != "N" && $arResult["CAN_EDIT"] != "N"):?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="btn-list clearfix">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "complex_menu", Array(
				"ROOT_MENU_TYPE" => "complex",	// Тип меню для первого уровня
					"MAX_LEVEL" => "1",	// Уровень вложенности меню
					"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
					"MENU_CACHE_TYPE" => "Y",	// Тип кеширования
					"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
					"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
					"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
					"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
					"DELAY" => "N",	// Откладывать выполнение шаблона меню
					"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
				),
				false
			);?>
			<?if ($arResult["SHOW_ADD_ITEM_BUTTON"] != "N"):?>
			<a class="btn btn-primary pull-right list__add_btn" href="<?=$arParams["EDIT_URL"]?>?edit=Y"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span> <?=(!empty($component->getComponentObject()->Loc("ADD_ITEM_BUTTON")) ? \Bitrix\Main\Localization\Loc::getMessage("ADD_ITEM_BUTTON") : "Добавить")?></a>
			<?endif?>
		</div>
	</div>
	<!-- panel --> 
</div>
<?endif?>

<?if ($issetTabs):?>
<div class="row">
	<div class="col-sm-12">
		<?$APPLICATION->IncludeComponent("bitrix:menu", "tabs_menu", Array(
			"ROOT_MENU_TYPE" => "tabs",	// Тип меню для первого уровня
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
		<!-- Tab panes -->
		<div class="tab-content tab-content-primary mb30">
		    <div class="tab-pane active">
<?else:?>
<div class="panel panel-body">
<?endif?>
	<div class="mb30"> 
	<?if ($issetFilter):?>
		<div data-toggle="buttons" style="position: absolute; right: 20px;">
			<label class="btn btn-link" data-toggle="collapse" data-target="#filter">
				<input type="checkbox"> Фильтр
			</label>
		</div>
	<?endif;?>
		<h1><?$APPLICATION->ShowTitle(false)?></h1>
	</div>

	<?if ($issetFilter):?>
	<div id="filter" class="collapse out">
		<?$APPLICATION->IncludeComponent($component->getName().".filter", "", $arParams, $component);?>
	</div>
	<?endif;
	/*elseif (!empty($arParams["COMPONENT_CLASS"]))
		$APPLICATION->IncludeComponent($component->getName(), "", array_merge($arParams, array("COMPONENT_CLASS" => $arParams["COMPONENT_CLASS"]."Filter")), $component);*/?>
	<div class=" mb30">
		<?if ($component->getName() == "shopmate:default")
			$APPLICATION->IncludeComponent($component->getName(), "", array_merge($arParams, array("COMPONENT_CLASS" => $arParams["COMPONENT_CLASS"]."List")), $component);
		else
			$APPLICATION->IncludeComponent($component->getName().".list", "", $arParams, $component);?>
<?if ($issetTabs):?>
				</div>
			</div>
		    <!-- tab-pane -->
		</div>
		<!-- tab-content --> 
	</div>
</div>
<?else:?>
</div>
<?endif?>