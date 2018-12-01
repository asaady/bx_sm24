INSERT INTO `b_sm_store_product` (`PRODUCT_ID`, `STORE_ID`, `PURCHASING_PRICE`, `PURCHASING_CURRENCY`, `END_DATE`)
SELECT * FROM (
	SELECT 
		`b_catalog_docs_element`.`ELEMENT_ID` AS `PRODUCT_ID`,
		`b_catalog_docs_element`.`STORE_TO` AS `STORE_ID`,
		`b_catalog_docs_element`.`PURCHASING_PRICE` AS `PURCHASING_PRICE`,
		`b_catalog_store_docs`.`CURRENCY` AS `PURCHASING_CURRENCY`,
		`b_sm_docs_element`.`END_DATE` AS `END_DATE`
	FROM `b_catalog_docs_element`
		LEFT JOIN `b_sm_docs_element` ON `b_sm_docs_element`.`DOCS_ELEMENT_ID` = `b_catalog_docs_element`.`ID`
		LEFT JOIN `b_catalog_store_docs` ON `b_catalog_docs_element`.`ID` = `b_catalog_docs_element`.`DOC_ID`
			AND `b_catalog_store_docs`.`DOC_TYPE` = 'A'
			AND `b_catalog_store_docs`.`STATUS` = 'Y'
	ORDER BY `b_catalog_store_docs`.`DATE_DOCUMENT` DESC) `store_product`
GROUP BY `PRODUCT_ID`, `STORE_ID`;

INSERT INTO `b_sm_store_product` (`PRODUCT_ID`, `STORE_ID`, `PURCHASING_PRICE`, `PURCHASING_CURRENCY`)
SELECT 
	`b_catalog_product`.`ID` AS `PRODUCT_ID`,
	`b_catalog_store`.`ID` AS `STORE_ID`,
	`b_catalog_product`.`PURCHASING_PRICE` AS `PURCHASING_PRICE`,
	`b_catalog_product`.`PURCHASING_CURRENCY` AS `PURCHASING_CURRENCY`
FROM `b_catalog_product`
	LEFT JOIN `b_catalog_store` ON `b_catalog_store`.`ACTIVE` = 'Y'
WHERE CONCAT(`b_catalog_product`.`ID`, '_', `b_catalog_store`.`ID`) NOT IN (SELECT CONCAT(`PRODUCT_ID`, "_", `STORE_ID`) FROM `b_sm_store_product`)
	AND `b_catalog_product`.`PURCHASING_PRICE` > 0;

UPDATE `b_sm_store_product`
	JOIN `b_catalog_product` ON `b_sm_store_product`.`PRODUCT_ID` = `b_catalog_product`.`ID`
	JOIN `b_catalog_store` ON `b_sm_store_product`.`STORE_ID` = `b_catalog_store`.`ID` AND `b_catalog_store`.`ACTIVE` = 'Y'
SET 
	`b_sm_store_product`.`PURCHASING_PRICE` = `b_catalog_product`.`PURCHASING_PRICE`,
	`b_sm_store_product`.`PURCHASING_CURRENCY` = `b_catalog_product`.`PURCHASING_CURRENCY`
WHERE (`b_sm_store_product`.`PURCHASING_PRICE` <= 0 OR `b_sm_store_product`.`PURCHASING_PRICE` IS NULL)
	AND `b_catalog_product`.`PURCHASING_PRICE` > 0;

INSERT INTO `b_sm_shop_product_history` (`PRODUCT_ID`, `SHOP_ID`)
SELECT 
	`b_catalog_product`.`ID` AS `PRODUCT_ID`,
	`b_catalog_store_product`.`STORE_ID` AS `SHOP_ID`
FROM `b_catalog_product`
	LEFT JOIN `b_catalog_store_product` ON `b_catalog_product`.`ID` = `b_catalog_store_product`.`PRODUCT_ID`
WHERE `b_catalog_store_product`.`AMOUNT` > 0 AND CONCAT(`b_catalog_product`.`ID`, '_', `b_catalog_store_product`.`STORE_ID`) NOT IN (SELECT CONCAT(`PRODUCT_ID`, "_", `SHOP_ID`) FROM `b_sm_shop_product_history`)
GROUP BY `PRODUCT_ID`, `SHOP_ID`;

INSERT INTO `b_sm_shop_product_history` (`PRODUCT_ID`, `SHOP_ID`)
SELECT 
	`b_catalog_docs_element`.`ELEMENT_ID` AS `PRODUCT_ID`,
	`b_catalog_docs_element`.`STORE_TO` AS `SHOP_ID`
FROM `b_catalog_docs_element`
WHERE CONCAT(`b_catalog_docs_element`.`ELEMENT_ID`, '_', `b_catalog_docs_element`.`STORE_TO`) NOT IN (SELECT CONCAT(`PRODUCT_ID`, "_", `SHOP_ID`) FROM `b_sm_shop_product_history`)
GROUP BY `PRODUCT_ID`, `SHOP_ID`;

INSERT INTO `b_sm_shop_product_history` (`PRODUCT_ID`, `SHOP_ID`)
SELECT 
	`b_sm_inventory_product`.`PRODUCT_ID` AS `PRODUCT_ID`,
	`b_sm_inventory`.`STORE_ID` AS `SHOP_ID`
FROM `b_sm_inventory_product`
	LEFT JOIN `b_sm_inventory` ON `b_sm_inventory`.`ID` = `b_sm_inventory_product`.`INVENTORY_ID`
WHERE CONCAT(`b_sm_inventory_product`.`PRODUCT_ID`, '_', `b_sm_inventory`.`STORE_ID`) NOT IN (SELECT CONCAT(`PRODUCT_ID`, "_", `SHOP_ID`) FROM `b_sm_shop_product_history`)
GROUP BY `PRODUCT_ID`, `SHOP_ID`;