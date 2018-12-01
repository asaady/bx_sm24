<!--WIKI_URL=https://yadadya-dev.atlassian.net/wiki/spaces/STORMATEWIKI/pages/60915713-->
<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=CLIENT]--><b>Название</b> [CLIENT] (для юр.лиц. - компания и контактное лицо, для физ.лица - ФИО)
* <!--[LIST_CODE=DISCOUNTS]--><b>Скидка клиента</b> [DISCOUNTS] (скидки текущего клиента)
* <!--[LIST_CODE=WORK_PHONE]--><b>Телефон контактного лица</b> [WORK_PHONE]
* <!--[LIST_CODE=EMAIL]--><b>Эл. почта контактного лица</b> [EMAIL]
* <!--[LIST_CODE=WORK_STREET]--><b>Адрес</b> [WORK_STREET]
* <!--[LIST_CODE=WORK_NOTES]--><b>Заметки</b> [WORK_NOTES]
* <!--[LIST_CODE=CORPORATE]--><b>Корпоративный клиент</b> [CORPORATE]

## Фильтр списка
* <!--[FILTER_CODE=SEARCH]--><b>Поиск</b> [SEARCH] (поиск по всей текстовой информации о клиенте: компания, ФИО, БИК, ОГРН, телефон, E-mail, номер контракта, адрес, заметки)
* <!--[FILTER_CODE=CORPORATE]--><b>Корпоративный клиент</b> [CORPORATE]: тип - список([Y] )

## Поля добавления/редактирования клиента
* <!--[ITEM_CODE=PERSON_TYPE]--><b>Тип клиента</b> [PERSON_TYPE]: тип - список([1] Физическое лицо, [2] Юридическое лицо); 
* <!--[ITEM_CODE=INN]--><b>ИНН</b> [INN]: проверка на уникальность
* <!--[ITEM_CODE=CORPORATE]--><b>Корпоративный клиент</b> [CORPORATE]: тип - список([Y] )
* <!--[ITEM_CODE=WORK_COMPANY]--><b>Название компании</b> [WORK_COMPANY]
* <!--[ITEM_CODE=NAME]--><b>Контактное лицо</b> [NAME]
* <!--[ITEM_CODE=BIK]--><b>БИК</b> [BIK]
* <!--[ITEM_CODE=OGRN]--><b>ОГРН</b> [OGRN]
* <!--[ITEM_CODE=WORK_PHONE]--><b>Телефон контактного лица</b> [WORK_PHONE]: ; проверка на уникальность
* <!--[ITEM_CODE=EMAIL]--><b>Эл. почта контактного лица</b> [EMAIL]: проверка на уникальность
* <!--[ITEM_CODE=REGULAR]--><b>Является ли клиент постоянным</b> [REGULAR]: тип - список([1] Да, [0] Нет); 
* <!--[ITEM_CODE=CONTRACT]--><b>Номер договора</b> [CONTRACT]
* <!--[ITEM_CODE=CONTRACT_DATE]--><b>Дата заключения договора</b> [CONTRACT_DATE]
* <!--[ITEM_CODE=DELAY]--><b>Срок отсрочки в днях</b> [DELAY] (время за которое клиент должен погасить долг)
* <!--[ITEM_CODE=WORK_STREET]--><b>Адрес</b> [WORK_STREET]
* <!--[ITEM_CODE=WORK_NOTES]--><b>Заметки</b> [WORK_NOTES]: тип - текстовая область
* <!--[ITEM_CODE=GROUP_ID_DISCOUNT]--><b>Скидки</b> [GROUP_ID_DISCOUNT]: тип - список([28] Скидка 10%, [29] Скидка 20%), список множественного выбора
<!--/AUTODOC-->