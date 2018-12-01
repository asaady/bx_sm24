<?php
namespace Yadadya\Shopmate;

use Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceReport
{
	public static function updateReport(array $data = array())
	{
		if(!empty($data["DATE"]) && !empty($data["PRODUCT_ID"]) && !empty($data["STORE_ID"])
			&& ($data["SALE"] != 0 || $data["SALE_QUANTITY"] != 0 
				|| $data["PURCHASE"] != 0 || $data["PURCHASE_AMOUNT"] != 0
				|| $data["PROFIT"] != 0 || $data["PRICE_PROFIT"] != 0))
		{
			$data["DATE"] = new Type\DateTime(date("Y-m-d", strtotime($data["DATE"])), "Y-m-d");
			$result = Internals\ReportTable::getList(array(
				"select" => array("ID"),
				"filter" => array(
					"DATE" => $data["DATE"],
					"PRODUCT_ID" => $data["PRODUCT_ID"],
					"STORE_ID" => $data["STORE_ID"],
				)
			));
			if($row = $result->fetch())
				Internals\ReportTable::update($row["ID"], $data);
			else
				Internals\ReportTable::add($data);
		}
	}

	public static function updateBlock($store = 0, $field = "")
	{
		if(!$store) $store = \Yadadya\Shopmate\Shops::getUserShop();
		if($_REQUEST["fin_ajax"] == "Y")
		{
			global $APPLICATION;
			$APPLICATION->RestartBuffer();
			if(!empty($_REQUEST["field"]))
				$field = $_REQUEST["field"];
		}
		else
			echo "<div id=\"fin_up_block\">";

		FinanceReport::removeOldTasks();
		$result = Internals\ReportTasksTable::getList(array(
			"select" => array("CNT" => new Entity\ExpressionField("CNT", "COUNT(*)")),
			"filter" => array("STORE_ID" => $store)
		));
		if($row = $result->fetch())
			$cnt = $row["CNT"];
		if($cnt > 0):
			echo Loc::getMessage('MESS_LEFT')." ".$cnt." ".Loc::getMessage('MESS_PROD')."...";
			FinanceReport::cron($store);?>
		<script>
			if(window.jQuery) {
			<?if(!empty($field)):?>
				$.ajax({
					type: 'POST',
					data: {
						fin_ajax: 'Y',
						field: '<?=$field?>'
					},
					success: function(data) {
						$('#fin_up_block').html(data);
					}
				});
			<?else:?>
				<?foreach(array("SALE", "PURCHASE", "PROFIT") as $field):?>
				$.ajax({
					type: 'POST',
					data: {
						fin_ajax: 'Y',
						field: '<?=$field?>'
					},
					success: function(data) {
						$('#fin_up_block').html(data);
					}
				});
				<?endforeach?>
			<?endif?>
			}
		</script>
		<?endif;
		if($_REQUEST["fin_ajax"] == "Y")
			die();
		else
			echo "</div>";
	}

	public static function cron($store = 0, $fields = array(), $user_filter = array())
	{
		$store = intval($store);
		$fields = (array) $fields;
		$fields = array_values($fields);
		$complexFields = array(
			array("SALE", "SALE_QUANTITY"),
			array("PURCHASE", "PURCHASE_AMOUNT"),
			array("PROFIT", "PRICE_PROFIT"),
		);
		$summFields = array("SALE", "SALE_QUANTITY", "PURCHASE", "PURCHASE_AMOUNT", "PROFIT", "PRICE_PROFIT");
		foreach($complexFields as $cFields)
		{
			if(in_array($cFields[0], $fields) && !in_array($cFields[1], $fields))
				$fields[] = $cFields[1];
			if(in_array($cFields[1], $fields) && !in_array($cFields[0], $fields))
				$fields[] = $cFields[0];
		}
		foreach($fields as $key => $field) 
			if(!in_array($field, $summFields))
				unset($fields[$key]);
		foreach($fields as $key => $field) 
			if($field == "*")
				$fields = array_splice($fields, $key, 1, $summFields);
		if(empty($fields))
			$fields = $summFields;
		$updateTask = array();
		$filterReport = $filterTask = array("LOGIC" => "OR");
		foreach($fields as $key => $field)
		{
			$filterTask["!".$field] = $updateTask[$field] = "Y";
			$filterReport["!".$field] = false;
		}
		if($store > 0)
		{
			$reportDates = array();
			$indexReports = array();
			$filter = array_merge((array) $user_filter, array("STORE_ID" => $store, $filterTask));
			$result = Internals\ReportTasksTable::getList(array(
				"filter" => $filter,
				"order" => array("DATE"),
				"limit" => 1000
			));
			while($row = $result->fetch())
			{
				$date = $row["DATE"]->toString();
				$date = date("Y-m-d 00:00:00", strtotime($date));
				$reportDates[$date][] = $row["PRODUCT_ID"];
				$indexReports[$date][$row["PRODUCT_ID"]][] = $row["ID"];
			}
			$reportDates = array_slice($reportDates, 0, 2);
			/*foreach($reportDates as $date => $prodId)
				$reportDates[$date] = array_slice($prodId, 0, 100);*/
			foreach($reportDates as $date => $prodId) 
			{
				$result = FinanceReport::calcElements(array(
					"select" => array_merge(array("ID"), $fields),
					"filter" => array(
						"DATE_FROM" => $date, 
						"DATE_TO" => $date, 
						$filterReport,
						"STORE_ID" => $store,
						"ID" => $prodId
					)
				));
				while($row = $result->fetch())
				{
					$arUpdate = array(
						"DATE" => $date,
						"PRODUCT_ID" => $row["ID"],
						"STORE_ID" => $store
					);
					foreach($fields as $key => $field)
						$arUpdate[$field] = floatval($row[$field]);
					if($arUpdate["PRICE_PROFIT"] <= 0 && $arUpdate["PROFIT"] > 0)
						$arUpdate["PRICE_PROFIT"] = $arUpdate["PROFIT"];

					FinanceReport::updateReport($arUpdate);
					foreach($indexReports[$date][$row["ID"]] as $keyID => $reportTaskId)
					{
						Internals\ReportTasksTable::update($reportTaskId, $updateTask);
						unset($indexReports[$date][$row["ID"]][$keyID]);
					}
				}

				foreach($indexReports[$date] as $productsTasks)
					foreach($productsTasks as $reportTaskId)
						Internals\ReportTasksTable::update($reportTaskId, $updateTask);
			}
			$retFields = array();
			foreach($fields as $key => $field)
				$retFields[] = "\"".$field."\"";
			return "\Yadadya\Shopmate\FinanceReport::cron(".$store.", array(".implode(", ", $retFields)."));";
		}
	}

	public static function removeOldTasks()
	{
		$fields = array("SALE", "SALE_QUANTITY", "PURCHASE", "PURCHASE_AMOUNT", "PROFIT", "PRICE_PROFIT");
		$filterTask = array();
		foreach($fields as $key => $field)
			$filterTask[$field] = "Y";
		$result = Internals\ReportTasksTable::getList(array(
			"select" => array("ID"),
			"filter" => $filterTask,
			"order" => array("DATE")
		));
		while($row = $result->fetch())
			Internals\ReportTasksTable::delete($row["ID"]);
		return "\Yadadya\Shopmate\FinanceReport::removeOldTasks();";
	}

	function getElements(array $parameters = array())
	{

		/*$parameters["runtime"]["ELEMENT"] = new Entity\ReferenceField(
			"ELEMENT",
			"Bitrix\Iblock\Element",
			array("=ref.ID" => "this.PRODUCT_ID"),
			array("join_type" => "LEFT")
		);*/

		$parameters["runtime"][] = new Entity\ReferenceField(
			'REPORT',
			'Yadadya\Shopmate\Internals\ReportTable',
			array('=ref.PRODUCT_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);

		if(!empty($parameters["filter"]["SECTION_ID"]))
		{
			if($parameters["filter"]["INCLUDE_SUBSECTIONS"] == "Y")
			{
				$q = new Entity\Query(\Bitrix\Iblock\SectionElementTable::getEntity());
				$q->setSelect(array("IBLOCK_SECTION_ID"));
				$q->registerRuntimeField("BSubS",
					new Entity\ReferenceField(
						"BSubS",
						"Bitrix\Iblock\Section",
						array("=ref.ID" => "this.IBLOCK_SECTION_ID"),
						array("join_type" => "INNER")
					)
				);
				$q->registerRuntimeField("BS",
					new Entity\ReferenceField(
						"BS",
						"Bitrix\Iblock\Section",
						array(
							"=ref.IBLOCK_ID" => new \Bitrix\Main\DB\SqlExpression("`iblock_section_element_bsubs`.`IBLOCK_ID`"),
							"<=ref.LEFT_MARGIN" => new \Bitrix\Main\DB\SqlExpression("`iblock_section_element_bsubs`.`LEFT_MARGIN`"),
							">=ref.RIGHT_MARGIN" => new \Bitrix\Main\DB\SqlExpression("`iblock_section_element_bsubs`.`RIGHT_MARGIN`")
						),
						array("join_type" => "INNER")
					)
				);
				$q->setFilter(array("BS.ID" => $parameters["filter"]["SECTION_ID"]));
				$sectionFilter = $q->getQuery();
				$parameters["filter"]["@IBLOCK_SECTION_ID"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");
				unset($parameters["filter"]["INCLUDE_SUBSECTIONS"]);
			}
			else
			{
				$parameters["filter"]["IBLOCK_SECTION_ID"] = $parameters["filter"]["SECTION_ID"];
			}
			unset($parameters["filter"]["SECTION_ID"]);
		}

		$removeFields = array("REPORT.ID", "REPORT.DATE", "REPORT.STORE_ID");
		$summFields = array("SALE", "SALE_QUANTITY", "PURCHASE", "PURCHASE_AMOUNT", "PROFIT", "PRICE_PROFIT");

		if(empty($parameters["select"]))
			$parameters["select"] = array_merge(array_keys(Iblock\ElementTable::getEntity()->getFields()), $summFields);
		foreach($parameters["select"] as $key => $field) 
			if($field == "*" || $field == "REPORT.*")
			{
				if($field == "REPORT.*") unset($parameters["select"][$key]);
				$parameters["select"] = array_merge($parameters["select"], $summFields);
			}

		foreach($parameters["select"] as $key => $field) 
			if(in_array($field, $removeFields))
				unset($parameters["select"][$key]);
			elseif(in_array($field, $summFields))
			{
				$parameters["runtime"][$field] = new Entity\ExpressionField(
					$field, 
					"SUM(%s)",
					"REPORT.".$field
				);
			}

		return Iblock\ElementTable::getList($parameters);
	}

	function getSections(array $parameters = array())
	{

		$parameters["runtime"][] = new Entity\ReferenceField(
			'BSTEMP',
			'Bitrix\Iblock\Section',
			array('=ref.IBLOCK_ID' => 'this.IBLOCK_ID'),
			array('join_type' => 'INNER')
		);//INNER JOIN b_iblock_section BSTEMP ON BSTEMP.IBLOCK_ID = b_iblock_section.IBLOCK_ID

		$parameters["runtime"][] = new Entity\ReferenceField(
			'BSE',
			'Bitrix\Iblock\SectionElement',
			array('=ref.IBLOCK_SECTION_ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`ID`')),
			array('join_type' => 'LEFT')
		);//LEFT JOIN b_iblock_section_element BSE ON BSE.IBLOCK_SECTION_ID=BSTEMP.ID 

		$parameters["runtime"][] = new Entity\ReferenceField(
			'BE',
			'Bitrix\Iblock\Element',
			array(
				'=ref.ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bse`.`IBLOCK_ELEMENT_ID`'),
				'=ref.IBLOCK_ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section`.`IBLOCK_ID`'),
			),
			array('join_type' => 'LEFT')
		);//LEFT JOIN b_iblock_element BE ON (BSE.IBLOCK_ELEMENT_ID=BE.ID
			//AND ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL )
			//AND BE.IBLOCK_ID = b_iblock_section.IBLOCK_ID

		$parameters["runtime"][] = new Entity\ReferenceField(
			'REPORT',
			'Yadadya\Shopmate\Internals\ReportTable',
			array(
				'=ref.PRODUCT_ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section_be`.`ID`'),
			),
			array('join_type' => 'LEFT')
		);

		$parameters["filter"][] = array(
			"LOGIC" => "AND",
			"IBLOCK_ID" => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`IBLOCK_ID`'),
			"<=LEFT_MARGIN" => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`LEFT_MARGIN`'),
			">=RIGHT_MARGIN" => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`RIGHT_MARGIN`'),
		);//BSTEMP.IBLOCK_ID = iblock_section.IBLOCK_ID
			//AND BSTEMP.LEFT_MARGIN >= iblock_section.LEFT_MARGIN
			//AND BSTEMP.RIGHT_MARGIN <= iblock_section.RIGHT_MARGIN

		$removeFields = array("REPORT.ID", "REPORT.DATE", "REPORT.STORE_ID");
		$summFields = array("SALE", "SALE_QUANTITY", "PURCHASE", "PURCHASE_AMOUNT", "PROFIT", "PRICE_PROFIT");

		if(empty($parameters["select"]))
			$parameters["select"] = array_merge(array_keys(Iblock\SectionTable::getEntity()->getFields()), $summFields);
		foreach($parameters["select"] as $key => $field) 
			if($field == "*" || $field == "REPORT.*")
			{
				if($field == "REPORT.*") unset($parameters["select"][$key]);
				$parameters["select"] = array_merge($parameters["select"], $summFields);
			}

		foreach($parameters["select"] as $key => $field) 
			if(in_array($field, $removeFields))
				unset($parameters["select"][$key]);
			elseif(in_array($field, $summFields))
			{
				$parameters["runtime"][$field] = new Entity\ExpressionField(
					$field, 
					"SUM(%s)",
					"REPORT.".$field
				);
			}

		return Iblock\SectionTable::getList($parameters);
	}

	function calcElements(array $parameters = array())
	{
		if(!empty($parameters["select"]))
			foreach($parameters["select"] as $key => $field)
				switch ($field) 
				{
					case "SALE":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * b_sale_basket.PRICE)
								FROM b_sale_basket, b_sale_order
								WHERE 
									b_sale_basket.ORDER_ID = b_sale_order.ID AND
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;

					case "SALE_QUANTITY":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY)
								FROM b_sale_basket, b_sale_order
								WHERE 
									b_sale_basket.ORDER_ID = b_sale_order.ID AND
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;

					/*case "SALE_PREV":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_FROM"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_FROM"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_TO"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_TO"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * b_sale_basket.PRICE)
								FROM b_sale_basket, b_sale_order
								WHERE 
									b_sale_basket.ORDER_ID = b_sale_order.ID AND
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;*/

					case "PURCHASE":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_catalog_docs_element.STORE_TO IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}

						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_catalog_docs_element.AMOUNT * b_catalog_docs_element.PURCHASING_PRICE)
								FROM b_catalog_docs_element, b_catalog_store_docs
								WHERE 
									b_catalog_docs_element.DOC_ID = b_catalog_store_docs.ID AND
									b_catalog_store_docs.DOC_TYPE = 'A' AND
									b_catalog_store_docs.STATUS = 'Y' AND
									b_catalog_docs_element.ELEMENT_ID = %s".$filter.")",
							"ID"
						);
						break;

					case "PURCHASE_AMOUNT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_catalog_docs_element.STORE_TO IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}

						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_catalog_docs_element.AMOUNT)
								FROM b_catalog_docs_element, b_catalog_store_docs
								WHERE 
									b_catalog_docs_element.DOC_ID = b_catalog_store_docs.ID AND
									b_catalog_store_docs.DOC_TYPE = 'A' AND
									b_catalog_store_docs.STATUS = 'Y' AND
									b_catalog_docs_element.ELEMENT_ID = %s".$filter.")",
							"ID"
						);
						break;

					/*case "PURCHASE_PREV":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_FROM"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_FROM"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_TO"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_TO"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_catalog_docs_element.STORE_TO IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}

						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_catalog_docs_element.AMOUNT * b_catalog_docs_element.PURCHASING_PRICE)
								FROM b_catalog_docs_element, b_catalog_store_docs
								WHERE 
									b_catalog_docs_element.DOC_ID = b_catalog_store_docs.ID AND
									b_catalog_store_docs.DOC_TYPE = 'A' AND
									b_catalog_store_docs.STATUS = 'Y' AND
									b_catalog_docs_element.ELEMENT_ID = %s".$filter.")",
							"ID"
						);
						break;*/

					/*case "PROFIT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(b_sale_basket.PRICE, 0) - IFNULL(b_sm_finance_prod_price_log.PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								LEFT JOIN b_sm_finance_prod_price_log 
									ON b_sm_finance_prod_price_log.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID = 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1)
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;*/

					case "PROFIT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(b_sale_basket.PRICE, 0) - IFNULL(b_sm_basket.PURCHASING_PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sm_basket ON b_sm_basket.ID=b_sale_basket.ID
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;

					/*case "PROFIT_PREV":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_FROM"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_FROM"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_TO"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_TO"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(b_sale_basket.PRICE, 0) - IFNULL(b_sm_finance_prod_price_log.PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								LEFT JOIN b_sm_finance_prod_price_log 
									ON b_sm_finance_prod_price_log.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID = 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1)
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;*/

					/*case "PRICE_PROFIT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(sale.PRICE, 0) - IFNULL(purchase.PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								LEFT JOIN b_sm_finance_prod_price_log purchase
									ON purchase.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID = 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1)
								LEFT JOIN b_sm_finance_prod_price_log sale
									ON sale.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID > 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1)
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;*/

					case "PRICE_PROFIT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(b_sale_basket.PRICE, 0) - IFNULL(b_sm_basket.PURCHASING_PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sm_basket ON b_sm_basket.ID=b_sale_basket.ID
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;

					/*case "PRICE_PROFIT_PREV":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_FROM"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_FROM"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$timestamp = strtotime($parameters["filter"]["DATE_TO"]);
							$date = (date("Y", strtotime($parameters["filter"]["DATE_TO"]))-1).date("-m-d 00:00:00", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"(SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(sale.PRICE, 0) - IFNULL(purchase.PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								LEFT JOIN b_sm_finance_prod_price_log purchase
									ON b_sm_finance_prod_price_log.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID = 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1)
								LEFT JOIN b_sm_finance_prod_price_log sale
									ON sale.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID > 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1)
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = %s".$filter.")",
							"ID"
						);
						break;*/
				}

		if(!empty($parameters["filter"]))
			foreach($parameters["filter"] as $field => $value)
				if($field === "SECTION_ID" && !empty($value)) 
				{
					if($parameters["filter"]["INCLUDE_SUBSECTIONS"] == "Y")
					{
						$parameters["filter"]["@IBLOCK_SECTION_ID"] = new \Bitrix\Main\DB\SqlExpression("(SELECT BSE.IBLOCK_SECTION_ID
							FROM b_iblock_section_element BSE
							INNER JOIN b_iblock_section BSubS ON BSE.IBLOCK_SECTION_ID = BSubS.ID
							INNER JOIN b_iblock_section BS ON (BSubS.IBLOCK_ID=BS.IBLOCK_ID
								AND BSubS.LEFT_MARGIN>=BS.LEFT_MARGIN
								AND BSubS.RIGHT_MARGIN<=BS.RIGHT_MARGIN)
							WHERE ((BS.ID IN (".(is_array($value) ? implode($value, ",") : $value)."))))");
					}
					else
					{
						$parameters["filter"]["IBLOCK_SECTION_ID"] = $value;
					}
				}

		foreach(array("DATE_FROM", "DATE_TO", "SECTION_ID", "INCLUDE_SUBSECTIONS", "STORE_ID") as $field) 
			unset($parameters["filter"][$field]);

		return Iblock\ElementTable::getList($parameters);
	}

	function calcSections(array $parameters = array())
	{
		if(!empty($parameters["select"]))
			foreach($parameters["select"] as $key => $field)
				switch ($field) 
				{
					case "SALE":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"SUM((SELECT SUM(b_sale_basket.QUANTITY * b_sale_basket.PRICE)
								FROM b_sale_basket, b_sale_order
								WHERE 
									b_sale_basket.ORDER_ID = b_sale_order.ID AND
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = iblock_section_be.ID".$filter."))"
						);
						break;

					case "PURCHASE":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_catalog_store_docs.DATE_DOCUMENT <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_catalog_docs_element.STORE_TO IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}

						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"SUM((SELECT SUM(b_catalog_docs_element.AMOUNT * b_catalog_docs_element.PURCHASING_PRICE)
								FROM b_catalog_docs_element, b_catalog_store_docs
								WHERE 
									b_catalog_docs_element.DOC_ID = b_catalog_store_docs.ID AND
									b_catalog_store_docs.DOC_TYPE = 'A' AND
									b_catalog_store_docs.STATUS = 'Y' AND
									b_catalog_docs_element.ELEMENT_ID = iblock_section_be.ID".$filter."))"
						);
						break;

					case "PROFIT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"SUM((SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(b_sale_basket.PRICE, 0) - IFNULL(b_sm_finance_prod_price_log.PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								LEFT JOIN b_sm_finance_prod_price_log 
									ON b_sm_finance_prod_price_log.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID = 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1) OR NULL
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = iblock_section_be.ID".$filter."))"
						);
						break;

					case "PRICE_PROFIT":
						$filter = "";
						if(!empty($parameters["filter"]["DATE_FROM"]))
						{
							$date = date("Y-m-d 00:00:00", strtotime($parameters["filter"]["DATE_FROM"]));
							$filter .= " AND b_sale_order.DATE_PAYED >= '".$date."'";
						}
						if(!empty($parameters["filter"]["DATE_TO"]))
						{
							$date = date("Y-m-d 59:59:59", strtotime($parameters["filter"]["DATE_TO"]));
							$filter .= " AND b_sale_order.DATE_PAYED <= '".$date."'";
						}
						if(!empty($parameters["filter"]["STORE_ID"]))
						{
							$filter .= " AND b_sale_order.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")";
						}
						$parameters["runtime"][$key] = new Entity\ExpressionField(
							$field, 
							"SUM((SELECT SUM(b_sale_basket.QUANTITY * (IFNULL(sale.PRICE, 0) - IFNULL(purchase.PRICE, 0)))
								FROM b_sale_basket
								LEFT JOIN b_sale_order ON b_sale_order.ID=b_sale_basket.ORDER_ID
								LEFT JOIN b_sm_finance_prod_price_log purchase
									ON purchase.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID = 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1) OR NULL
								LEFT JOIN b_sm_finance_prod_price_log sale
									ON sale.ID = (SELECT ID 
										FROM b_sm_finance_prod_price_log 
										WHERE 
											b_sale_order.DATE_PAYED >= b_sm_finance_prod_price_log.TIMESTAMP_X 
											AND b_sale_basket.PRODUCT_ID = b_sm_finance_prod_price_log.PRODUCT_ID
											AND b_sm_finance_prod_price_log.CATALOG_GROUP_ID > 0
											AND b_sm_finance_prod_price_log.PRICE > 0
											".(!empty($parameters["filter"]["STORE_ID"]) ? " AND b_sm_finance_prod_price_log.STORE_ID IN (".implode(",", (array) $parameters["filter"]["STORE_ID"]).")" : "")."
										ORDER BY TIMESTAMP_X DESC
										LIMIT 1) OR NULL
								WHERE 
									b_sale_order.PAYED = 'Y' AND
									b_sale_order.CANCELED = 'N' AND
									b_sale_basket.PRODUCT_ID = iblock_section_be.ID".$filter."))"
						);
						break;
				}

		foreach(array("DATE_FROM", "DATE_TO", "SECTION_ID", "INCLUDE_SUBSECTIONS", "STORE_ID") as $field) 
			unset($parameters["filter"][$field]);

		$parameters["runtime"][] = new Entity\ReferenceField(
			'BSTEMP',
			'Bitrix\Iblock\Section',
			array('=ref.IBLOCK_ID' => 'this.IBLOCK_ID'),
			array('join_type' => 'INNER')
		);//INNER JOIN b_iblock_section BSTEMP ON BSTEMP.IBLOCK_ID = b_iblock_section.IBLOCK_ID

		$parameters["runtime"][] = new Entity\ReferenceField(
			'BSE',
			'Bitrix\Iblock\SectionElementTable',
			array('=ref.IBLOCK_SECTION_ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`ID`')),
			array('join_type' => 'LEFT')
		);//LEFT JOIN b_iblock_section_element BSE ON BSE.IBLOCK_SECTION_ID=BSTEMP.ID 

		$parameters["runtime"][] = new Entity\ReferenceField(
			'BE',
			'Bitrix\Iblock\ElementTable',
			array(
				'=ref.ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bse`.`IBLOCK_ELEMENT_ID`'),
				'=ref.IBLOCK_ID' => new \Bitrix\Main\DB\SqlExpression('`iblock_section`.`IBLOCK_ID`'),
			),
			array('join_type' => 'LEFT')
		);//LEFT JOIN b_iblock_element BE ON (BSE.IBLOCK_ELEMENT_ID=BE.ID
			//AND ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL )
			//AND BE.IBLOCK_ID = b_iblock_section.IBLOCK_ID

		$parameters["filter"][] = array(
			"LOGIC" => "AND",
			"IBLOCK_ID" => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`IBLOCK_ID`'),
			"<=LEFT_MARGIN" => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`LEFT_MARGIN`'),
			">=RIGHT_MARGIN" => new \Bitrix\Main\DB\SqlExpression('`iblock_section_bstemp`.`RIGHT_MARGIN`'),
		);//BSTEMP.IBLOCK_ID = iblock_section.IBLOCK_ID
			//AND BSTEMP.LEFT_MARGIN >= iblock_section.LEFT_MARGIN
			//AND BSTEMP.RIGHT_MARGIN <= iblock_section.RIGHT_MARGIN

		return Iblock\SectionTable::getList($parameters);
	}

	/* ProdAmountTable */
	public static function getProdAmount(array $parameters = array())
	{
		return Internals\ProdAmountTable::getList($parameters);
	}

	public static function updateProdAmount($arFields)
	{
		if(!empty($arFields["PRODUCT_ID"]) && !empty($arFields["STORE_ID"]) && (!empty($arFields["QUANTITY"]) || !empty($arFields["AMOUNT"])))
		{
			global $USER;
			$addFields = array(
				"DATE" => is_object($arFields["DATE"]) ? $arFields["DATE"] : new Type\DateTime($arFields["DATE"] ? date("d.m.Y H:i:s", strtotime($arFields["DATE"])) : date("d.m.Y H:i:s", time()), "d.m.Y H:i:s"),
				"STORE_ID" => $arFields["STORE_ID"],
				"PRODUCT_ID" => $arFields["PRODUCT_ID"],
				"AMOUNT" => $arFields["AMOUNT"],
				"USER_ID" => isset($arFields["USER_ID"]) ? $arFields["USER_ID"] : (is_object($USER) && ($USER->GetID() > 0) ? $USER->GetID() : false),
				"SITE_ID" => isset($arFields["SITE_ID"]) ? $arFields["SITE_ID"] : (defined("ADMIN_SECTION") && ADMIN_SECTION==true ? false : SITE_ID),
				"REQUEST_URI" => preg_replace("/(&?sessid=[0-9a-z]+)/", "", $_SERVER["REQUEST_URI"]),
				"DESCRIPTION" => $arFields["DESCRIPTION"] ? $arFields["DESCRIPTION"] : "",
			);

			if(!empty($arFields["AMOUNT"]))
			{
				$res = Internals\ProdAmountTable::add($addFields);
			}
			elseif(!empty($arFields["QUANTITY"]) && empty($arFields["DATE"]))
			{
				$rsAmount = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arFields["PRODUCT_ID"], "STORE_ID" => $arFields["STORE_ID"]), false, false, array("PRODUCT_ID", "STORE_ID", "AMOUNT"));
				if($arAmount = $rsAmount->Fetch())
					$addFields["AMOUNT"] = $arFields["QUANTITY"] - $arAmount["AMOUNT"];
				else
					$addFields["AMOUNT"] = $arFields["QUANTITY"];
				$res = Internals\ProdAmountTable::add($addFields);
			}
		}
		return $res;
	}
	/* !ProdAmountTable */

	/* ProdPriceTable */
	public static function getProdPrice(array $parameters = array())
	{
		return Internals\ProdPriceTable::getList($parameters);
	}

	public static function updateProdPrice($arFields)
	{
		if(!empty($arFields["PRODUCT_ID"]) && !empty($arFields["STORE_ID"]) && $arFields["PRICE"] > 0)
		{
			global $USER;
			$addFields = array(
				"DATE" => is_object($arFields["DATE"]) ? $arFields["DATE"] : new Type\DateTime($arFields["DATE"] ? date("d.m.Y H:i:s", strtotime($arFields["DATE"])) : date("d.m.Y H:i:s", time()), "d.m.Y H:i:s"),
				"STORE_ID" => $arFields["STORE_ID"],
				"PRODUCT_ID" => $arFields["PRODUCT_ID"],
				"CATALOG_GROUP_ID" => intval($arFields["CATALOG_GROUP_ID"]),
				"PRICE" => $arFields["PRICE"],
				"CURRENCY" => $arFields["CURRENCY"] ? $arFields["CURRENCY"] : "RUB",
				"USER_ID" => isset($arFields["USER_ID"]) ? $arFields["USER_ID"] : (is_object($USER) && ($USER->GetID() > 0) ? $USER->GetID() : false),
				"SITE_ID" => isset($arFields["SITE_ID"]) ? $arFields["SITE_ID"] : (defined("ADMIN_SECTION") && ADMIN_SECTION==true ? false : SITE_ID),
				"REQUEST_URI" => preg_replace("/(&?sessid=[0-9a-z]+)/", "", $_SERVER["REQUEST_URI"]),
				"DESCRIPTION" => $arFields["DESCRIPTION"] ? $arFields["DESCRIPTION"] : "",
			);

			$result = Internals\ProdPriceTable::GetList(array(
				"select" => array("PRICE"),
				"filter" => array(
					"<=DATE" => $addFields["DATE"],
					"STORE_ID" => $addFields["STORE_ID"],
					"PRODUCT_ID" => $addFields["PRODUCT_ID"],
					"CATALOG_GROUP_ID" => $addFields["CATALOG_GROUP_ID"],
					"CURRENCY" => $addFields["CURRENCY"],
				),
				"order" => array("DATE" => "DESC"),
				"limit" => 1
			));
			if($row = $result->fetch())
			{
				if($addFields["PRICE"] != $row["PRICE"])
					$res = Internals\ProdPriceTable::add($addFields);
			}
			else
				$res = Internals\ProdPriceTable::add($addFields);
		}
		return $res;
	}
	/* !ProdPriceTable */

	/* ReportTasks */
	public static function addTask(array $data = array())
	{
		if(!empty($data["DATE"]) && !empty($data["PRODUCT_ID"]) && !empty($data["STORE_ID"]))
		{
			$data["DATE"] = new Type\DateTime(date("Y-m-d", strtotime($data["DATE"])), "Y-m-d");
			$result = Internals\ReportTasksTable::getList(array(
				"select" => array("ID"),
				"filter" => array(
					"DATE" => $data["DATE"],
					"PRODUCT_ID" => $data["PRODUCT_ID"],
					"STORE_ID" => $data["STORE_ID"],
				)
			));
			$data = array_merge($data, array(
				"SALE" => "N",
				"SALE_QUANTITY" => "N",
				"PURCHASE" => "N",
				"PURCHASE_AMOUNT" => "N",
				"PROFIT" => "N",
				"PRICE_PROFIT" => "N",
			));
			if($row = $result->fetch())
				Internals\ReportTasksTable::update($row["ID"], $data);
			else
				Internals\ReportTasksTable::add($data);
		}
	}
	/* !ReportTasks */
}
