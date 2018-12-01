<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=ACCOUNT_NUMBER]--><b>Номер чека</b> [ACCOUNT_NUMBER]
* <!--[LIST_CODE=USER_NAME]--><b>ФИО/Компания</b> [USER_NAME] (для юр.лиц. - компания и контактное лицо, для физ.лица - ФИО)
* <!--[LIST_CODE=USER_PHONE]--><b>Телефон</b> [USER_PHONE]
* <!--[LIST_CODE=PRICE]--><b>Сумма покупки</b> [PRICE]
* <!--[LIST_CODE=SUM_NOPAID]--><b>Сумма долга</b> [SUM_NOPAID] (неоплаченная сумма при частичной оплате)
* <!--[LIST_CODE=DATE]--><b>Дата покупки</b> [DATE]
* <!--[LIST_CODE=STATUS_NAME]--><b>Статус</b> [STATUS_NAME]

## Фильтр списка
* <!--[FILTER_CODE=DATE_FROM]--><b>Дата от dd.mm.yyyy</b> [DATE_FROM]
* <!--[FILTER_CODE=DATE_TO]--><b>Дата до dd.mm.yyyy</b> [DATE_TO]
* <!--[FILTER_CODE=ACCOUNT_NUMBER]--><b>Номер чека</b> [ACCOUNT_NUMBER]
* <!--[FILTER_CODE=DEBT]--><b>Только покупки в долг</b> [DEBT]: тип - список([Y] )

## Поля добавления/редактирования 
* <!--[ITEM_CODE=PRODUCT]--><b>PRODUCT</b> [PRODUCT]: тип - таблица
	* <!--[ITEM_CODE=PRODUCT_PRODUCT_ID]--><b>Название (Штрихкод)</b> [PRODUCT_ID] (выбор товара по названию или штрихкоду)
	* <!--[ITEM_CODE=PRODUCT_AMOUNT]--><b>Остаток на складе</b> [AMOUNT] (остаток текущего товара на складе): только для чтения
	* <!--[ITEM_CODE=PRODUCT_QUANTITY]--><b>Количество</b> [QUANTITY] (количество продаваемого отвара, для весового товара при сканировании штрихкода устанавливается автоматически)
	* <!--[ITEM_CODE=PRODUCT_PRICE]--><b>Цена</b> [PRICE] (цена за 1 штуку с учетом установленных скидок): только для чтения
	* <!--[ITEM_CODE=PRODUCT_SUMM]--><b>Сумма</b> [SUMM]: только для чтения
* <!--[ITEM_CODE=USER_ID]--><b>Клиент</b> [USER_ID] (при выборе клиента пересчитываются цены, если у клиента есть скидки): 
* <!--[ITEM_CODE=PRICE]--><b>Сумма покупки</b> [PRICE]: только для чтения
* <!--[ITEM_CODE=SUM_NOPAID]--><b>Сумма долга</b> [SUM_NOPAID] (неоплаченная сумма при частичной оплате): только для чтения
* <!--[ITEM_CODE=PAID]--><b>Сумма оплаты</b> [PAID]
<!--/AUTODOC-->