<!--WIKI_URL=https://yadadya-dev.atlassian.net/wiki/spaces/STORMATEWIKI/pages/66486282-->
<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=NAME_FORMATED]--><b>Поставщик</b> [NAME_FORMATED] (для юр.лиц. - компания и контактное лицо, для физ.лица - ФИО)
* <!--[LIST_CODE=PERSON_TYPE_FORMATED]--><b>Тип поставщика</b> [PERSON_TYPE_FORMATED]
* <!--[LIST_CODE=DISCOUNT_FORMATED]--><b>Наша скидка</b> [DISCOUNT_FORMATED]
* <!--[LIST_CODE=LAST_DATE_DOCUMENT]--><b>Дата последней закупки</b> [LAST_DATE_DOCUMENT]
* <!--[LIST_CODE=DEBT]--><b>Задолженность по счетам</b> [DEBT]

## Фильтр списка
* <!--[FILTER_CODE=SEARCH]--><b>SEARCH</b> [SEARCH]
* <!--[FILTER_CODE=PERSON_TYPE]--><b>Тип поставщика</b> [PERSON_TYPE]: тип - список([1] fiz, [2] yur)

## Поля добавления/редактирования поставщика
* <!--[ITEM_CODE=PERSON_TYPE]--><b>Тип поставщика</b> [PERSON_TYPE]: тип - список([1] Физическое лицо, [2] Юридическое лицо); 
* <!--[ITEM_CODE=INN]--><b>ИНН</b> [INN] (по введенному ИНН автоматически подгружаетчся подгружается информация о компании): проверка на уникальность
* <!--[ITEM_CODE=COMPANY]--><b>Название компании</b> [COMPANY]
* <!--[ITEM_CODE=PERSON_NAME]--><b>Контактное лицо</b> [PERSON_NAME]
* <!--[ITEM_CODE=BIK]--><b>БИК</b> [BIK]
* <!--[ITEM_CODE=OGRN]--><b>ОГРН</b> [OGRN]
* <!--[ITEM_CODE=NDS]--><b>НДС поставщика</b> [NDS]: тип - список([0] НДС 0%, [10] НДС 10%, [18] НДС 18%); 
* <!--[ITEM_CODE=PHONE]--><b>Телефон контактного лица</b> [PHONE]: ; проверка на уникальность
* <!--[ITEM_CODE=EMAIL]--><b>Эл. почта контактного лица</b> [EMAIL]: ; проверка на уникальность
* <!--[ITEM_CODE=REGULAR]--><b>Является ли поставщик постоянным</b> [REGULAR]: тип - список([1] Да, [0] Нет); 
* <!--[ITEM_CODE=CONTRACT]--><b>Номер договора</b> [CONTRACT]
* <!--[ITEM_CODE=CONTRACT_DATE]--><b>Дата заключения договора</b> [CONTRACT_DATE]
* <!--[ITEM_CODE=DELAY]--><b>Срок отсрочки в днях</b> [DELAY]
* <!--[ITEM_CODE=ADDRESS]--><b>Юридический адрес</b> [ADDRESS] (Юридический адрес для юр. лица или адрес физ. лица): тип - текстовая область
* <!--[ITEM_CODE=ADDRESS_FACT]--><b>Фактический адрес</b> [ADDRESS_FACT] (для юр. лица): тип - текстовая область
* <!--[ITEM_CODE=NOTES]--><b>Заметки</b> [NOTES]: тип - текстовая область
* <!--[ITEM_CODE=DISCOUNT]--><b>Наша скидка</b> [DISCOUNT]
<!--/AUTODOC-->