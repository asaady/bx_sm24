INSERT INTO b_sm_finance_prod_price_log (`DATE`, STORE_ID, PRODUCT_ID, CATALOG_GROUP_ID, PRICE, CURRENCY, USER_ID, SITE_ID, DESCRIPTION)
	SELECT * FROM (
		SELECT 
			b_catalog_store_docs.DATE_DOCUMENT as `DATE`,
			b_catalog_docs_element.STORE_TO as STORE_ID,
			b_catalog_docs_element.ELEMENT_ID as PRODUCT_ID,
			0 as CATALOG_GROUP_ID,
			b_catalog_docs_element.PURCHASING_PRICE as PRICE,
			b_catalog_store_docs.CURRENCY as CURRENCY,
			1 as USER_ID,
			"s1" as SITE_ID,
			CONCAT('overhead ', b_catalog_store_docs.ID) as DESCRIPTION
		FROM `b_catalog_docs_element`
			LEFT JOIN `b_catalog_store_docs` ON b_catalog_docs_element.DOC_ID=b_catalog_store_docs.ID
			LEFT JOIN `b_sm_docs_element` ON b_catalog_docs_element.ID=b_sm_docs_element.DOCS_ELEMENT_ID
		WHERE
			b_catalog_store_docs.DOC_TYPE="A"
		GROUP BY `DATE`, PRODUCT_ID, STORE_ID
	UNION
		SELECT 
			b_catalog_store_docs.DATE_DOCUMENT as `DATE`,
			b_catalog_docs_element.STORE_TO as STORE_ID,
			b_catalog_docs_element.ELEMENT_ID as PRODUCT_ID,
			b_catalog_group.ID as CATALOG_GROUP_ID,
			b_sm_docs_element.SHOP_PRICE as PRICE,
			b_catalog_store_docs.CURRENCY as CURRENCY,
			1 as USER_ID,
			"s1" as SITE_ID,
			CONCAT('overhead ', b_catalog_store_docs.ID) as DESCRIPTION
		FROM `b_catalog_docs_element`
			LEFT JOIN `b_catalog_store_docs` ON b_catalog_docs_element.DOC_ID=b_catalog_store_docs.ID
			LEFT JOIN `b_sm_docs_element` ON b_catalog_docs_element.ID=b_sm_docs_element.DOCS_ELEMENT_ID
			LEFT JOIN `b_catalog_group` ON b_catalog_group.NAME=CONCAT("SHOP_ID_", b_catalog_docs_element.STORE_TO)
		WHERE
			b_catalog_store_docs.DOC_TYPE="A"
		GROUP BY `DATE`, PRODUCT_ID, STORE_ID) prod_price_log 
	WHERE PRICE > 0
	ORDER BY `DATE` ASC;

INSERT INTO b_sm_finance_prod_amount_log (`DATE`, STORE_ID, PRODUCT_ID, AMOUNT, USER_ID, SITE_ID, DESCRIPTION)
	SELECT * FROM (
		SELECT 
			b_catalog_store_docs.DATE_DOCUMENT as `DATE`,
			b_catalog_docs_element.STORE_TO as STORE_ID,
			b_catalog_docs_element.ELEMENT_ID as PRODUCT_ID,
			b_catalog_docs_element.AMOUNT as AMOUNT,
			1 as USER_ID,
			's1' as SITE_ID,
			CONCAT('overhead ', b_catalog_store_docs.ID) as DESCRIPTION
		FROM b_catalog_docs_element
			LEFT JOIN b_catalog_store_docs ON b_catalog_docs_element.DOC_ID=b_catalog_store_docs.ID
		WHERE
			b_catalog_store_docs.DOC_TYPE='A'
		GROUP BY `DATE`, PRODUCT_ID, STORE_ID
	UNION 
		SELECT 
			b_sale_order.DATE_PAYED as `DATE`,
			b_sale_order.STORE_ID as STORE_ID,
			b_sale_basket.PRODUCT_ID as PRODUCT_ID,
			b_sale_basket.QUANTITY as AMOUNT,
			1 as USER_ID,
			's1' as SITE_ID,
			CONCAT('cash ', b_sale_order.ID) as DESCRIPTION
		FROM b_sale_basket
			LEFT JOIN b_sale_order ON b_sale_basket.ORDER_ID=b_sale_order.ID
		WHERE
			b_sale_order.PAYED='Y'
			AND b_sale_order.CANCELED='N'
		GROUP BY `DATE`, PRODUCT_ID, STORE_ID) prod_amount_log 
	ORDER BY `DATE` ASC;

