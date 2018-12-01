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
<?/*<h4><?$APPLICATION->ShowTitle(false)?> | Редактирование</h4>*/?>
<?if($_REQUEST["CODE"] > 0):?>
<div class="row">
	<div class="col-md-7">
		<?$APPLICATION->IncludeComponent($component->getName().".detail", "", $arParams, $component);?>
	</div>
	<div class="col-md-5">
		<?global $arrFilter;
		$docsID = array();
		$rsDoc = CCatalogDocs::getList(array("ID" => "ASC"), array("SITE_ID" => SITE_ID, "DOC_TYPE" => "A", "CONTRACTOR_ID" => $_REQUEST["CODE"]), false, false, array("ID"));
		while($arDoc = $rsDoc->Fetch())
			$arrFilter["ITEM_ID"][] = $arDoc["ID"];
		if(!empty($arrFilter["ITEM_ID"])):?>
		<?$APPLICATION->IncludeComponent(
			"shopmate:finance.transactions", 
			"", 
			array(
				"TRANSACTION" => "overhead",
				"ITEM_ID" => "",
				"SORT_FIELD" => "ID",
				"SORT_ORDER" => "asc",
				"FILTER_NAME" => "arrFilter",
				"NAV_ON_PAGE" => "10",
				"GROUPS" => array(
				),
				"ALLOW_EDIT" => "Y",
				"ALLOW_DELETE" => "Y",
				"SEF_MODE" => "N",
				"SEF_FOLDER" => "/"
			),
			$component
		);?>
		<?endif?>
	</div>
</div>
<?else:?>
<?$APPLICATION->IncludeComponent($component->getName().".detail", "", $arParams, $component);?>
<?endif?>