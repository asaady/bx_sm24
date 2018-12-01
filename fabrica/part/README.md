<!--WIKI_URL=https://yadadya-dev.atlassian.net/wiki/spaces/STORMATEWIKI/pages/66289671-->
<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=NAME]--><b>NAME</b> [NAME]
* <!--[LIST_CODE=DATE]--><b>DATE</b> [DATE]
* <!--[LIST_CODE=COMMENT]--><b>COMMENT</b> [COMMENT]

## Поля добавления/редактирования 
* <!--[ITEM_CODE=PRODUCT]--><b>Товары</b> [PRODUCT] (товары, полученные из производства): тип - таблица
	* <!--[ITEM_CODE=PRODUCT_FABRICA_PROD_ID]--><b>Товары</b> [FABRICA_PROD_ID] (при соединении - новые товары, при разделении - разделяемые товары)
	* <!--[ITEM_CODE=PRODUCT_AMOUNT]--><b>Количество</b> [AMOUNT] (приготовленных или разделяемых)
	* <!--[ITEM_CODE=PRODUCT_MEASURE]--><b>Единица измерения</b> [MEASURE]: тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт); только для чтения
* <!--[ITEM_CODE=CONNECT]--><b>Товары</b> [CONNECT] (сырье для производства): тип - таблица
	* <!--[ITEM_CODE=CONNECT_AMOUNT]--><b>Количество</b> [AMOUNT] (ингридиентов или полученных на выходе)
	* <!--[ITEM_CODE=CONNECT_MEASURE]--><b>Единица измерения</b> [MEASURE]: тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт); только для чтения
* <!--[ITEM_CODE=COMMENT]--><b>Комментарий</b> [COMMENT] (обязательно при редактировании товара, написать зачем меняет)
<!--/AUTODOC-->