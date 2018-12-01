<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class FabricaPart extends Base
{
	protected static $currentFields = array("ID", "NAME", "DATE", "COMMENT");
	protected static $currentSort = array("DATE" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"PRODUCT" => array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"PROPERTY_LIST" => array(
				"FABRICA_PROD_ID" => array(
				),
				"AMOUNT" => array(
					"VERIFICATION" => "float"
				),
				"MEASURE" => array(
					"PROPERTY_TYPE" => "L",
					"READONLY" => "Y",
				),
			),
		),
		"CONNECT" => array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"PROPERTY_LIST" => array(
				"FABRICA_PROD_ID" => array(
					"PROPERTY_TYPE" => "H"
				),
				"AMOUNT" => array(
					"VERIFICATION" => "float"
				),
				"MEASURE" => array(
					"PROPERTY_TYPE" => "L",
					"READONLY" => "Y",
				),
			),
		),
		"COMMENT" => array(
		)
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$measureEnum = Products::getMeasureEnumList();
		foreach ($measureEnum as $measureId => $measureValue) 
			if ($measureValue["DEFAULT"] == "Y")
			{
				$measureDefault = $measureId;
				break;
			}

		$propList["PRODUCT"]["PROPERTY_LIST"]["MEASURE"]["ENUM"] = $measureEnum;
		$propList["CONNECT"]["PROPERTY_LIST"]["MEASURE"]["ENUM"] = $measureEnum;

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("fabrica");
	}*/

	public function getList(array $parameters = array())
	{
		$parameters["count_total"] = false;
		return Shopmate\Internals\FabricaPartTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$parameters = array("filter" => array("ID" => $primary));
		$result = self::getList($parameters)->fetch();

		$partProds = array();
		$res = Shopmate\Internals\FabricaPartProductTable::getList(array(
			"select" => array("FABRICA_PROD_ID", "AMOUNT", "MEASURE", 
				"PARENT" => new Entity\ExpressionField("PARENT", 
					"(CASE 
						WHEN (%s = %s) OR (%s IS NULL)
							THEN \"Y\"
						ELSE \"N\" 
					END)",
					array("FABRICA_PROD.PARENT_ID", "FABRICA_PROD.ID", "FABRICA_PROD.PARENT_ID")
				),
				"TYPE" => "FABRICA_PROD.TYPE",
				"PRODUCT_ID" => "FABRICA_PROD.PRODUCT_ID"
			),
			"filter" => array("PART_ID" => $primary)
		));
		while ($row = $res->fetch()) 
			if ($row["PARENT"] == "Y")
				$result["PRODUCT"][] = $row;
			else
				$result["CONNECT"][] = $row;

		return $result;
	}

	public static function checkErrors(array $data, array $propList = array(), &$errors, $ignoreRequired = false)
	{
		$errors = (array) $errors;
		$errors = array_merge((array) $errors, parent::checkErrors($data, $propList, $errors, $ignoreRequired));

		if (!empty($data["PRODUCT"]) && empty($data["COMMENT"]))
		{
			$products = $connects = array();
			foreach ($data["PRODUCT"] as $key => $product) 
				if (!empty($product["FABRICA_PROD_ID"]))
					$products[] = $product;
			foreach ($data["CONNECT"] as $key => $product) 
				if (!empty($product["FABRICA_PROD_ID"]))
					$connects[] = $product;

			foreach ($products as $key => $product) 
			{
				$fabricaProduct = Fabrica::getById($product["FABRICA_PROD_ID"]);
				$products[$key]["RULE"] = $fabricaProduct["ITEMS"][0];
			}

			foreach ($products as $kp => $product) 
			{
				if ($product["RULE"]["TYPE"] == 1 || $product["RULE"]["TYPE"] == 2)
				{
					$product["PCT"] = $product["AMOUNT"] / $product["RULE"]["AMOUNT"];
					if ($product["RULE"]["TYPE"] == 2) $product["PCT"] *= $product["RULE"]["MEASURE_TO"] / $product["RULE"]["MEASURE_FROM"];

					foreach ($product["RULE"]["CONNECT"] as $rule_connect) 
					{
						$rule_amount = $rule_connect["AMOUNT"] * $product["PCT"] * $rule_connect["MEASURE_FROM"] / $rule_connect["MEASURE_TO"];

						$found = false;
						foreach ($connects as $kc => $connect) 
							if($rule_connect["ID"] == $connect["FABRICA_PROD_ID"])
							{
								$connect["RULE_AMOUNT"] += $rule_amount;
								$connect["FAULT_RATIO"] += $product["RULE"]["FAULT_RATIO"];
								$connect["NAME"] = $rule_connect["NAME"];
								$connects[$kc] = $connect;
								$found = true;
								break;
							}
						if (!$found)
						{
							$connects[] = array(
								"FABRICA_PROD_ID" => $rule_connect["ID"],
								"AMOUNT" => 0,
								"RULE_AMOUNT" => $rule_amount,
								"FAULT_RATIO" => $product["RULE"]["FAULT_RATIO"],
								"NAME" => $rule_connect["NAME"]
							);
							$errors[] = new Entity\EntityError(Loc::getMessage("ERROR_FOUND_CONNECT", array("#PRODUCT#" => $rule_connect["NAME"])));
						}
					}
				}
				$products[$kp] = $product;
			}

			foreach ($connects as $connect) 
				if (round(abs($connect["AMOUNT"] - $connect["RULE_AMOUNT"]), 4) > ($connect["RULE_AMOUNT"] * $connect["FAULT_RATIO"] / 100))
					$errors[] = new Entity\EntityError(Loc::getMessage("ERROR_FAULT_RATIO", array("#PRODUCT#" => $connect["NAME"], "#FAULT_RATIO#" => $connect["FAULT_RATIO"], "#AMOUNT#" => $connect["AMOUNT"], "#RULE_AMOUNT#" => $connect["RULE_AMOUNT"], "#RULE_FAUT#" => $connect["RULE_AMOUNT"] * $connect["FAULT_RATIO"] / 100)));
		}

		return $errors;
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
			$products = $data["PRODUCT"];
			$connect = $data["CONNECT"];
			unset($data["PRODUCT"], $data["CONNECT"]);
			if($primary > 0)
			{
				/*$res = Shopmate\Internals\FabricaPartTable::update($primary, $data);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());*/
			}
			else
			{
				if (empty($data["DATE"]))
					$data["DATE"] = new \Bitrix\Main\Type\DateTime();
				if (empty($data["NAME"]))
					$data["NAME"] = Loc::getMessage("DEFAULT_NAME", array("#TYPE#" => Loc::getMessage("SITE_SECTION_NAME"), "#DATE#" => $data["DATE"]->toString()));
				
				$res = Shopmate\Internals\FabricaPartTable::add($data);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
				else
					$primary = $res->GetID();
			}
			$data["PRODUCT"] = $products;
			$data["CONNECT"] = $connect;

			if ($primary > 0)
			{
				$fabricaProdIds = array();

				foreach ($data["PRODUCT"] as $key => $product) 
					if (!empty($product["FABRICA_PROD_ID"]))
						$fabricaProdIds[] = $product["FABRICA_PROD_ID"];
					else
						unset($data["PRODUCT"][$key]);

				if (!empty($fabricaProdIds))
				{
					foreach ($data["CONNECT"] as $key => $product) 
						if (!empty($product["FABRICA_PROD_ID"]))
							$fabricaProdIds[] = $product["FABRICA_PROD_ID"];
						else
							unset($data["CONNECT"][$key]);

					$lastFabricaProdId = array();
					$res = Shopmate\Internals\FabricaPartProductTable::getList(array(
						"select" => array("FABRICA_PROD_ID"),
						"filter" => array("PART_ID" => $primary, "FABRICA_PROD_ID" => $fabricaProdIds)
					));
					while ($row = $res->fetch()) 
						$lastFabricaProdId[] = $row["FABRICA_PROD_ID"];

					$partProds = array_merge($data["PRODUCT"], $data["CONNECT"]);

					foreach ($partProds as $key => $product) 
					{
						if (in_array($product["FABRICA_PROD_ID"], $lastFabricaProdId))
						{
							Shopmate\Internals\FabricaPartProductTable::update(array("PART_ID" => $primary, "FABRICA_PROD_ID" => $product["FABRICA_PROD_ID"]), $product);
						}
						else
						{
							$product["PART_ID"] = $primary;
							Shopmate\Internals\FabricaPartProductTable::add($product);
						}
					}

					foreach ($partProds as $key => $product) 
						if (($key = array_search($product["FABRICA_PROD_ID"], $lastFabricaProdId)) !== false)
							unset($lastFabricaProdId[$key]);
					if (!empty($lastFabricaProdId))
						foreach ($lastFabricaProdId as $fabricaProdId) 
						{
							Shopmate\Internals\FabricaPartProductTable::delete(array("PART_ID" => $primary, "FABRICA_PROD_ID" => $fabricaProdId));
						}
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
		$result = parent::delete($primary);
		if(!$result->isSuccess()) return $result;
		
		$result = Shopmate\Internals\FabricaPartTable::delete($primary);

		return $result;
	}
}