INSERT INTO `b_sm_finance_report_tasks` (`DATE`, `PRODUCT_ID`, `STORE_ID`)
SELECT 
	DATE_FORMAT(b_catalog_store_docs.DATE_DOCUMENT, '%Y-%m-%d') as `DATE`,
	b_catalog_docs_element.ELEMENT_ID as PRODUCT_ID,
	b_catalog_docs_element.STORE_TO as STORE_ID
FROM `b_catalog_docs_element`
	LEFT JOIN `b_catalog_store_docs` ON b_catalog_docs_element.DOC_ID=b_catalog_store_docs.ID
WHERE
	b_catalog_store_docs.DOC_TYPE="A"
GROUP BY `DATE`, PRODUCT_ID, STORE_ID;

INSERT INTO `b_sm_finance_report_tasks` (`DATE`, `PRODUCT_ID`, `STORE_ID`)
SELECT 
	DATE_FORMAT(b_sale_order.DATE_PAYED, '%Y-%m-%d') as `DATE`,
	b_sale_basket.PRODUCT_ID as PRODUCT_ID,
	b_sale_order.STORE_ID as STORE_ID
FROM `b_sale_basket`
	LEFT JOIN `b_sale_order` ON b_sale_basket.ORDER_ID=b_sale_order.ID
WHERE
	b_sale_order.PAYED="Y"
	AND b_sale_order.CANCELED="N"
GROUP BY `DATE`, PRODUCT_ID, STORE_ID;

INSERT INTO b_sm_basket (ID, PURCHASING_PRICE, PURCHASING_CURRENCY, QUANTITY_FIRST)
SELECT * FROM
(SELECT 
	b_sale_basket.ID as ID,
	b_sm_finance_prod_price_log.PRICE as PURCHASING_PRICE,
	b_sm_finance_prod_price_log.CURRENCY as PURCHASING_CURRENCY,
	b_sale_basket.QUANTITY as QUANTITY_FIRST
FROM `b_sale_basket`
	LEFT JOIN `b_sale_order` ON b_sale_basket.ORDER_ID=b_sale_order.ID
	RIGHT JOIN `b_sm_finance_prod_price_log` 
		ON b_sale_basket.PRODUCT_ID=b_sm_finance_prod_price_log.PRODUCT_ID 
			AND b_sale_order.DATE_PAYED>=b_sm_finance_prod_price_log.DATE
			AND b_sale_order.STORE_ID>=b_sm_finance_prod_price_log.STORE_ID
WHERE
	b_sale_order.PAYED="Y"
	AND b_sale_order.CANCELED="N"
	AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID=0
	AND b_sm_finance_prod_price_log.PRICE>0
ORDER BY b_sm_finance_prod_price_log.DATE DESC) basket
GROUP BY basket.ID;

INSERT INTO `b_sm_finance_money` (`DATE`, `STORE_ID`, `TYPE`, `OUTGO`, `ITEM_TYPE`, `ITEM_ID`, `PRICE`)
SELECT 
	`b_catalog_store_docs`.`DATE_DOCUMENT` as `DATE`,
	`b_sm_store_docs`.`STORE_ID` as `STORE_ID`,
	"cash" as `TYPE`,
	"contractor" as `OUTGO`,
	`b_catalog_store_docs`.`ID` as `ITEM_TYPE`,
	`b_catalog_store_docs`.`CONTRACTOR_ID` as `ITEM_ID`,
	`b_sm_store_docs`.`TOTAL_FACT` as `PRICE`
FROM `b_catalog_store_docs`
	LEFT JOIN `b_sm_store_docs` ON `b_sm_store_docs`.`DOC_ID`=`b_catalog_store_docs`.`ID`
WHERE
	`b_catalog_store_docs`.`DOC_TYPE`="A"
	AND `b_catalog_store_docs`.`STATUS`="Y"
	AND `b_catalog_store_docs`.`CONTRACTOR_ID`>0
	AND `b_sm_store_docs`.`TOTAL_FACT`>0;