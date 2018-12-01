<!--WIKI_URL=https://yadadya-dev.atlassian.net/wiki/spaces/STORMATEWIKI/pages/1093445-->
<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=NUMBER_DOCUMENT]--><b>Номер накладной</b> [NUMBER_DOCUMENT]
* <!--[LIST_CODE=PRODUCTS_COUNT]--><b>Общее кол-во товаров</b> [PRODUCTS_COUNT]
* <!--[LIST_CODE=TOTAL_SUMM]--><b>Сумма</b> [TOTAL_SUMM] (сумма накладной)
* <!--[LIST_CODE=DATE_DOCUMENT]--><b>Дата накладной</b> [DATE_DOCUMENT]
* <!--[LIST_CODE=TOTAL_PERCENT]--><b>Оплата</b> [TOTAL_PERCENT] (процент оплаченности накладной)

## Фильтр списка
* <!--[FILTER_CODE=CONTRACTOR]--><b>Выбор поставщика</b> [CONTRACTOR] (фильтр по накладным с установленным поставщиком)
* <!--[FILTER_CODE=PRODUCT]--><b>Товар в накладной</b> [PRODUCT] (по названию или штрихкоду)
* <!--[FILTER_CODE=DATE_FROM]--><b>Дата от mm.dd.yyyy</b> [DATE_FROM]
* <!--[FILTER_CODE=DATE_TO]--><b>Дата до mm.dd.yyyy</b> [DATE_TO]
* <!--[FILTER_CODE=PERISHABLE]--><b>Показать только скоропортящиеся товары</b> [PERISHABLE]: тип - список([1] perishable)
* <!--[FILTER_CODE=EXPIRATION]--><b>Показать накладные с уже испорченным товаром</b> [EXPIRATION]: тип - список([1] expiration)

## Поля добавления/редактирования 
* <!--[ITEM_CODE=NUMBER_DOCUMENT]--><b>Номер накладной</b> [NUMBER_DOCUMENT]: 
* <!--[ITEM_CODE=DATE_DOCUMENT]--><b>Дата накладной</b> [DATE_DOCUMENT]: 
* <!--[ITEM_CODE=CONTRACTOR_ID]--><b>Поставщик</b> [CONTRACTOR_ID] (выбор поставщика по доступной информации (фио, название компании, тел., e-mail, ИНН и т.д.))
* <!--[ITEM_CODE=ELEMENT]--><b>Товары</b> [ELEMENT]: тип - таблица
	* <!--[ITEM_CODE=ELEMENT_ELEMENT_ID]--><b>Название (Штрихкод)</b> [ELEMENT_ID] (выбор товара по названию или штрихкоду)
	* <!--[ITEM_CODE=ELEMENT_PURCHASING_PRICE]--><b>Цена за шт.</b> [PURCHASING_PRICE] (вычисляется из "Общей цены с НДС", "НДС" и "Количества по накладной" ): только для чтения
	* <!--[ITEM_CODE=ELEMENT_MEASURE]--><b>Ед. изм.</b> [MEASURE]: тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт); только для чтения
	* <!--[ITEM_CODE=ELEMENT_AMOUNT]--><b>Фактическое количество</b> [AMOUNT] (сколько привезли на самом деле, а не по документам)
	* <!--[ITEM_CODE=ELEMENT_SHOP_PRICE]--><b>Действующая цена</b> [SHOP_PRICE] (цена на продажу по базовому прайслисту)
	* <!--[ITEM_CODE=ELEMENT_DOC_AMOUNT]--><b>Количество по накладной</b> [DOC_AMOUNT] (по документам поставщика)
	* <!--[ITEM_CODE=ELEMENT_PURCHASING_NDS]--><b>НДС</b> [PURCHASING_NDS]: тип - список([0] НДС 0%, [10] НДС 10%, [18] НДС 18%)
	* <!--[ITEM_CODE=ELEMENT_PURCHASING_SUMM]--><b>Общая цена с НДС</b> [PURCHASING_SUMM] (по документам поставщика)
	* <!--[ITEM_CODE=ELEMENT_NDS_VALUE]--><b>Величина НДС</b> [NDS_VALUE] (вычисляется из "Общей цены с НДС" и "НДС"): только для чтения
	* <!--[ITEM_CODE=ELEMENT_START_DATE]--><b>Дата выработки</b> [START_DATE] (для товаров с установленным сроком годности)
* <!--[ITEM_CODE=TOTAL_SUMM]--><b>Сумма</b> [TOTAL_SUMM] (сумма накладной): только для чтения
* <!--[ITEM_CODE=TOTAL_FACT]--><b>Сколько заплатили по факту</b> [TOTAL_FACT]
<!--/AUTODOC-->