<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Bitrix\Iblock;
use Bitrix\Catalog;
use Yadadya\Shopmate;
use Yadadya\Shopmate\BitrixInternals;
use Yadadya\Shopmate\Internals;

Loc::loadMessages(__FILE__);

class Overhead extends Base
{
	protected static $currentFields = array("ID", "DOC_TYPE", "NUMBER_DOCUMENT", "PRODUCTS_COUNT", "TOTAL_SUMM", "DATE_DOCUMENT", /*"TOTAL_FACT", "TOTAL", */"TOTAL_PERCENT"/*, "END_DATE"*/);
	protected static $currentSort = array("DATE_DOCUMENT" => "DESC");
	protected static $filterList = array(
		"CONTRACTOR" => array(
		),
		"PRODUCT" => array(
		),
		"DATE_FROM" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"DATE_TO" => array(
			"PROPERTY_TYPE" => "S",
			"USER_TYPE" => "Date"
		),
		"PERISHABLE" => array(
			"PROPERTY_TYPE" => "L",
			"MULTIPLE" => "N",
			"LIST_TYPE" => "C",
			"USER_TYPE" => "checkbox",
			"ENUM" => array(1 => "perishable"),
			"STYLE" => array("display" => "inline", "width" => "auto")
		),
		"EXPIRATION" => array(
			"PROPERTY_TYPE" => "L",
			"MULTIPLE" => "N",
			"LIST_TYPE" => "C",
			"USER_TYPE" => "checkbox",
			"ENUM" => array(1 => "expiration"),
			"STYLE" => array("display" => "inline", "width" => "auto")
		)
	);
	protected static $propList = array(
		"NUMBER_DOCUMENT" => Array(
			"REQUIRED" => "Y"
		),
		"DATE_DOCUMENT" => Array(
			"REQUIRED" => "Y",
			"USER_TYPE" => "Date",
			"VERIFICATION" => "date"
		),
		"CONTRACTOR_ID" => Array(
			"REF_ENTITY" => "\\Yadadya\\Shopmate\\Components\\Contractor"
		),
		"ELEMENT" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"NUM_LIST" => "Y",
			"PROPERTY_LIST" => array(
				"ELEMENT_ID" => array(
					"REF_ENTITY" => "\\Yadadya\\Shopmate\\Components\\Products"
				),
				"PURCHASING_PRICE" => array(
					"DISABLED" => "Y",
				),
				"MEASURE" => array(
					"PLACEHOLDER" => "",
					"PROPERTY_TYPE" => "L",
					"DISABLED" => "Y",
				),
				"DOC_AMOUNT" => array(
					"VERIFICATION" => "float"
				),
				"AMOUNT" => array(
					"VERIFICATION" => "float"
				),
				"SHOP_PRICE" => array(
					"VERIFICATION" => "float"
				),
				"PURCHASING_NDS" => array(
					"PROPERTY_TYPE" => "L",
				),
				"PURCHASING_SUMM" => array(
					"VERIFICATION" => "float"
				),
				"NDS_VALUE" => array(
					"DISABLED" => "Y",
				),
				"START_DATE" => array(
					"USER_TYPE" => "DateTime",
					"VERIFICATION" => "datetime"
				),
				"ID" => array(
					"PROPERTY_TYPE" => "H",
				),
			),
		),
		"TOTAL_SUMM" => Array(
			"READONLY" => "Y",
			"PLACEHOLDER" => "0.0",
			"VERIFICATION" => "float"
		),
		"USER_ID" => [
			"PROPERTY_TYPE" => "L", 
			"LIST_TYPE" => "AJAX",
			"REF_ENTITY" => "\\Yadadya\\Shopmate\\Components\\Personal",
			"DATA" => [], 
		],
		"TOTAL_FACT" => Array(
		),
		"DOC_TYPE" => Array(
			"PROPERTY_TYPE" => "H",
			"DEFAULT_VALUE" => "A"
		)
	);
	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["ELEMENT"]["PROPERTY_LIST"]["MEASURE"]["ENUM"] = Products::getMeasureEnumList();
		$propList["ELEMENT"]["PROPERTY_LIST"]["PURCHASING_NDS"]["ENUM"] = Products::getVatEnumList();

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("overhead");
	}*/

	public function getList(array $parameters = array())
	{
		//join NUMBER_DOCUMENT
		$parameters["runtime"][] = new Entity\ReferenceField(
			'SMSTORE_DOCS',
			'Yadadya\Shopmate\Internals\StoreDocs',
			array('=ref.DOC_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);

		$parameters["filter"]["SMSTORE_DOCS.STORE_ID"] = Shopmate\Shops::getUserShop();
		//$parameters["filter"]["DOC_TYPE"] = "A"; 
		//A - Приход товара на склад
		//M - Перемещение товара между складами
		//R - Возврат товара
		//D - Списание товара
		//U - Отмена резервирования

		$arDocElemFilter = array("DOC_ID" => new \Bitrix\Main\DB\SqlExpression("`yadadya_shopmate_bitrixinternals_store_docs`.`ID`"));

		$referenceFields = array(
			"NUMBER_DOCUMENT" => "SMSTORE_DOCS.NUMBER_DOCUMENT", 
			"TOTAL_FACT" => "SMSTORE_DOCS.TOTAL_FACT", 
			"PRODUCTS_COUNT" => new Entity\ExpressionField("PRODUCTS_COUNT", "COUNT(%s)", array('Yadadya\Shopmate\BitrixInternals\StoreDocsElement:DOC.AMOUNT')), 
			/*"TOTAL_SUMM" => new Entity\ExpressionField("TOTAL_SUMM", "SUM(%s * %s)", array('Yadadya\Shopmate\BitrixInternals\StoreDocsElement:DOC.AMOUNT', 'Yadadya\Shopmate\BitrixInternals\StoreDocsElement:DOC.SMDOCS_ELEMENT.SHOP_PRICE')),*/ 
			"TOTAL_SUMM" => "TOTAL", 
			"END_DATE" => new Entity\ExpressionField("END_DATE", "MIN(IFNULL(%s, 'none'))", array('Yadadya\Shopmate\BitrixInternals\StoreDocsElement:DOC.SMDOCS_ELEMENT.END_DATE')),
			"TOTAL_PERCENT" => new Entity\ExpressionField("TOTAL_PERCENT", "CONCAT((%s * 100 / %s), '%%')", array('SMSTORE_DOCS.TOTAL_FACT', 'TOTAL')),
			"FULL_SUMM" => new Entity\ExpressionField("FULL_SUMM", "SUM(%s)", array('TOTAL')),
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		if (is_array($parameters["order"]) && !isset($parameters["order"]["ID"]))
			$parameters["order"]["ID"] = "DESC";

		$parameters["count_total"] = false;

		return BitrixInternals\StoreDocsTable::getList($parameters);
	}

	public function resultModifier($arResult)
	{
		if (is_array($arResult["ITEMS"]))
		{
			foreach ($arResult["ITEMS"] as &$arItem) 
				$arItem["DOC_TYPE"] = Loc::getMessage("DOC_TYPE_".$arItem["DOC_TYPE"]);
		}
		elseif (is_array($arResult["BUTTONS"]))
		{
			$arResult["PROPERTY_LIST"]["submit"] = ["PROPERTY_TYPE" => "H", "DEFAULT_VALUE" => "Y"];
			//$arResult["ITEM"]["submit"] = "Y";
			$cancel_btn = [];
			foreach ($arResult["BUTTONS"] as $key => $value)
				if ($value["NAME"] == "cancel")
				{
					$cancel_btn = $value;
					break;
				}

			$arResult["BUTTONS"] = [];

			if (empty($arResult["ITEM"]["ID"]) && $arResult["ITEM"]["DOC_TYPE"] != "R" || $arResult["ITEM"]["DOC_TYPE"] == "A")
				$arResult["BUTTONS"][] = 
				[
					"NAME" => "DOC_TYPE[A]",
					"VALUE" => Loc::getMessage("BUTTON_SAVE"),
					"CLASS" => "btn-primary"
				];

			if ($arResult["ITEM"]["DOC_TYPE"] == "A")
				$arResult["BUTTONS"][] = 
				[
					"NAME" => "DOC_TYPE[D]",
						"VALUE" => Loc::getMessage("BUTTON_SAVE_D"),
						"CLASS" => "btn-warning"
				];

			if (empty($arResult["ITEM"]["ID"]) && $arResult["ITEM"]["DOC_TYPE"] == "R")
				$arResult["BUTTONS"][] = 
				[
					"NAME" => "DOC_TYPE[R]",
					"VALUE" => Loc::getMessage("BUTTON_SAVE_R"),
					"CLASS" => "btn-primary"
				];

			$arResult["BUTTONS"][] = $cancel_btn;
		}
		if (!empty($arResult["ITEM"]["ID"]))
		{
				$arResult["PROPERTY_LIST"]["USER_ID"]["REQUIRED"] = "Y";
		}
		return $arResult;
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		foreach($filter as $field => $value) 
			if(!empty($value))
			{
				switch($field) 
				{
					case "PRODUCT":
					case "PERISHABLE":
					case "EXPIRATION":

						break;

					case "DATE_FROM":

						$arFilter[] = parent::getDateFilter("DATE_DOCUMENT", $value);

						break;

					case "DATE_TO":

						$arFilter[] = parent::getDateFilter("DATE_DOCUMENT", null, $value);

						break;

					case "CONTRACTOR":

						$arFilter["CONTRACTOR_ID"] = $value;

						break;

					default:

						$arFilter[$field] = $value;

						break;
				}
		}

		if(!empty($filter["PRODUCT"]) || !empty($filter["PERISHABLE"]) || !empty($filter["EXPIRATION"]))
		{
			$q = new Entity\Query(BitrixInternals\StoreDocsElementTable::getEntity());
			$q->setSelect(array("DOC_ID"));
			$q->registerRuntimeField("SMDOC_ELEMENT", new Entity\ReferenceField(
				'SMDOC_ELEMENT',
				'Yadadya\Shopmate\Internals\StoreDocsElement',
				array('=ref.DOCS_ELEMENT_ID' => 'this.ID'),
				array('join_type' => 'LEFT')
			));
			if(!empty($filter["PRODUCT"])) $q->addFilter(null, array("ELEMENT_ID" => $filter["PRODUCT"]));
			if(!empty($filter["PERISHABLE"])) $q->addFilter(null, array("!SMDOC_ELEMENT.END_DATE" => false));
			if(!empty($filter["EXPIRATION"])) $q->addFilter(null, array("<SMDOC_ELEMENT.END_DATE" => new \Bitrix\Main\Type\DateTime()));
			$arFilter[]["@ID"] = new \Bitrix\Main\DB\SqlExpression("(".$q->getQuery().")");
		}

		return $arFilter;
	}

	public function getByID($primary = 0)
	{
		$result = BitrixInternals\StoreDocsTable::getList(array(
			"select" => array("ID", "DOC_TYPE", "NUMBER_DOCUMENT" => "SMSTORE_DOCS.NUMBER_DOCUMENT", "DATE_DOCUMENT", "CONTRACTOR_ID", "TOTAL_SUMM" => "TOTAL", "TOTAL_FACT" => "SMSTORE_DOCS.TOTAL_FACT", "STORE_ID" => "SMSTORE_DOCS.STORE_ID", "USER_ID" => "SMSTORE_DOCS.USER_ID"),
			"filter" => array("ID" => $primary),
			"runtime" => array(
				new Entity\ReferenceField(
					'SMSTORE_DOCS',
					'Yadadya\Shopmate\Internals\StoreDocs',
					array('=ref.DOC_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				)
			)
		))->fetch();

		$result["ELEMENT"] = array();
		$res = BitrixInternals\StoreDocsElementTable::getList(array(
			"select" => array("ID", "ELEMENT_ID", "PURCHASING_PRICE", "MEASURE" => "PRODUCT.MEASURE", "AMOUNT", "SHOP_PRICE" => "SMDOCS_ELEMENT.SHOP_PRICE", "DOC_AMOUNT" => "SMDOCS_ELEMENT.DOC_AMOUNT", "PURCHASING_NDS" => "SMDOCS_ELEMENT.PURCHASING_NDS", "PURCHASING_SUMM" => new Entity\ExpressionField("PURCHASING_SUMM", "ROUND(%s * %s * (%s + 100) / 100, 2)", array("PURCHASING_PRICE", "SMDOCS_ELEMENT.DOC_AMOUNT", "SMDOCS_ELEMENT.PURCHASING_NDS")), "NDS_VALUE" => new Entity\ExpressionField("NDS_VALUE", "%s * %s / 100", array("PURCHASING_SUMM", "SMDOCS_ELEMENT.PURCHASING_NDS")), "START_DATE" => new Entity\ExpressionField("START_DATE", "%s - INTERVAL %s DAY", array("SMDOCS_ELEMENT.END_DATE", "SMPRODUCT.SHELF_LIFE")), "STORE_TO"),
			"filter" => array("DOC_ID" => $primary),
			"runtime" => array(
				new Entity\ReferenceField(
					'SMDOCS_ELEMENT',
					'Yadadya\Shopmate\Internals\StoreDocsElement',
					array('=ref.DOCS_ELEMENT_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				),
				new Entity\ReferenceField(
					'PRODUCT',
					'Bitrix\Catalog\ProductTable',
					array('=ref.ID' => 'this.ELEMENT_ID'),
					array('join_type' => 'LEFT')
				),
				new Entity\ReferenceField(
					'SMPRODUCT',
					'Yadadya\Shopmate\Internals\ProductTable',
					array('=ref.PRODUCT_ID' => 'this.ELEMENT_ID'),
					array('join_type' => 'LEFT')
				)
			)
		));
		while ($row = $res->fetch())
		{
			$row["PURCHASING_PRICE"] = round($row["PURCHASING_PRICE"], 2);
			if ($result["DOC_TYPE"] == "R")
			{
				$row["AMOUNT"] *= -1;
				$row["SHOP_PRICE"] *= -1;
				$row["DOC_AMOUNT"] *= -1;
				$row["PURCHASING_SUMM"] *= -1;
				$row["NDS_VALUE"] *= -1;
			}
			$result["ELEMENT"][] = $row;
		}

		$total = 0;
		foreach ($result["ELEMENT"] as $arElement)
			$total += $arElement["PURCHASING_SUMM"];

		if($total != $result["TOTAL_SUMM"])
		{
			$result["TOTAL_SUMM"] = $total;
			BitrixInternals\StoreDocsTable::update($primary, array("TOTAL" => $total));
		}
		return $result;
	}

	public function add(array $data)
	{
		$result = parent::add($data);

		if($result->isSuccess())
		{
			self::update(0, $data, $result);
		}

		return $result;
	}

	public function update($primary, array $data, $result = null)
	{
		global $USER;
		$data["ELEMENT"] = (array) $data["ELEMENT"];
		if (!empty($data["DOC_TYPE"]) && is_array($data["DOC_TYPE"]))
		{
			reset($data["DOC_TYPE"]);
			$data["DOC_TYPE"] = key($data["DOC_TYPE"]);
		}
		if ($data["DOC_TYPE"] == "R")
		{
			foreach ($data["ELEMENT"] as &$arElement) 
			{
				$arElement["AMOUNT"] *= -1;
				$arElement["SHOP_PRICE"] *= -1;
				$arElement["DOC_AMOUNT"] *= -1;
				$arElement["PURCHASING_NDS"] *= -1;
				$arElement["PURCHASING_SUMM"] *= -1;
			}
		}
		if(!($result instanceof \Bitrix\Main\Entity\AddResult)) 
		{
			if(is_object($this)) 
			{
				$this->result->setData(array("ID" => $primary));
				self::prepareResult();
			}
			$result = parent::update($primary, $data);
		}
		if($result->isSuccess())
		{
			foreach ($data["ELEMENT"] as $key => $val) 
				if (empty($val["ELEMENT_ID"]))
					unset($data["ELEMENT"][$key]);
			$tableFields = array(
				"DOC" => array("DATE_DOCUMENT", "CONTRACTOR_ID", "TOTAL_SUMM", "DOC_TYPE"),
				"SMDOC" => array("NUMBER_DOCUMENT", "TOTAL_FACT", "USER_ID"),
				"DOC_ELEMENT" => array("ELEMENT")
			);

			foreach ($tableFields as $table => $fields) 
			{
				$tData = array();
				foreach ($fields as $field) 
					if(isset($data[$field]))
						$tData[$field] = $data[$field];

				if(!empty($tData))
					switch ($table) 
					{
						case "DOC":

							$tData["TOTAL"] = $tData["TOTAL_SUMM"];
							unset($tData["TOTAL_SUMM"]);
							$total = 0;
							if(!empty($data["ELEMENT"]))
								foreach($data["ELEMENT"] as $arElement)
									if($arElement["ELEMENT_ID"] > 0)
										$total += $arElement["PURCHASING_SUMM"];
							if($tData["TOTAL"] != $total)
								$tData["TOTAL"] = $total;
							$tData["DATE_DOCUMENT"] = new \Bitrix\Main\Type\DateTime($tData["DATE_DOCUMENT"]);

							if($primary > 0)
							{
								$tData["MODIFIED_BY"] = $USER->GetID();
								$res = BitrixInternals\StoreDocsTable::update($primary, $tData);
								if(!$res->isSuccess())
									$result->addErrors($res->getErrors());
							}
							else
							{
								$tData["SITE_ID"] = SITE_ID;
								if (empty($tData["DOC_TYPE"])) $tData["DOC_TYPE"] = "A";
								$tData["MODIFIED_BY"] = $tData["CREATED_BY"] = $USER->GetID();
								$tData["STATUS"] = "Y";
								$res = BitrixInternals\StoreDocsTable::add($tData);
								if(!$res->isSuccess())
									$result->addErrors($res->getErrors());
								else
									$primary = $res->GetID();
							}

							break;

						case "SMDOC":

							$res = Internals\StoreDocsTable::GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"DOC_ID" => $primary,
								)
							));
							if($row = $res->fetch())
							{
								$res = Internals\StoreDocsTable::update($row["ID"], $tData);
							}
							else
							{
								$tData["DOC_ID"] = $primary;
								$tData["STORE_ID"] = Shopmate\Shops::getUserShop();
								$res = Internals\StoreDocsTable::add($tData);
							}
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

							break;

						case "DOC_ELEMENT":
							if ($primary > 0)
							{
								$shelf_life_products = array();
								if(!empty($tData["ELEMENT"]))
									foreach($tData["ELEMENT"] as $arElement)
										if($arElement["ELEMENT_ID"] > 0 && !empty($arElement["START_DATE"]))
											$shelf_life_products[] = $arElement["ELEMENT_ID"];
								if(!empty($shelf_life_products))
								{
									$prod_shelf_life = array();
									$res = \Yadadya\Shopmate\Internals\ProductTable::getList(array(
										"select" => array("PRODUCT_ID", "SHELF_LIFE"),
										"filter" => array("PRODUCT_ID" => $shelf_life_products)
									));
									while ($prod = $res->fetch())
										$prod_shelf_life[$prod["PRODUCT_ID"]] = $prod["SHELF_LIFE"];
									foreach($tData["ELEMENT"] as $key => $arElement)
										if($arElement["ELEMENT_ID"] > 0 && !empty($arElement["START_DATE"]))
										{
											$tData["ELEMENT"][$key]["END_DATE"] = new \Bitrix\Main\Type\DateTime($arElement["START_DATE"]);
											$tData["ELEMENT"][$key]["END_DATE"]->add($prod_shelf_life[$arElement["ELEMENT_ID"]]." day");
											unset($tData["ELEMENT"][$key]["START_DATE"]);
										}
								}

								$last_elems = array();
								$res = BitrixInternals\StoreDocsElementTable::getList(array(
									"select" => array("ID", "SMID" => "SMDOCS_ELEMENT.ID", "PRODUCT_ID" => "ELEMENT_ID", "QUANTITY" => "AMOUNT"),
									"filter" => array("DOC_ID" => $primary),
									"runtime" => array(
										new Entity\ReferenceField(
											'SMDOCS_ELEMENT',
											'Yadadya\Shopmate\Internals\StoreDocsElement',
											array('=ref.DOCS_ELEMENT_ID' => 'this.ID'),
											array('join_type' => 'LEFT')
										)
									),
									"order" => array("ID" => "ASC")
								));
								while ($doc_elem = $res->fetch())
									$last_elems[] = $doc_elem;

								$updateQuantity = array();
								foreach ($last_elems as $last_elem) 
									$updateQuantity[] = array(
										"PRODUCT_ID" => $last_elem["PRODUCT_ID"],
										"QUANTITY" => -1 * $last_elem["QUANTITY"],
										"DATE" => $data["DATE_DOCUMENT"]
									);
								\Yadadya\Shopmate\Products::updateProductsAmount($updateQuantity);

								if ($data["DOC_TYPE"] != "D")
								{
									if(!empty($tData["ELEMENT"]))
										foreach($tData["ELEMENT"] as $arElement)
											if($arElement["ELEMENT_ID"] > 0)
											{
												$doc_elem = array(
													"ELEMENT_ID" => $arElement["ELEMENT_ID"],
													"AMOUNT" => $arElement["AMOUNT"],
													"PURCHASING_PRICE" => $arElement["PURCHASING_SUMM"] * 100 / (100 + $arElement["PURCHASING_NDS"]) / $arElement["DOC_AMOUNT"],
												);
												if($save_elem = array_shift($last_elems))
												{
													$res = BitrixInternals\StoreDocsElementTable::update($save_elem["ID"], $doc_elem);
													if(!$res->isSuccess())
														$result->addErrors($res->getErrors());
												}
												else
												{
													$doc_elem["DOC_ID"] = $primary;
													$doc_elem["STORE_TO"] = Shopmate\Shops::getUserShop();
													$res = BitrixInternals\StoreDocsElementTable::add($doc_elem);
													if(!$res->isSuccess())
														$result->addErrors($res->getErrors());
													else
														$save_elem = array("ID" => $res->GetID());
												}
												\Yadadya\Shopmate\Products::updateProductsAmount(array(
													array(
														"PRODUCT_ID" => $doc_elem["ELEMENT_ID"],
														"QUANTITY" => $doc_elem["AMOUNT"],
														"DATE" => $data["DATE_DOCUMENT"]
													)
												));

												$smdoc_elem = array(
													"DOCS_ELEMENT_ID" => $save_elem["ID"],
													"DOC_AMOUNT" => $arElement["DOC_AMOUNT"],
													"SHOP_PRICE" => $arElement["SHOP_PRICE"],
													"END_DATE" => $arElement["END_DATE"],
													"PURCHASING_NDS" => $arElement["PURCHASING_NDS"],
												);
												if($save_elem["SMID"] > 0)
												{
													$res = Internals\StoreDocsElementTable::update($save_elem["SMID"], $smdoc_elem);
													if(!$res->isSuccess())
														$result->addErrors($res->getErrors());
												}
												else
												{
													$res = Internals\StoreDocsElementTable::add($smdoc_elem);
													if(!$res->isSuccess())
														$result->addErrors($res->getErrors());
													else
														$save_elem["SMID"] = $res->GetID();
												}
											}
									foreach ($last_elems as $last_elem)
									{
										if($last_elem["ID"] > 0) BitrixInternals\StoreDocsElementTable::delete($last_elem["ID"]);
										if($last_elem["SMID"] > 0) Internals\StoreDocsElementTable::delete($last_elem["SMID"]);
									}
								}
							}

							break;
					}
			}

			if($result instanceof \Bitrix\Main\Entity\AddResult)
				$result->setId($primary);
			else
				$result->setPrimary(array($primary));
		}
		
		return $result;
	}

	public function delete($primary)
	{
		$primary = intval($primary);
		$result = parent::delete($primary);
		if(!$result->isSuccess()) return $result;

		$data = self::getByID($primary);
		$data["ELEMENT"] = array();
		$res = Internals\StoreDocsTable::GetList(array(
			"select" => array("ID"),
			"filter" => array(
				"DOC_ID" => $primary,
			)
		));
		if($row = $res->fetch())
			Internals\StoreDocsTable::delete($row["ID"]);

		$result = BitrixInternals\StoreDocsTable::delete($primary);

		return $result;
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();
		if(!empty($filter["SEARCH"]))
		{
			$q = $filter["SEARCH"];
			unset($filter["SEARCH"]);
			$filter[] = array(
				"LOGIC" => "OR",
				"DATE_DOCUMENT" => "%".$q."%",
				"NUMBER_DOCUMENT" => "%".$q."%",
			);
		}
		$filter["DOC_TYPE"] = "A";
		$result = BitrixInternals\StoreDocsTable::GetList(array(
			"select" => array("ID", "DATE_DOCUMENT", "NUMBER_DOCUMENT" => "SMSTORE_DOCS.NUMBER_DOCUMENT"),
			"filter" => $filter,
			"runtime" => array(
				new Entity\ReferenceField(
					'SMSTORE_DOCS',
					'Yadadya\Shopmate\Internals\StoreDocs',
					array('=ref.DOC_ID' => 'this.ID'),
					array('join_type' => 'LEFT')
				)
			)
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = $row["NUMBER_DOCUMENT"]." ".Loc::getMessage("FROM")." ".$row["DATE_DOCUMENT"];
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}