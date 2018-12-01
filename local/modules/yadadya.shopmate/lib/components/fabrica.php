<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\Internals;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;

Loc::loadMessages(__FILE__);

class Fabrica extends Base
{
	protected static $currentFields = array("ID", "NAME", "AMOUNT", "USER_FORMATED", "QUANTITY", "ACTIVE");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"ITEMS" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"PROPERTY_LIST" => array(
				"PRODUCT_ID" => array(
				),
				"NAME" => array(
				),
				"MEASURE" => array(
					"PROPERTY_TYPE" => "L",
					"DISABLED" => "Y",
				),
				"TYPE" => array(
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
					"ENUM" => array(
						0 => "simple",
						1 => "merge",
						2 => "split",
					)
				),

				"AMOUNT" => array(
					"VERIFICATION" => "float"
				),
				"AMOUNT_MEASURE" => array(
					"PROPERTY_TYPE" => "L",
				),
				"MEASURE_FROM" => array(
					"VERIFICATION" => "float"
				),
				"MEASURE_TO" => array(
					"VERIFICATION" => "float"
				),
				"FAULT_RATIO" => array(
					"VERIFICATION" => "float"
				),

				"CONNECT" => array(
					"PROPERTY_TYPE" => "SUBLIST",
					"MULTIPLE" => "Y",
					"PROPERTY_LIST" => array(
						"PRODUCT_ID" => array(
						),
						"NAME" => array(
						),
						"AMOUNT" => array(
							"VERIFICATION" => "float"
						),
						"MEASURE" => array(
							"PLACEHOLDER" => "",
							"PROPERTY_TYPE" => "L",
						),
						"MEASURE_FROM" => array(
							"VERIFICATION" => "float"
						),
						"MEASURE_TO" => array(
							"VERIFICATION" => "float"
						),
						"AMOUNT_RATIO" => array(
							"VERIFICATION" => "float"
						),
						"ID" => array(
							"PROPERTY_TYPE" => "H",
						),
					)
				),
			),
		),
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

		$propList["ITEMS"]["PROPERTY_LIST"]["MEASURE"]["ENUM"] = $measureEnum;
		$propList["ITEMS"]["PROPERTY_LIST"]["MEASURE"]["DEFAULT_VALUE"] = $measureDefault;
		$propList["ITEMS"]["PROPERTY_LIST"]["AMOUNT_MEASURE"]["ENUM"] = $measureEnum;
		$propList["ITEMS"]["PROPERTY_LIST"]["AMOUNT_MEASURE"]["DEFAULT_VALUE"] = $measureDefault;
		$propList["ITEMS"]["PROPERTY_LIST"]["CONNECT"]["PROPERTY_LIST"]["MEASURE"]["ENUM"] = $measureEnum;
		$propList["ITEMS"]["PROPERTY_LIST"]["CONNECT"]["PROPERTY_LIST"]["MEASURE"]["DEFAULT_VALUE"] = $measureDefault;

		$propList["ITEMS"]["PROPERTY_LIST"]["TYPE"]["ENUM"] = array(
			0 => Loc::getMessage("ITEMS_TYPE_VALUE_0"),
			1 => Loc::getMessage("ITEMS_TYPE_VALUE_1"),
			2 => Loc::getMessage("ITEMS_TYPE_VALUE_2"),
		);

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("fabrica");
	}*/

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"USER_FORMATED" => new Entity\ExpressionField("USER_FORMATED", 
				"(CONCAT(%s, \" (\", %s, \")\"))", 
				array("USER.WORK_COMPANY", "USER.NAME")
			),
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["filter"][] = array(
			"LOGIC" => "OR",
			array("PARENT_ID" => false),
			array("=PARENT_ID" => new \Bitrix\Main\DB\SqlExpression("`".strtolower(Internals\FabricaProductTable::getEntity()->getCode())."`.`ID`"))
		);
		$parameters["filter"]["STORE_ID"] = Shopmate\Shops::getUserStore();

		$parameters["count_total"] = false;

		return Internals\FabricaProductTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$result = array("ITEMS" => array());

		$items = array();
		$res = Internals\FabricaProductTable::getList(array("filter" => array(array("LOGIC" => "OR", "ID" => $primary, "PARENT_ID" => $primary))));
		while ($row = $res->fetch())
			$items[$row["ID"]] = $row;

		$parent_id = array();
		foreach ($items as $item)
			$parent_id[] = $item["ID"];

		$connect = array();
		$res = Internals\FabricaProductConnectTable::getList(array("filter" => array("PARENT_ID" => $parent_id)));
		while ($row = $res->fetch())
		{
			if (!empty($items[$row["CONNECT_ID"]]))
				$row = array_merge($items[$row["CONNECT_ID"]], $row);
			$connect[$row["PARENT_ID"]][] = $row;
		}

		foreach ($items as $item)
		{
			$item["CONNECT"] = empty($connect[$item["ID"]]) ? array() : $connect[$item["ID"]];
			$result["ITEMS"][] = $item;
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
		if(!($result instanceof \Bitrix\Main\Entity\AddResult)) 
		{
			if(is_object($this)) 
			{
				$this->result->setData(array("ID" => $primary));
				self::prepareResult();
			}
			$data["ID"] = $primary;
			$result = parent::update($primary, $data);
		}

		if($result->isSuccess())
		{
			$asocId = array();

			$storeId = Shopmate\Shops::getUserShop();
			global $USER;

			$tobj = new Internals\FabricaProductTable;

			$first = true;
			foreach ($data["ITEMS"] as $key => $item) 
			{
				if ($primary > 0)
					$item["PARENT_ID"] = $primary;

				if ($first)
					$item["ID"] = $primary;

				//$aId = $item["ID"];
				$aId = $key;
				unset($item["ID"], $item["CONNECT"]);

				$item["STORE_ID"] = $storeId;
				$item["USER_ID"] = $USER->GetId();
				$item["ACTIVE"] = "Y";
				if (intval($aId) > 0)
					$res = $tobj->Update($aId, $item);
				else
					$res = $tobj->Add($item);

				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
				else
				{
					$id = $res->getId();

					if ($first)
						$primary = $id;

					$asocId[$aId] = $id;

					$data["ITEMS"][$key]["ID"] = $id;
				}


				$first = false;
			}

			if(!($result instanceof \Bitrix\Main\Entity\AddResult))
			{
				$res = Internals\FabricaProductTable::getList(array("filter" => array(array("LOGIC" => "OR", "ID" => $primary, "PARENT_ID" => $primary), "!ID" => array_values($asocId)), "select" => array("ID")));
				while ($row = $res->fetch())
				{
					$tobj->Delete($row["ID"]);
					Internals\FabricaProductConnectTable::Delete(array("PARENT_ID" => $row["ID"]));
				}
			}

			$tobj = new Internals\FabricaProductConnectTable;

			foreach ($data["ITEMS"] as $item)
			{
				$tobj->Delete(array("PARENT_ID" => $item["ID"]));
				if ($item["TYPE"] > 0)
					foreach ($item["CONNECT"] as $connect) 
						if (!empty($connect["ID"]))
						{
							$connect["PARENT_ID"] = $item["ID"];
							$connect["CONNECT_ID"] = $asocId[$connect["ID"]];
							unset($connect["ID"], $connect["PRODUCT_ID"], $connect["NAME"]);
							$res = $tobj->Add($connect);
							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());
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
		return Internals\FabricaProductTable::delete($primary);
	}

	public function getEnumList(array $filter = array(), $json = false)
	{
		$arResult = array();

		//CONNECT==Y

		$fabricaProducts = array();
		$fabricaParams = array(
			"select" => array("ID", "PRODUCT_ID"),
			"filter" => array("STORE_ID" => Shopmate\Shops::getUserStore())
		);

		if (isset($filter["ID"]))
		{
			$fabricaParams["select"][] = "NAME";
			$fabricaParams["filter"]["ID"] = $filter["ID"];
			$res = Internals\FabricaProductTable::getList($fabricaParams);
			while ($row = $res->fetch())
				$arResult[$row["ID"]] = $row["NAME"];

			if($json)
				return Base::getJSONEnumList($arResult);
			
			return $arResult;
		}

		if (isset($filter["SEARCH"]))
		{
			$fabricaParams["filter"]["ACTIVE"] = "Y";
			$fabricaParams["filter"][] = array(
				"LOGIC" => "OR",
				array("PARENT_ID" => false),
				array("=PARENT_ID" => new \Bitrix\Main\DB\SqlExpression("`".strtolower(Internals\FabricaProductTable::getEntity()->getCode())."`.`ID`"))
			);
			$fabricaParams["filter"]["TYPE"] = $filter["TYPE"];
			unset($filter["TYPE"]);
		}

		if (!empty($filter["PARENT_PROD_ID"]))
		{
			$fabricaParams["runtime"]["PARENT_CONNECT"] = new Entity\ReferenceField(
				'PARENT_CONNECT',
				'Yadadya\Shopmate\Internals\FabricaProductConnect',
				array('=this.ID' => 'ref.CONNECT_ID'),
				array('join_type' => 'LEFT')
			);
			$fabricaParams["filter"]["PARENT_CONNECT.PARENT_ID"] = $filter["PARENT_PROD_ID"];
			unset($filter["PARENT_PROD_ID"]);
		}

		$res = Internals\FabricaProductTable::getList($fabricaParams);
		while ($row = $res->fetch())
			$fabricaProducts[$row["ID"]] = $row["PRODUCT_ID"];

		$filter["ID"] = array_values($fabricaProducts);

		$products = Products::getEnumList($filter);

		foreach ($fabricaProducts as $fpId => $pId) 
			if (!empty($products[$pId]))
				$arResult[$fpId] = $products[$pId];

		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}