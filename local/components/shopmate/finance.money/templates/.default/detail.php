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

if($_REQUEST["CODE"] == "balance")
	$APPLICATION->IncludeComponent("shopmate:finance.money.balance", "", $arParams, $component);	
else
	$APPLICATION->IncludeComponent("shopmate:finance.money.detail", "", $arParams, $component);
?>