<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подразделения");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "PersonalDepartment"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>