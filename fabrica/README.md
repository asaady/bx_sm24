<!--WIKI_URL=https://yadadya-dev.atlassian.net/wiki/spaces/STORMATEWIKI/pages/66453510-->
<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=NAME]--><b>Название</b> [NAME]
* <!--[LIST_CODE=AMOUNT]--><b>Вес одной порции</b> [AMOUNT]
* <!--[LIST_CODE=USER_FORMATED]--><b>Кем сделана</b> [USER_FORMATED]
* <!--[LIST_CODE=QUANTITY]--><b>Штук на складе</b> [QUANTITY]
* <!--[LIST_CODE=ACTIVE]--><b>Активность</b> [ACTIVE]

## Поля добавления/редактирования 
* <!--[ITEM_CODE=ITEMS]--><b>Товары</b> [ITEMS]: тип - таблица
	* <!--[ITEM_CODE=ITEMS_PRODUCT_ID]--><b>Товар</b> [PRODUCT_ID]
	* <!--[ITEM_CODE=ITEMS_NAME]--><b>Придумайте название</b> [NAME]
	* <!--[ITEM_CODE=ITEMS_MEASURE]--><b>Единица измерения</b> [MEASURE]: тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт); только для чтения
	* <!--[ITEM_CODE=ITEMS_TYPE]--><b>Выберите вид производства</b> [TYPE]: тип - список([0] простой товар, [1] приготовить из нескольких ингридиентов, [2] разделить на несколько товаров)
	* <!--[ITEM_CODE=ITEMS_AMOUNT]--><b>Кол-во одной порции товара</b> [AMOUNT]
	* <!--[ITEM_CODE=ITEMS_AMOUNT_MEASURE]--><b>Единица измерения</b> [AMOUNT_MEASURE]: тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт)
	* <!--[ITEM_CODE=ITEMS_MEASURE_FROM]--><b>, где</b> [MEASURE_FROM]
	* <!--[ITEM_CODE=ITEMS_MEASURE_TO]--><b>=</b> [MEASURE_TO]
	* <!--[ITEM_CODE=ITEMS_FAULT_RATIO]--><b>Погрешность соотношений</b> [FAULT_RATIO]
	* <!--[ITEM_CODE=ITEMS_CONNECT]--><b>Составляющие</b> [CONNECT]: тип - таблица
		* <!--[ITEM_CODE=CONNECT_PRODUCT_ID]--><b>PRODUCT_ID</b> [PRODUCT_ID]
		* <!--[ITEM_CODE=CONNECT_NAME]--><b>NAME</b> [NAME]
		* <!--[ITEM_CODE=CONNECT_AMOUNT]--><b>AMOUNT</b> [AMOUNT]
		* <!--[ITEM_CODE=CONNECT_MEASURE]--><b>MEASURE</b> [MEASURE]: тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт)
		* <!--[ITEM_CODE=CONNECT_MEASURE_FROM]--><b>MEASURE_FROM</b> [MEASURE_FROM]
		* <!--[ITEM_CODE=CONNECT_MEASURE_TO]--><b>MEASURE_TO</b> [MEASURE_TO]
		* <!--[ITEM_CODE=CONNECT_AMOUNT_RATIO]--><b>AMOUNT_RATIO</b> [AMOUNT_RATIO]
<!--/AUTODOC-->