<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список чеков");
?><?$APPLICATION->IncludeComponent(
	"shopmate:cash", 
	"", 
	array(
		"CORPORATE" => "N",
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>