<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/bitrix/services/ymarket/#",
		"RULE" => "",
		"ID" => "",
		"PATH" => "/bitrix/services/ymarket/index.php",
	),
	array(
		"CONDITION" => "#^/personal/lists/#",
		"RULE" => "",
		"ID" => "bitrix:lists",
		"PATH" => "/personal/lists/index.php",
	),
	array(
		"CONDITION" => "#^/productions/#",
		"RULE" => "",
		"ID" => "bitrix:iblock.element.add",
		"PATH" => "/products/index.php",
	),
	array(
		"CONDITION" => "#^/app_news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/app_news/index.php",
	),
);

?>