<?php

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("DisableEventsCheck", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__)."/template.php");

$current_date = new \Bitrix\Main\Type\DateTime();
$result = array(
	"date" => $current_date->toString(),
	"items" => array(),
	"html" => ""
);

CBitrixComponent::includeComponentClass("shopmate:notify.list");
$component = new CNotifyListComponent();
$res = $component->getList(array("filter" => array(">DATE" => new \Bitrix\Main\Type\DateTime($_REQUEST["date"]))));
while ($arItem = $res->fetch())
{
	ob_start();
?>
	<strong><?=$arItem["USER_FORMATED"]?></strong> 
	<?=(!empty(GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"]))) ? GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"])) : $arItem["EVENT_TYPE"]) ?> <a href="<?=$arItem["URL"]?>"><?=(!empty(GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"]))) ? GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"])) : $arItem["ITEM_OBJECT"]) ?></a><br /><?=$arItem["DESCRIPTION"]?>
	<small class="date"><i class="fa fa-calendar"></i> <?=$arItem["DATE"]?></small> 
<?
	$result["items"][] = ob_get_contents();
	ob_end_clean();

	ob_start();
?>
	<li class="media"> 
		<div class="media-body"> 
			<strong><?=$arItem["USER_FORMATED"]?></strong> 
			<?=(!empty(GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"]))) ? GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"])) : $arItem["EVENT_TYPE"]) ?> <a href="<?=$arItem["URL"]?>"><?=(!empty(GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"]))) ? GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"])) : $arItem["ITEM_OBJECT"]) ?></a><br /><?=$arItem["DESCRIPTION"]?>
			<small class="date"><i class="fa fa-calendar"></i> <?=$arItem["DATE"]?></small> 
		</div>
	</li>
<?
	$result["html"] .= ob_get_contents();
	ob_end_clean();
}

header('Content-Type: application/json; charset=' . LANG_CHARSET);
echo json_encode($result);