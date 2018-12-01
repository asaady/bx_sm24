<!--WIKI_URL=https://yadadya-dev.atlassian.net/wiki/spaces/STORMATEWIKI/pages/962543-->
<!--AUTODOC-->
## Столбцы списка
* <!--[LIST_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[LIST_CODE=NAME]--><b>Название продукта</b> [NAME]
* <!--[LIST_CODE=AMOUNT]--><b>Количество</b> [AMOUNT] (в магазине)
* <!--[LIST_CODE=MEASURE]--><b>В чем измеряется</b> [MEASURE] (ед. изм.)
* <!--[LIST_CODE=PURCHASING_PRICE]--><b>Закупочная цена</b> [PURCHASING_PRICE]
* <!--[LIST_CODE=PURCHASING_CURRENCY]--><b>PURCHASING_CURRENCY</b> [PURCHASING_CURRENCY]
* <!--[LIST_CODE=PRICE]--><b>Цена на продажу</b> [PRICE]
* <!--[LIST_CODE=CURRENCY]--><b>CURRENCY</b> [CURRENCY]
* <!--[LIST_CODE=END_DATE]--><b>Срок годности (если есть скоропорт)</b> [END_DATE] (дата окончания срока догности, высчитывается из даты выработки (задается в накладной) + срок годности(задается в товаре))
* <!--[LIST_CODE=TIMESTAMP_X]--><b>Дата последнего изменения</b> [TIMESTAMP_X]

## Фильтр списка
* <!--[FILTER_CODE=ID]--><b>ID</b> [ID] (уникальный идентификатор)
* <!--[FILTER_CODE=SECTION]--><b>Категория</b> [SECTION]
* <!--[FILTER_CODE=SUBSECTION]--><b>Подкатегория</b> [SUBSECTION]
* <!--[FILTER_CODE=OVERHEAD_DATE]--><b>Дата прихода</b> [OVERHEAD_DATE] (фильтр по накладным установленной даты)
* <!--[FILTER_CODE=PACK]--><b>Упаковка</b> [PACK]
* <!--[FILTER_CODE=STORE_PRODUCT]--><b>Выбрать товары</b> [STORE_PRODUCT] (склад - товары созданные суперадмином для всех, собственные - магазином): тип - список([ALL] Products of base and store, [BASE] Products of base, [STORE] Products of store)
* <!--[FILTER_CODE=PRODUCT]--><b>Товар в накладной</b> [PRODUCT] (по названию или штрихкоду)
* <!--[FILTER_CODE=CONTRACTOR]--><b>Выбор поставщика</b> [CONTRACTOR] (фильтр по накладным с установленным поставщиком)
* <!--[FILTER_CODE=PERISHABLE]--><b>Показать только скоропортящиеся товары</b> [PERISHABLE]: тип - список([1] perishable)

## Поля добавления/редактирования 
* <!--[ITEM_CODE=DATE_CREATE]--><b>Дата создания</b> [DATE_CREATE]: только для чтения
* <!--[ITEM_CODE=TIMESTAMP_X]--><b>Дата последнего изменения</b> [TIMESTAMP_X]: только для чтения
* <!--[ITEM_CODE=IBLOCK_SECTION]--><b>Категория товара</b> [IBLOCK_SECTION]: тип - список(...)
* <!--[ITEM_CODE=NAME]--><b>Название продукта</b> [NAME]: 
* <!--[ITEM_CODE=BARCODE]--><b>Штрихкод</b> [BARCODE]
* <!--[ITEM_CODE=MEASURE]--><b>В чем измеряется</b> [MEASURE] (ед. изм.): тип - список([5] шт, [1] м, [2] л., [3] г, [4] кг, [6] ед, [7] т, [8] шт., [9] кг, [10] шт)
* <!--[ITEM_CODE=AMOUNT]--><b>Количество</b> [AMOUNT] (в магазине)
* <!--[ITEM_CODE=PURCHASING_PRICE]--><b>Закупочная цена</b> [PURCHASING_PRICE]
* <!--[ITEM_CODE=PRICE]--><b>Цена на продажу</b> [PRICE]
* <!--[ITEM_CODE=SHELF_LIFE]--><b>Срок годности</b> [SHELF_LIFE]
* <!--[ITEM_CODE=DNC_TYPE_CODE]--><b>Код вида товара Дэнси</b> [DNC_TYPE_CODE]: тип - список([0] обычный товар, [1] табачное изделие, [2] немаркируемый алкоголь, [3] маркируемый алкоголь)
* <!--[ITEM_CODE=ALCCODE]--><b>Код ЕГАИС</b> [ALCCODE] (заполняется автоматом в разделе Алкогольная продукция, можно вручную, но лучше не трогать)
* <!--[ITEM_CODE=DETAIL_TEXT]--><b>Описание товара</b> [DETAIL_TEXT]: тип - текстовая область
* <!--[ITEM_CODE=COMMENT]--><b>Комментарий</b> [COMMENT] (обязательно при редактировании товара, написать зачем меняет)
<!--/AUTODOC-->

* <b>Админ может редактировать полностью любой товар</b>
* <b>Магазин может создавай и полностью редактировать только свои товары</b>
* <b>В товарах склада (общие) магазин может редактировать только: Количество, Закупочная цена, Цена на продажу</b>