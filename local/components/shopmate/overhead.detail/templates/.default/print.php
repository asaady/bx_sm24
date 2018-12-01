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
use Yadadya\Shopmate\Components\Template,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=utf-8">
<TITLE></TITLE>
<STYLE TYPE="text/css">
body { background: #ffffff; margin: 0; font-family: Arial; font-size: 8pt; font-style: normal; }
tr.R0{ height: 16pt; }
tr.R0 td.R0C1{ font-family: Arial; font-size: 10pt; font-style: normal; vertical-align: bottom; border-bottom: #000000 1px solid; }
tr.R0 td.R0C7{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: right; }
tr.R1{ height: 12pt; }
tr.R1 td.R13C1{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; }
tr.R1 td.R13C2{ font-family: Arial; font-size: 10pt; font-style: normal; vertical-align: bottom; overflow: visible;border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R1 td.R14C1{ font-family: Arial; font-size: 8pt; font-style: normal; }
tr.R1 td.R14C2{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R1 td.R14C3{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; }
tr.R1 td.R14C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: bottom; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R1 td.R14C8{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: bottom; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R1 td.R14C9{ font-family: Arial; font-size: 10pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
tr.R1 td.R15C3{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R1 td.R15C5{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: right; }
tr.R1 td.R17C9{ font-family: Arial; font-size: 10pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; border-right: #000000 2px solid; }
tr.R1 td.R1C0{ font-family: Arial; font-size: 8pt; font-style: normal; vertical-align: top; }
tr.R1 td.R1C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: medium; }
tr.R1 td.R1C8{ text-align: center; vertical-align: medium; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R1 td.R1C9{ text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; border-right: #000000 1px solid; }
tr.R1 td.R28C5{ border-bottom: #ffffff 1px none; }
tr.R1 td.R28C6{ border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R1 td.R28C8{ border-bottom: #000000 1px solid; }
tr.R1 td.R2C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: medium; }
tr.R1 td.R2C9{ font-family: Arial; font-size: 9pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: medium; border-left: #000000 2px solid; border-top: #000000 2px solid; border-right: #000000 2px solid; }
tr.R1 td.R31C16{ border-left: #000000 1px none; border-bottom: #000000 2px solid; border-right: #000000 2px solid; }
tr.R1 td.R31C3{ border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R1 td.R31C8{ border-left: #000000 0px none; border-top: #ffffff 0px none; border-bottom: #000000 1px solid; border-right: #ffffff 0px none; }
tr.R1 td.R33C14{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; }
tr.R1 td.R33C9{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R1 td.R5C0{ text-align: right; }
tr.R1 td.R5C1{ font-family: Arial; font-size: 10pt; font-style: normal; vertical-align: bottom; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R1 td.R5C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; }
tr.R16{ height: 18pt; }
tr.R16 td.R16C2{ font-family: Arial; font-size: 9pt; font-style: normal; font-weight: bold; text-align: right; vertical-align: medium; }
tr.R16 td.R16C3{ font-family: Arial; font-size: 14pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: medium; border-left: #000000 2px solid; border-top: #000000 2px solid; border-bottom: #000000 2px solid; }
tr.R16 td.R16C4{ font-family: Arial; font-size: 14pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 2px solid; border-bottom: #000000 2px solid; border-right: #000000 2px solid; }
tr.R16 td.R16C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: bottom; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R16 td.R16C8{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: bottom; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 1px solid; }
tr.R16 td.R16C9{ font-family: Arial; font-size: 10pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
td.R18C0{ vertical-align: top; }
td.R18C15{ font-family: Arial; font-size: 8pt; font-style: italic; text-align: left; vertical-align: top; }
td.R18C16{ font-family: Arial; font-size: 8pt; font-style: italic; text-align: right; vertical-align: top; }
td.R18C3{ text-align: center; vertical-align: top; }
td.R18C7{ text-align: right; vertical-align: top; }
tr.R19{ height: 11pt; }
tr.R19 td.R19C0{ vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R19 td.R19C1{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R19 td.R19C16{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; border-right: #000000 1px solid; }
tr.R19 td.R19C2{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R19 td.R19C4{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R19 td.R19C6{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R19 td.R29C3{ border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R19 td.R29C7{ border-top: #000000 1px solid; border-bottom: #000000 1px solid; }
tr.R19 td.R29C8{ border-bottom: #000000 1px solid; }
td.R20C0{ vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
td.R20C2{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
td.R20C3{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
td.R20C4{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
td.R21C0{ vertical-align: medium; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
td.R21C1{ font-family: Arial; font-size: 7pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
td.R21C16{ font-family: Arial; font-size: 7pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; border-right: #000000 1px solid; }
tr.R22{ font-family: Arial; font-size: 11pt; font-style: normal; }
tr.R22 td.R22C0{ vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R22 td.R22C1{ text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C10{ text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C12{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C13{ text-align: center; vertical-align: top; overflow: visible;border-left: #000000 2px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C14{ text-align: right; vertical-align: top; border-left: #000000 2px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C16{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
tr.R22 td.R22C2{ vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C3{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 2px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C4{ text-align: center; vertical-align: top; border-left: #000000 2px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C5{ text-align: center; vertical-align: top; border-left: #000000 2px solid; border-top: #000000 1px solid; }
tr.R22 td.R22C6{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: center; vertical-align: medium; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R22 td.R25C1{ text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 1px solid; }
tr.R22 td.R25C2{ vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 1px solid; }
tr.R22 td.R25C4{ text-align: center; vertical-align: top; border-left: #000000 2px solid; border-top: #000000 1px solid; border-bottom: #000000 1px solid; }
tr.R22 td.R26C0{ border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; border-right: #ffffff 1px none; }
tr.R22 td.R26C1{ border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R22 td.R26C11{ text-align: center; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 2px solid; }
tr.R22 td.R26C13{ text-align: center; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R22 td.R26C14{ text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 2px solid; }
tr.R22 td.R26C15{ text-align: center; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 2px solid; }
tr.R22 td.R26C16{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 2px solid; border-right: #000000 2px solid; }
tr.R22 td.R26C2{ border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; border-right: #ffffff 1px none; }
tr.R22 td.R26C3{ border-top: #000000 2px solid; }
tr.R22 td.R26C7{ text-align: right; border-top: #000000 2px solid; }
tr.R22 td.R26C8{ text-align: right; border-left: #000000 2px solid; border-top: #000000 2px solid; }
tr.R22 td.R26C9{ text-align: right; border-left: #000000 1px solid; border-top: #000000 2px solid; }
tr.R22 td.R27C11{ text-align: center; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; }
tr.R22 td.R27C12{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; }
tr.R22 td.R27C14{ text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; }
tr.R22 td.R27C16{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: right; vertical-align: top; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; border-right: #000000 2px solid; }
tr.R22 td.R27C7{ text-align: right; }
tr.R22 td.R27C8{ text-align: right; border-left: #000000 2px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; }
tr.R22 td.R27C9{ text-align: right; border-left: #000000 1px solid; border-top: #000000 1px solid; border-bottom: #000000 2px solid; }
tr.R3{ height: 14pt; }
tr.R3 td.R3C0{ text-align: right; }
tr.R3 td.R3C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; }
tr.R3 td.R3C9{ font-family: Arial; font-size: 9pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
tr.R3 td.R6C0{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 0px none; border-top: #ffffff 1px none; }
tr.R3 td.R6C2{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R3 td.R6C9{ font-family: Arial; font-size: 10pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-bottom: #000000 1px solid; border-right: #000000 2px solid; }
tr.R35{ height: 5pt; }
tr.R35 td.R46C7{ border-right: #000000 1px solid; }
tr.R36{ height: 9pt; }
tr.R36 td.R36C10{ text-align: right; }
tr.R36 td.R36C4{ border-bottom: #000000 1px solid; }
tr.R36 td.R36C7{ border-right: #000000 1px solid; }
tr.R36 td.R38C0{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; }
tr.R36 td.R38C1{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; overflow: visible;}
tr.R36 td.R38C11{ border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
tr.R36 td.R38C2{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; }
tr.R37{ height: 10pt; }
tr.R37 td.R37C4{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; }
tr.R37 td.R37C7{ border-right: #000000 1px solid; }
td.R39C0{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; text-align: left; vertical-align: bottom; }
td.R39C1{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; text-align: left; vertical-align: bottom; border-bottom: #000000 1px solid; }
td.R39C11{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; }
td.R39C2{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; text-align: left; vertical-align: top; border-bottom: #000000 1px solid; }
td.R39C7{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; text-align: left; vertical-align: top; border-right: #000000 1px solid; }
tr.R4{ height: 8pt; }
tr.R4 td.R12C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: bottom; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R4 td.R12C8{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; vertical-align: bottom; border-left: #000000 1px solid; border-top: #000000 1px solid; }
tr.R4 td.R12C9{ font-family: Arial; font-size: 10pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
tr.R4 td.R30C15{ border-left: #000000 2px solid; border-top: #000000 2px solid; border-right: #ffffff 1px none; }
tr.R4 td.R30C16{ border-left: #000000 1px none; border-top: #000000 2px solid; border-right: #000000 2px solid; }
tr.R4 td.R30C3{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; }
tr.R4 td.R32C10{ border-top: #ffffff 1px none; }
tr.R4 td.R32C15{ border-left: #000000 2px solid; border-top: #000000 2px solid; border-bottom: #000000 2px solid; border-right: #ffffff 1px none; }
tr.R4 td.R32C16{ border-left: #000000 1px none; border-right: #000000 2px solid; }
tr.R4 td.R32C9{ font-family: Arial; font-size: 6pt; font-style: normal; border-top: #ffffff 1px none; }
tr.R4 td.R4C0{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; }
tr.R4 td.R4C2{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R4 td.R4C7{ font-family: Arial; font-size: 8pt; font-style: normal; }
tr.R4 td.R4C9{ vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
tr.R4 td.R8C0{ text-align: right; }
tr.R4 td.R8C1{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: right; }
tr.R4 td.R8C2{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R4 td.R8C3{ font-family: Arial; font-size: 8pt; font-style: normal; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #ffffff 1px none; }
tr.R4 td.R8C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; }
tr.R4 td.R8C9{ font-family: Arial; font-size: 9pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-top: #000000 1px solid; border-right: #000000 2px solid; }
tr.R40{ height: 11pt; }
tr.R40 td.R40C2{ border-bottom: #000000 1px solid; }
tr.R40 td.R40C4{ border-bottom: #000000 1px solid; }
tr.R40 td.R40C6{ overflow: hidden;border-bottom: #000000 1px solid; }
tr.R40 td.R40C7{ border-right: #000000 1px solid; }
tr.R40 td.R47C1{ text-align: right; }
tr.R40 td.R47C10{ text-align: center; }
tr.R41{ height: 9pt; }
tr.R41 td.R41C10{ border-left: #000000 0px none; border-top: #ffffff 1px none; }
tr.R41 td.R41C11{ border-top: #ffffff 1px none; }
tr.R41 td.R41C2{ font-family: Arial; font-size: 6pt; font-style: normal; text-align: center; vertical-align: top; border-top: #ffffff 1px none; }
tr.R41 td.R41C7{ border-right: #000000 1px solid; }
td.R42C0{ font-family: Arial; font-size: 8pt; font-style: normal; font-weight: bold; }
td.R42C4{ border-bottom: #000000 1px solid; }
td.R42C6{ overflow: hidden;border-bottom: #000000 1px solid; }
td.R42C7{ border-right: #000000 1px solid; }
td.R44C2{ font-family: Arial; font-size: 8pt; font-style: normal; text-align: left; vertical-align: top; overflow: hidden;border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
td.R7C0{ text-align: right; }
td.R7C1{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; }
td.R7C2{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: left; vertical-align: bottom; border-left: #000000 1px none; border-top: #ffffff 1px none; border-bottom: #000000 1px solid; }
td.R7C7{ font-family: Arial; font-size: 9pt; font-style: normal; text-align: right; }
td.R7C9{ font-family: Arial; font-size: 9pt; font-style: normal; font-weight: bold; text-align: center; vertical-align: bottom; overflow: hidden;border-left: #000000 2px solid; border-right: #000000 2px solid; }
td.R9C2{ font-family: Arial; font-size: 10pt; font-style: normal; text-align: left; border-bottom: #000000 1px solid; }
table {table-layout: fixed; padding: 0 0 0 1px; vertical-align:bottom; width: 100%; font-family: Arial; font-size: 8pt; font-style: normal; }
td { padding-left: 3px; }
</STYLE>
</HEAD>
<BODY>
<TABLE CELLSPACING=0>
<COL WIDTH="7">
<COL WIDTH="170">
<COL WIDTH="250">
<COL WIDTH="153">
<COL WIDTH="157">
<COL WIDTH="104">
<COL WIDTH="63">
<COL WIDTH="71">
<COL WIDTH="75">
<COL WIDTH="123">
<TR CLASS=R0>
<TD>&nbsp;</TD>
<TD CLASS=R0C1 COLSPAN=5 ROWSPAN=4></TD>
<TD CLASS=R0C7 COLSPAN=4><SPAN STYLE="white-space:nowrap">Унифицированная&nbsp;форма&nbsp;№&nbsp;ТОРГ-12<BR>Утверждена&nbsp;постановлением&nbsp;Госкомстата&nbsp;России&nbsp;от&nbsp;25.12.98&nbsp;№&nbsp;132</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD CLASS=R1C0>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R1C7>&nbsp;</TD>
<TD CLASS=R1C8>&nbsp;</TD>
<TD CLASS=R1C9><SPAN STYLE="white-space:nowrap">Коды</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD CLASS=R1C0>&nbsp;</TD>
<TD CLASS=R2C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">Форма&nbsp;по&nbsp;ОКУД&nbsp;</SPAN></TD>
<TD CLASS=R2C9><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R3>
<TD CLASS=R3C0>&nbsp;</TD>
<TD CLASS=R3C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">по&nbsp;ОКПО</SPAN></TD>
<TD CLASS=R3C9><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R4>
<TD CLASS=R4C0>&nbsp;</TD>
<TD CLASS=R4C0>&nbsp;</TD>
<TD CLASS=R4C2 COLSPAN=4><SPAN STYLE="white-space:nowrap">организация-грузоотправитель,&nbsp;адрес,&nbsp;телефон,&nbsp;факс,&nbsp;банковские&nbsp;реквизиты</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R4C7>&nbsp;</TD>
<TD CLASS=R4C7>&nbsp;</TD>
<TD CLASS=R4C9 ROWSPAN=2>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD CLASS=R5C0>&nbsp;</TD>
<TD CLASS=R5C1 COLSPAN=5><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R5C7>&nbsp;</TD>
<TD CLASS=R5C7>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R3>
<TD CLASS=R6C0>&nbsp;</TD>
<TD CLASS=R6C0>&nbsp;</TD>
<TD CLASS=R6C2 COLSPAN=4><SPAN STYLE="white-space:nowrap">структурное&nbsp;подразделение</SPAN></TD>
<TD CLASS=R3C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">Вид&nbsp;деятельности&nbsp;по&nbsp;ОКДП</SPAN></TD>
<TD CLASS=R6C9>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R7C0>&nbsp;</TD>
<TD CLASS=R7C1><BR>Грузополучатель</TD>
<TD CLASS=R7C2 COLSPAN=4></TD>
<TD CLASS=R7C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">по&nbsp;ОКПО</SPAN></TD>
<TD CLASS=R7C9><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R4>
<TD CLASS=R8C0>&nbsp;</TD>
<TD CLASS=R8C1>&nbsp;</TD>
<TD CLASS=R8C2 COLSPAN=4><SPAN STYLE="white-space:nowrap">организация,&nbsp;адрес,&nbsp;телефон,&nbsp;факс,&nbsp;банковские&nbsp;реквизиты</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R8C7>&nbsp;</TD>
<TD CLASS=R8C7>&nbsp;</TD>
<TD CLASS=R8C9 ROWSPAN=2><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R7C7 COLSPAN=2><SPAN STYLE="white-space:nowrap"><BR>Поставщик</SPAN></TD>
<TD CLASS=R9C2 COLSPAN=4></TD>
<TD CLASS=R7C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">по&nbsp;ОКПО</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R4>
<TD CLASS=R8C0>&nbsp;</TD>
<TD CLASS=R8C1>&nbsp;</TD>
<TD CLASS=R8C2 COLSPAN=4><SPAN STYLE="white-space:nowrap">организация,&nbsp;адрес,&nbsp;телефон,&nbsp;факс,&nbsp;банковские&nbsp;реквизиты</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R8C7>&nbsp;</TD>
<TD CLASS=R8C7>&nbsp;</TD>
<TD CLASS=R8C9 ROWSPAN=2><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R7C7 COLSPAN=2><SPAN STYLE="white-space:nowrap"><BR>Плательщик</SPAN></TD>
<TD CLASS=R7C2 COLSPAN=4></TD>
<TD CLASS=R7C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">по&nbsp;ОКПО</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R4>
<TD CLASS=R8C0>&nbsp;</TD>
<TD CLASS=R8C1>&nbsp;</TD>
<TD CLASS=R8C2 COLSPAN=4><SPAN STYLE="white-space:nowrap">организация,&nbsp;адрес,&nbsp;телефон,&nbsp;факс,&nbsp;банковские&nbsp;реквизиты</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R12C7 ROWSPAN=2>&nbsp;</TD>
<TD CLASS=R12C8 ROWSPAN=2><SPAN STYLE="white-space:nowrap">номер</SPAN></TD>
<TD CLASS=R12C9 ROWSPAN=2><SPAN STYLE="white-space:nowrap">РОП</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD CLASS=R13C1 COLSPAN=2><SPAN STYLE="white-space:nowrap">Основание</SPAN></TD>
<TD CLASS=R13C2 COLSPAN=4><SPAN STYLE="white-space:nowrap">Договор&nbsp;п</SPAN></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD>&nbsp;</TD>
<TD CLASS=R14C1 COLSPAN=2 ROWSPAN=4 ALIGN=LEFT VALIGN=TOP><IMG SRC = "Накладная 1_files\image000.png" ALT = ""></TD>
<TD CLASS=R14C3 COLSPAN=3>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R14C7>&nbsp;</TD>
<TD CLASS=R14C8><SPAN STYLE="white-space:nowrap">дата</SPAN></TD>
<TD CLASS=R14C9>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD>&nbsp;</TD>
<TD CLASS=R15C3><SPAN STYLE="white-space:nowrap">Номер&nbsp;документа</SPAN></TD>
<TD CLASS=R1C9><SPAN STYLE="white-space:nowrap">Дата&nbsp;составления</SPAN></TD>
<TD CLASS=R15C5 COLSPAN=3><SPAN STYLE="white-space:nowrap">Товарная&nbsp;накладная</SPAN></TD>
<TD CLASS=R14C8><SPAN STYLE="white-space:nowrap">номер</SPAN></TD>
<TD CLASS=R14C9>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R16>
<TD>&nbsp;</TD>
<TD CLASS=R16C3><SPAN STYLE="white-space:nowrap"><?=$arResult["ITEM"]["NUMBER_DOCUMENT"]?></SPAN></TD>
<TD CLASS=R16C4><SPAN STYLE="white-space:nowrap"><?=substr($arResult["ITEM"]["DATE_DOCUMENT"]->toString(), 0, 10)?></SPAN></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R16C7>&nbsp;</TD>
<TD CLASS=R16C8><SPAN STYLE="white-space:nowrap">дата</SPAN></TD>
<TD CLASS=R16C9>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD CLASS=R13C1 COLSPAN=9><SPAN STYLE="white-space:nowrap">Вид&nbsp;операции</SPAN></TD>
<TD CLASS=R17C9>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
</TABLE>
<TABLE CELLSPACING=0>
<COL WIDTH="7">
<COL WIDTH="35">
<COL WIDTH="229">
<COL WIDTH="81">
<COL WIDTH="42">
<COL WIDTH="36">
<COL WIDTH="42">
<COL WIDTH="39">
<COL WIDTH="41">
<COL WIDTH="92">
<COL WIDTH="96">
<COL WIDTH="57">
<COL WIDTH="91">
<COL WIDTH="44">
<COL WIDTH="88">
<COL WIDTH="65">
<COL WIDTH="91">
<TR>
<TD CLASS=R18C16 COLSPAN=17><SPAN STYLE="white-space:nowrap">Страница&nbsp;1</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R19>
<TD CLASS=R19C0>&nbsp;</TD>
<TD CLASS=R19C1 ROWSPAN=2>№ по по-<BR>рядку </TD>
<TD CLASS=R19C2 COLSPAN=2><SPAN STYLE="white-space:nowrap">Товар</SPAN></TD>
<TD CLASS=R19C4 COLSPAN=2><SPAN STYLE="white-space:nowrap">Ед.&nbsp;измерения</SPAN></TD>
<TD CLASS=R19C6 ROWSPAN=2>Вид упаковки</TD>
<TD CLASS=R19C4 COLSPAN=2><SPAN STYLE="white-space:nowrap">Количество</SPAN></TD>
<TD CLASS=R19C1 ROWSPAN=2>Масса брутто</TD>
<TD CLASS=R19C2 ROWSPAN=2><SPAN STYLE="white-space:nowrap">Количе-<BR>ство&nbsp;<BR>(масса&nbsp;<BR>нетто)</SPAN></TD>
<TD CLASS=R19C1 ROWSPAN=2>Цена<BR> (без НДС),<BR>руб. коп.</TD>
<TD CLASS=R19C2 ROWSPAN=2><SPAN STYLE="white-space:nowrap">Сумма&nbsp;без<BR>учета&nbsp;НДС,<BR>руб.&nbsp;коп.</SPAN></TD>
<TD CLASS=R19C2 COLSPAN=2><SPAN STYLE="white-space:nowrap">НДС</SPAN></TD>
<TD CLASS=R19C1 ROWSPAN=2>Цена <BR>(с налогами),<BR>руб. коп.</TD>
<TD CLASS=R19C16 ROWSPAN=2><SPAN STYLE="white-space:nowrap">Сумма&nbsp;с<BR>учетом&nbsp;<BR>НДС,&nbsp;<BR>руб.&nbsp;коп.</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R20C0>&nbsp;</TD>
<TD CLASS=R20C2>наименование, характеристика, сорт, артикул товара</TD>
<TD CLASS=R20C3><SPAN STYLE="white-space:nowrap">код</SPAN></TD>
<TD CLASS=R20C4>наиме- нова- ние</TD>
<TD CLASS=R20C4>код по ОКЕИ</TD>
<TD CLASS=R20C4>в одном месте</TD>
<TD CLASS=R20C4>мест,<BR>штук</TD>
<TD CLASS=R20C2>ставка, %</TD>
<TD CLASS=R20C2>сумма, <BR>руб. коп.</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R21C0>&nbsp;</TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">1</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">2</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">3</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">4</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">5</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">6</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">7</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">8</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">9</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">10</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">11</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">12</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">13</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">14</SPAN></TD>
<TD CLASS=R21C1><SPAN STYLE="white-space:nowrap">15</SPAN></TD>
<TD CLASS=R21C16><SPAN STYLE="white-space:nowrap">16</SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<?$numb = 0;
$cols_sum = [];
foreach ($arResult["ITEM"]["ELEMENT"] as $product):
	$numb++;
	$cols = [
		10 => $product["AMOUNT"],
		11 => ($product["PURCHASING_SUMM"] - $product["NDS_VALUE"]) / $product["AMOUNT"],
		12 => $product["PURCHASING_SUMM"] - $product["NDS_VALUE"],
		14 => $product["NDS_VALUE"],
		15 => $product["PURCHASING_PRICE"],
		16 => $product["PURCHASING_SUMM"],
	];?>
<TR CLASS=R22>
<TD CLASS=R22C0>&nbsp;</TD>
<TD CLASS=R22C1><SPAN STYLE="white-space:nowrap"><?=$numb?></SPAN></TD>
<TD CLASS=R22C2><?Template::PrintInput("ELEMENT_ID", $arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["ELEMENT_ID"], $product["ELEMENT_ID"]);?></TD>
<TD CLASS=R22C3>&nbsp;</TD>
<TD CLASS=R22C4><SPAN STYLE="white-space:nowrap"><?Template::PrintInput("MEASURE", $arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["MEASURE"], $product["MEASURE"]);?></SPAN></TD>
<TD CLASS=R22C5>&nbsp;</TD>
<TD CLASS=R22C6>&nbsp;</TD>
<TD CLASS=R22C1>&nbsp;</TD>
<TD CLASS=R22C1>&nbsp;</TD>
<TD CLASS=R22C1>&nbsp;</TD>
<TD CLASS=R22C10><SPAN STYLE="white-space:nowrap"><?=number_format($cols[10], 3, ",", " ")?></SPAN></TD>
<TD CLASS=R22C10><SPAN STYLE="white-space:nowrap"><?=number_format($cols[11], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R22C12><SPAN STYLE="white-space:nowrap"><?=number_format($cols[12], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R22C13><SPAN STYLE="white-space:nowrap"><?Template::PrintInput("PURCHASING_NDS", $arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["PURCHASING_NDS"], $product["PURCHASING_NDS"]);?></SPAN></TD>
<TD CLASS=R22C14><SPAN STYLE="white-space:nowrap"><?=number_format($cols[14], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R22C10><SPAN STYLE="white-space:nowrap"><?=number_format($cols[15], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R22C16><SPAN STYLE="white-space:nowrap"><?=number_format($cols[16], 2, ",", " ")?></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
	<?foreach ($cols as $key => $col) 
		$cols_sum[$key] += $col;
endforeach?>
<TR CLASS=R22>
<TD CLASS=R26C0>&nbsp;</TD>
<TD CLASS=R26C1>&nbsp;</TD>
<TD CLASS=R26C2>&nbsp;</TD>
<TD CLASS=R26C3>&nbsp;</TD>
<TD CLASS=R26C0>&nbsp;</TD>
<TD CLASS=R26C7 COLSPAN=3><SPAN STYLE="white-space:nowrap">Итого&nbsp;</SPAN></TD>
<TD CLASS=R26C8><SPAN STYLE="white-space:nowrap">&nbsp;</SPAN></TD>
<TD CLASS=R26C9><SPAN STYLE="white-space:nowrap">&nbsp;</SPAN></TD>
<TD CLASS=R26C9><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[10], 3, ",", " ")?></SPAN></TD>
<TD CLASS=R26C11><SPAN STYLE="white-space:nowrap">Х</SPAN></TD>
<TD CLASS=R22C12><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[12], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R26C13><SPAN STYLE="white-space:nowrap">Х</SPAN></TD>
<TD CLASS=R26C14><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[14], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R26C15><SPAN STYLE="white-space:nowrap">Х</SPAN></TD>
<TD CLASS=R26C16><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[16], 2, ",", " ")?></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R22>
<TD CLASS=R26C0>&nbsp;</TD>
<TD CLASS=R27C7 COLSPAN=7><SPAN STYLE="white-space:nowrap">Всего&nbsp;по&nbsp;накладной&nbsp;</SPAN></TD>
<TD CLASS=R27C8><SPAN STYLE="white-space:nowrap">&nbsp;</SPAN></TD>
<TD CLASS=R27C9><SPAN STYLE="white-space:nowrap">&nbsp;</SPAN></TD>
<TD CLASS=R27C9><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[10], 3, ",", " ")?></SPAN></TD>
<TD CLASS=R27C11><SPAN STYLE="white-space:nowrap">Х</SPAN></TD>
<TD CLASS=R27C12><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[12], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R27C11><SPAN STYLE="white-space:nowrap">Х</SPAN></TD>
<TD CLASS=R27C14><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[14], 2, ",", " ")?></SPAN></TD>
<TD CLASS=R27C11><SPAN STYLE="white-space:nowrap">Х</SPAN></TD>
<TD CLASS=R27C16><SPAN STYLE="white-space:nowrap"><?=number_format($cols_sum[16], 2, ",", " ")?></SPAN></TD>
<TD>&nbsp;</TD>
</TR>
</TABLE>
<TABLE CELLSPACING=0>
<COL WIDTH="7">
<COL WIDTH="125">
<COL WIDTH="91">
<COL WIDTH="30">
<COL WIDTH="103">
<COL WIDTH="13">
<COL WIDTH="138">
<COL WIDTH="13">
<COL WIDTH="27">
<COL WIDTH="56">
<COL WIDTH="42">
<COL WIDTH="86">
<COL WIDTH="11">
<COL WIDTH="100">
<COL WIDTH="12">
<COL WIDTH="223">
<COL WIDTH="104">
<TR CLASS=R1>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD COLSPAN=3><SPAN STYLE="white-space:nowrap">Товарная&nbsp;накладная&nbsp;имеет&nbsp;приложение&nbsp;на</SPAN></TD>
<TD CLASS=R28C5>&nbsp;</TD>
<TD CLASS=R28C6>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R28C8>&nbsp;</TD>
<TD CLASS=R28C8>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R19>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD><SPAN STYLE="white-space:nowrap">и&nbsp;содержит</SPAN></TD>
<TD CLASS=R29C3 COLSPAN=4><SPAN STYLE="white-space:nowrap">&nbsp;</SPAN></TD>
<TD CLASS=R29C7>&nbsp;</TD>
<TD CLASS=R29C8>&nbsp;</TD>
<TD CLASS=R29C8>&nbsp;</TD>
<TD CLASS=R29C8>&nbsp;</TD>
<TD COLSPAN=7><SPAN STYLE="white-space:nowrap">порядковых&nbsp;номеров&nbsp;записей</SPAN></TD>
</TR>
<TR CLASS=R4>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R30C3 COLSPAN=8><SPAN STYLE="white-space:nowrap">прописью</SPAN></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R30C15 ROWSPAN=2>&nbsp;</TD>
<TD CLASS=R30C16>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R31C3 COLSPAN=2 ROWSPAN=3><SPAN STYLE="white-space:nowrap">&nbsp;</SPAN></TD>
<TD>&nbsp;</TD>
<TD COLSPAN=2><SPAN STYLE="white-space:nowrap">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Масса&nbsp;груза&nbsp;(нетто)</SPAN></TD>
<TD CLASS=R31C8 COLSPAN=6><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R31C16>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R4>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R30C3 COLSPAN=6><SPAN STYLE="white-space:nowrap">прописью</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R32C15 ROWSPAN=2>&nbsp;</TD>
<TD CLASS=R32C16>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R1>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD><SPAN STYLE="white-space:nowrap">Всего&nbsp;мест</SPAN></TD>
<TD>&nbsp;</TD>
<TD COLSPAN=2><SPAN STYLE="white-space:nowrap">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Масса&nbsp;груза&nbsp;(брутто)</SPAN></TD>
<TD CLASS=R31C3 COLSPAN=6><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD CLASS=R33C14>&nbsp;</TD>
<TD CLASS=R31C16>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R4>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R30C3 COLSPAN=2><SPAN STYLE="white-space:nowrap">прописью</SPAN></TD>
<TD CLASS=R30C3>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R30C3 COLSPAN=6><SPAN STYLE="white-space:nowrap">прописью</SPAN></TD>
<TD CLASS=R30C3>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R35>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R36>
<TD>&nbsp;</TD>
<TD COLSPAN=3><SPAN STYLE="white-space:nowrap">Приложение&nbsp;(паспорта,&nbsp;сертификаты&nbsp;и&nbsp;т.п.)&nbsp;на&nbsp;</SPAN></TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD><SPAN STYLE="white-space:nowrap">листах</SPAN></TD>
<TD CLASS=R36C7>&nbsp;</TD>
<TD CLASS=R36C10 COLSPAN=3><SPAN STYLE="white-space:nowrap">По&nbsp;доверенности&nbsp;№</SPAN></TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD><SPAN STYLE="white-space:nowrap">от&nbsp;</SPAN></TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R37>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R37C4 COLSPAN=2><SPAN STYLE="white-space:nowrap">прописью</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R37C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R36>
<TD CLASS=R38C0>&nbsp;</TD>
<TD CLASS=R38C1 COLSPAN=6><SPAN STYLE="white-space:nowrap">Всего&nbsp;отпущено&nbsp;&nbsp;на&nbsp;сумму</SPAN></TD>
<TD CLASS=R36C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD COLSPAN=2><SPAN STYLE="white-space:nowrap">выданной</SPAN></TD>
<TD CLASS=R38C11>&nbsp;</TD>
<TD CLASS=R38C11>&nbsp;</TD>
<TD CLASS=R38C11>&nbsp;</TD>
<TD CLASS=R38C11>&nbsp;</TD>
<TD CLASS=R38C11>&nbsp;</TD>
<TD CLASS=R36C4>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R39C0>&nbsp;</TD>
<TD CLASS=R39C1 COLSPAN=7></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11 COLSPAN=5><SPAN STYLE="white-space:nowrap">кем,&nbsp;кому&nbsp;(организация,&nbsp;должность,&nbsp;фамилия,&nbsp;и.&nbsp;о.)</SPAN></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R40>
<TD>&nbsp;</TD>
<TD><SPAN STYLE="white-space:nowrap">Отпуск&nbsp;разрешил</SPAN></TD>
<TD CLASS=R40C2>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R40C6><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD CLASS=R40C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD CLASS=R40C4>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R41>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R41C2><SPAN STYLE="white-space:nowrap">должность</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R41C2><SPAN STYLE="white-space:nowrap">подпись</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R41C2><SPAN STYLE="white-space:nowrap">расшифровка&nbsp;подписи</SPAN></TD>
<TD CLASS=R41C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R41C10>&nbsp;</TD>
<TD CLASS=R41C11>&nbsp;</TD>
<TD CLASS=R41C11>&nbsp;</TD>
<TD CLASS=R41C11>&nbsp;</TD>
<TD CLASS=R41C11>&nbsp;</TD>
<TD CLASS=R41C11>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD CLASS=R42C0>&nbsp;</TD>
<TD CLASS=R42C0 COLSPAN=3><SPAN STYLE="white-space:nowrap">Главный&nbsp;(старший)&nbsp;бухгалтер</SPAN></TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C6>&nbsp;</TD>
<TD CLASS=R42C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD COLSPAN=2><SPAN STYLE="white-space:nowrap">Груз&nbsp;принял</SPAN></TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">подпись</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">расшифровка&nbsp;подписи</SPAN></TD>
<TD CLASS=R42C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">должность</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">подпись</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">расшифровка&nbsp;подписи</SPAN></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD>&nbsp;</TD>
<TD><SPAN STYLE="white-space:nowrap">Отпуск&nbsp;груза&nbsp;произвел</SPAN></TD>
<TD CLASS=R44C2>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C6><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD CLASS=R42C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD COLSPAN=2><SPAN STYLE="white-space:nowrap">Груз&nbsp;получил&nbsp;</SPAN></TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD CLASS=R42C4>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">должность</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">подпись</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">расшифровка&nbsp;подписи</SPAN></TD>
<TD CLASS=R42C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R18C0 COLSPAN=2><SPAN STYLE="white-space:nowrap">грузополучатель</SPAN></TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">должность</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">подпись</SPAN></TD>
<TD>&nbsp;</TD>
<TD CLASS=R39C11><SPAN STYLE="white-space:nowrap">расшифровка&nbsp;подписи</SPAN></TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R35>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R46C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
</TR>
<TR CLASS=R40>
<TD CLASS=R47C1 COLSPAN=2><SPAN STYLE="white-space:nowrap">М.П.</SPAN></TD>
<TD CLASS=R47C1 COLSPAN=2><SPAN STYLE="white-space:nowrap">"&nbsp;&nbsp;"</SPAN></TD>
<TD CLASS=R40C4><SPAN STYLE="white-space:nowrap"></SPAN></TD>
<TD COLSPAN=2><SPAN STYLE="white-space:nowrap">20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;года</SPAN></TD>
<TD CLASS=R40C7>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD>&nbsp;</TD>
<TD CLASS=R47C10><SPAN STYLE="white-space:nowrap">М.П.</SPAN></TD>
<TD>&nbsp;</TD>
<TD COLSPAN=6><SPAN STYLE="white-space:nowrap">"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"&nbsp;_____________&nbsp;20&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;года</SPAN></TD>
</TR>
</TABLE>
<script type="text/javascript">
	window.print();
</script>
</BODY>
</HTML>
