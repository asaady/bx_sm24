<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Продажа корпоративным клиентам");
?><?$APPLICATION->IncludeComponent(
	"shopmate:cash", 
	"", 
	array(
		"CORPORATE" => "Y",
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>