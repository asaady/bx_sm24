<?
$MESS["ADD_ITEM_BUTTON"] = "Добавить партию товаров";

$MESS["DATE_TITLE"] = "Дата";
$MESS["DATE_DESCRIPTION"] = "дата созания партии товаров";

$MESS["PRODUCT_TITLE"] = "Товары";
$MESS["PRODUCT_DESCRIPTION"] = "товары, полученные из производства";
$MESS["PRODUCT_FABRICA_PROD_ID_TITLE"] = $_REQUEST["merge"] == "Y" ? "Новые товары" : ($_REQUEST["split"] == "Y" ? "Разделяемые товары" : "Товары");
$MESS["PRODUCT_FABRICA_PROD_ID_DESCRIPTION"] = "при соединении - новые товары, при разделении - разделяемые товары";
$MESS["PRODUCT_AMOUNT_TITLE"] = "Количество";
$MESS["PRODUCT_AMOUNT_DESCRIPTION"] = "приготовленных или разделяемых";
$MESS["PRODUCT_MEASURE_TITLE"] = "Единица измерения";

$MESS["CONNECT_TITLE"] = "Товары";
$MESS["CONNECT_DESCRIPTION"] = "сырье для производства";
$MESS["CONNECT_FABRICA_PROD_ID_TITLE"] = $_REQUEST["merge"] == "Y" ? "Ингредиенты" : ($_REQUEST["split"] == "Y" ? "Товары на выходе" : "Товары");
$MESS["CONNECT_FABRICA_PROD_ID_DESCRIPTION"] = "при соединении - ингредиенты, при разделении - товары на выходе";
$MESS["CONNECT_AMOUNT_TITLE"] = "Количество";
$MESS["CONNECT_AMOUNT_DESCRIPTION"] = "ингридиентов или полученных на выходе";
$MESS["CONNECT_MEASURE_TITLE"] = "Единица измерения";

$MESS["DEFAULT_NAME"] = "Партия товаров #TYPE# от #DATE#";

$MESS["TITLE_ADD"] = "Добавление партии";
$MESS["TITLE_UPDATE"] = "Редактирование партии";
$MESS["SITE_SECTION_NAME"] = $_REQUEST["merge"] == "Y" ? "из приготовления" : ($_REQUEST["split"] == "Y" ? "из разделения" : "");
$MESS["BUTTON_APPLY"] = "Применить";
$MESS["BUTTON_SAVE"] = "Сохранить";
$MESS["MESSAGE_ADD"] = "Партия товаров успешно добавлена.";
$MESS["MESSAGE_UPDATE"] = "Информация о партии товаров успешно обновлена.";

$MESS["ERROR_FOUND_CONNECT"] = "Отсутствует #PRODUCT#.";
$MESS["ERROR_FAULT_RATIO"] = "Допустимая погрешность \"#PRODUCT#\" превышает #FAULT_RATIO#% (#AMOUNT# вместо #RULE_AMOUNT# ± #RULE_FAUT#).";
?>