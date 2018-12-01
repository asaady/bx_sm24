<? define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Полное обновление базы Дэнси");

CModule::IncludeModule("yadadya.shopmate");
SMDnc::RewriteCron();
echo "Создан файл полной выгрузки";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>