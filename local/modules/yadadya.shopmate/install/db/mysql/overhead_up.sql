UPDATE `b_sm_store_docs`
	JOIN `b_catalog_docs_element` ON `b_sm_store_docs`.`DOC_ID` = `b_catalog_docs_element`.`DOC_ID`
SET 
	`b_sm_store_docs`.`STORE_ID` = `b_catalog_docs_element`.`STORE_TO`
WHERE `b_catalog_docs_element`.`STORE_TO` > 0