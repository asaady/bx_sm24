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

class UserOverhead extends Base
{
	protected static $currentFields = array("ID", "DATE", "FROM_TO", "ACTIVE_FORMAT");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		"STORE_ID" => Array(
			"REQUIRED" => "Y"
		),
		"XML" => Array(
			"PROPERTY_TYPE" => "F",
			"ATTR" => array(
				"accept" => "text/xml"
			),
		),
		"DESCRIPTION" => array(
			"PROPERTY_TYPE" => "T"
		)
	);
	protected static $overheadPropList = array(
		"NUMBER_DOCUMENT" => Array(
			"DISABLED" => "Y"
		),
		"DATE_DOCUMENT" => Array(
			"DISABLED" => "Y"
		),
		"CONTRACTOR_ID" => Array(
			"DISABLED" => "Y"
		),
		"ELEMENT" => Array(
			"PROPERTY_TYPE" => "SUBLIST",
			"MULTIPLE" => "Y",
			"NUM_LIST" => "Y",
			"READONLY" => "Y",
			"PROPERTY_LIST" => array(
				"ELEMENT_ID" => array(
					"REQUIRED" => "Y"
				),
				"ELEMENT_NAME" => array(
					"DISABLED" => "Y",
					"PROPERTY_TYPE" => "T",
					"ATTR" => array(
						"cols" => 30,
						"rows" => 5
					)
				),
				"DOC_AMOUNT" => array(
					"DISABLED" => "Y",
				),
				"DOC_MEASURE" => array(
					"DISABLED" => "Y",
				),
				"AMOUNT" => array(
				),
				"MEASURE" => array(
					"PLACEHOLDER" => "",
					"PROPERTY_TYPE" => "L",
					"DISABLED" => "Y",
				),
				"SHOP_PRICE" => array(
					"VERIFICATION" => "float"
				),
				"PURCHASING_PRICE" => array(
					"DISABLED" => "Y",
				),
				"PURCHASING_NDS" => array(
					"DISABLED" => "Y",
					"PROPERTY_TYPE" => "L",
				),
				"NDS_VALUE" => array(
					"DISABLED" => "Y",
				),
				"PURCHASING_SUMM" => array(
					"VERIFICATION" => "float"
				),
				"START_DATE" => array(
					"USER_TYPE" => "DateTime"
				),
				"1C_CODE" => array(
					"PROPERTY_TYPE" => "H",
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
		"TOTAL_FACT" => Array(
			"PLACEHOLDER" => "0.0",
		)
	);
	public static function getPropList()
	{
		$propList = static::$propList;


		return $propList;
	}

	public static function GetUserPermission()
	{
		return parent::GetUserPermission("overhead", true);
	}

	public function prepareResult()
	{
		$primary = $this->result->getData()["ID"];
		if($primary > 0)
		{
			$propList = $this->getProps();
			if(!self::isCreater($primary))
				self::disableInput($propList, array("STORE_ID", "XML"));
			$this->setPropList($propList);
		}
	}

	public function isCreater($primary = 0)
	{
		global $USER;
		if ($USER->IsAdmin())
			return true;
		if ($primary > 0)
		{
			$res = self::getList(array("select" => array("ID", "USER_ID"), "filter" => array("ID" => $primary, "USER_ID" => $USER->GetId())));
			if (!$res->fetch())
				return false;
		}
		return true;
	}

	public function resultModifier($arResult)
	{
		if (!empty($arResult["ITEM"])) 
		{
			if (!self::isCreater($arResult["ITEM"]["ID"]))
			{
				$arResult["PROPERTY_LIST"]["STORE_ID"]["DISABLED"] = "Y";
				$arResult["PROPERTY_LIST"]["XML"]["DISABLED"] = "Y";
				$arResult["PROPERTY_LIST"]["XML"]["CLASS"] = $arResult["PROPERTY_LIST"]["XML"]["CLASS"]." hidden";

				$arResult["PROPERTY_LIST"]["XML"]["PROPERTY_TYPE"] = "H";
				$arResult["PROPERTY_LIST"]["STORE_ID"]["PROPERTY_TYPE"] = "H";
			}
			if ($arResult["ITEM"]["ACCEPTED"] != "Y")
			{	
				if (self::isCreater($arResult["ITEM"]["ID"]))
				{
					if (empty($arResult["ITEM"]["OVERHEAD_ID"]))
						$arResult["SHOW_EDIT_BTN"] = "Y";
					if (!empty($arResult["ITEM"]["STATUS"]) && $arResult["ITEM"]["STATUS"] != "N")
						$arResult["SHOW_ACCEPT_BTN"] = "Y";
				}
				if (!empty(Shopmate\Shops::getUserShop()) && $arResult["ITEM"]["STORE_ID"] == Shopmate\Shops::getUserShop() && empty($arResult["ITEM"]["OVERHEAD_ID"]))
					$arResult["SHOW_ACT_BTN"] = "Y";
			}
			if ($arResult["ITEM"]["XML"] && $arResult["ITEM"]["STORE_ID"] == Shopmate\Shops::getUserShop())
			{
				$arResult["PROPERTY_LIST"] = static::$overheadPropList + $arResult["PROPERTY_LIST"];
				$arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["MEASURE"]["ENUM"] = Products::getMeasureEnumList();
				$arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["PURCHASING_NDS"]["ENUM"] = Products::getVatEnumList();
				$arResult["PROPERTY_LIST"] = self::setInputLangTitle($arResult["PROPERTY_LIST"]);
				
				$xmlData = Shopmate\UserOverhead::loadByID($arResult["ITEM"]["XML"]);

				$arResult["ITEM"]["NUMBER_DOCUMENT"] = $xmlData["OVERHEAD"]["DOCUMENT_NUMBER"];
				$arResult["ITEM"]["DATE_DOCUMENT"] = $xmlData["OVERHEAD"]["DOCUMENT_DATE"];

				if (!empty($xmlData["CONTRACTOR"]["INN"]))
				{
					$contractor = new Contractor();
					if ($contr = $contractor->getList(array("filter" => array("INN" => $xmlData["CONTRACTOR"]["INN"]), "select" => array("ID")))->fetch())
						$arResult["ITEM"]["CONTRACTOR_ID"] = $contr["ID"];
					else
					{
						$res = $contractor->add(array(
							"INN" => $xmlData["CONTRACTOR"]["INN"],
							"KPP" => $xmlData["CONTRACTOR"]["KPP"],
							"COMPANY" => $xmlData["CONTRACTOR"]["NAME"],
							"ADDRESS" => $xmlData["CONTRACTOR"]["ADDRESS"],
						));
						if ($res->isSuccess())
							$arResult["ITEM"]["CONTRACTOR_ID"] = $res->getID();
						else
						{
							$errors = $res->getErrors();
							foreach($errors as $error)
								$arResult["ERRORS"][] = $error->getMessage();
						}
					}
				}

				$totalSumm = 0;
				$arResult["ITEM"]["ELEMENT"] = array();
				foreach ($xmlData["ITEMS"] as $xmlItem) 
				{
					$xmlItem["NDS"] = floatval($xmlItem["NDS"]);
					$arResult["ITEM"]["ELEMENT"][] = array(
						"ELEMENT_NAME" => $xmlItem["NAME"],
						"DOC_AMOUNT" => $xmlItem["AMOUNT"],
						"DOC_MEASURE" => $xmlItem["MEASURE"],
						"AMOUNT" => $xmlItem["AMOUNT"],
						"PURCHASING_PRICE" => $xmlItem["PRICE"],
						"PURCHASING_NDS" => $xmlItem["NDS"],
						"NDS_VALUE" => $xmlItem["NDS_VALUE"],
						"PURCHASING_SUMM" => $xmlItem["SUMM_NDS"],
						"1C_CODE" => $xmlItem["1C_CODE"],
					);
					if ($xmlItem["NDS"] > 0 && !isset($arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["PURCHASING_NDS"]["ENUM"][$xmlItem["NDS"]]))
					{
						$res = Shopmate\BitrixInternals\VatTable::add(array(
							"ACTIVE" => "Y",
							"NAME" => "НДС ".$xmlItem["NDS"]."%",
							"RATE" => $xmlItem["NDS"],
						));
						if ($res->isSuccess())
							$arResult["PROPERTY_LIST"]["ELEMENT"]["PROPERTY_LIST"]["PURCHASING_NDS"]["ENUM"][$xmlItem["NDS"]] = "НДС ".$xmlItem["NDS"]."%";
						else
						{
							$errors = $res->getErrors();
							foreach($errors as $error)
								$arResult["ERRORS"][] = $error->getMessage();
						}
					}
					$totalSumm += $xmlItem["SUMM_NDS"];
				}

				$elem_codes = array();
				foreach ($arResult["ITEM"]["ELEMENT"] as $element)
					$elem_codes[] = $element["1C_CODE"];
				if (!empty($elem_codes))
				{
					$res = Internals\UserOverheadProductTable::getList(array("filter" => array("STORE_ID" => Shopmate\Shops::getUserShop(), "CODE" => $elem_codes)));
					$code_products = array();
					while ($row = $res->fetch())
						$code_products[$row["CODE"]] = $row["PRODUCT_ID"];
					foreach ($arResult["ITEM"]["ELEMENT"] as $key => $element)
						$arResult["ITEM"]["ELEMENT"][$key]["ELEMENT_ID"] = $code_products[$element["1C_CODE"]];
				}

				$arResult["ITEM"]["TOTAL_SUMM"] = $totalSumm;
			}
		}
		return $arResult;
	}

	public static function getOrderList($sort_fields = array())
	{
		if(empty($sort_fields))
			$sort_fields = static::$currentFields;

		foreach ($sort_fields as $keyField => $currentField) 
			if ($currentField == "FROM_TO")
			{
				$sort_fields[$keyField] = empty(Shopmate\Shops::getUserShop()) ? "STORE_FORMATED" : "USER_FORMATED";
				break;
			}

		return parent::getOrderList($sort_fields);
	}

	public function getList(array $parameters = array())
	{
		$referenceFields = array(
			"USER_FORMATED" => new Entity\ExpressionField("USER_FORMATED", 
				"(CONCAT(%s, \" (\", %s, \")\"))", 
				array("USER.WORK_COMPANY", "USER.NAME")
			),
			"STORE_FORMATED" => new Entity\ExpressionField("STORE_FORMATED", 
				"(CONCAT(%s, \" (\", %s, \")\"))", 
				array("STORE.TITLE", "STORE.XML_ID")
			),
			"ACTIVE_FORMAT" => new Entity\ExpressionField("ACTIVE_FORMAT", 
				"(CASE %s 
					WHEN \"Y\" 
						THEN \"".Loc::getMessage("ACTIVE_Y")."\"
					ELSE \"".Loc::getMessage("ACTIVE_N")."\" 
				END)", 
				array("ACTIVE")
			),
			"DESCRIPTION" => new Entity\ExpressionField("DESCRIPTION", "''"),
		);

		$currentFields = self::$currentFields;
		foreach ($currentFields as $keyField => $currentField) 
			if ($currentField == "FROM_TO")
			{
				$currentFields[$keyField] = empty(Shopmate\Shops::getUserShop()) ? "STORE_FORMATED" : "USER_FORMATED";
				break;
			}

		$parameters["select"] = parent::getSelect($parameters["select"], $currentFields, $referenceFields);

		global $USER;
		$parameters["filter"][] = array(
			"LOGIC" => "OR",
			"USER_ID" => $USER->getId(),
			"STORE_ID" => Shopmate\Shops::getUserShop()
		);

		$parameters["count_total"] = false;
		unset($parameters["offset"], $parameters["limit"]);

		return Internals\UserOverheadTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$select = array_keys(static::$propList);
		$select[] = "ID";
		$select[] = "ACTIVE";
		$select[] = "STATUS";
		$select[] = "ACCEPTED";
		$select[] = "OVERHEAD_ID";
		$parameters = array("filter" => array("ID" => $primary), "select" => $select);
		$item = self::getList($parameters)->fetch();

		$userShop = Shopmate\Shops::getUserShop();
		$userShop = (array) $userShop;
		if ($item["ACTIVE"] == "Y" && in_array($item["STORE_ID"], $userShop))
			Internals\UserOverheadTable::update($primary, array("ACTIVE" => "N"));

		$item["CHAT"] = self::getChatByOverheadID($primary);

		if ($item["XML"] > 0) Shopmate\UserOverhead::loadByID($item["XML"]);

		return $item;
	}

	public function getChatByOverheadID($primary = 0)
	{
		$chat = array();
		$res = Internals\UserOverheadChatTable::getList(array("filter" => array("UO_ID" => $primary), "order" => array("DATE" => "DESC")));
		while ($row = $res->fetch())
			$chat[] = $row;
		return $chat;
	}

	public static function checkErrors(array $data, array $propList = array(), &$errors, $ignoreRequired = false)
	{
		$errors = (array) $errors;
		$errors = array_merge((array) $errors, parent::checkErrors($data, $propList, $errors, $ignoreRequired));

		if (!empty($data["ELEMENT"]))
			foreach ($data["ELEMENT"] as $key => $element) 
				if (empty($element["ELEMENT_ID"]) && !empty($element["ELEMENT_NAME"]))
					$errors[] = new Entity\EntityError(Loc::getMessage("ERROR_ELEMENT_CONNECT", array("#ELEMENT#" => $element["ELEMENT_NAME"])), "FIELD_ELEMENT_ELEMENT_ID_".$key);
				;

		foreach ($propList as $prop => $arProp)
			if($arProp["PROPERTY_TYPE"] == "F" && !empty($_FILES[$prop]) && $_FILES[$prop]["size"] > 0)
			{
				if (!empty($arProp["ATTR"]["accept"]) && stripos($_FILES[$prop]["type"], strstr($arProp["ATTR"]["accept"], "/")) === false)
					$errors[] = new Entity\EntityError(Loc::getMessage("ERROR_FILE_TYPE"), "FIELD_".$prop);
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
			$data["ID"] = $primary;

			if ($data["submit"] == "act" || $data["apply"] == "act")
			{
				$status = !empty($data["accepted"]) ? "A" : (!empty($data["changed"]) ? "C" : (!empty($data["rejected"]) ? "R" : ""));
				$message = $data["message"];
				$act_accept = $data["act_accept"];

				$overhead = array();
				foreach (array_keys(static::$overheadPropList) as $prop) 
					if (isset($data[$prop]))
						$overhead[$prop] = $data[$prop];
			}

			$result = parent::update($primary, $data);
		}

		global $USER;

		if (!empty($status) && $status != "A")
		{
			parent::checkErrors($data, array("DESCRIPTION" => array("REQUIRED" => "Y")), $errors);
			if(!empty($errors)) $result->addErrors($errors);
		}

		if($result->isSuccess())
		{
			$tobj = new Internals\UserOverheadTable;

			$isCreater = self::isCreater($primary);

			$data["ACTIVE"] = "Y";
			$description = $data["DESCRIPTION"];
			unset($data["DESCRIPTION"]);
			if (!empty($status))
				$data["STATUS"] = $status;
			if (!empty($act_accept) && $isCreater)
				$data["ACCEPTED"] = "Y";

			$arFile = $_FILES["XML"];
			if ($arFile["size"] > 0)
			{
				$arFile["MODULE_ID"] = "yadadya.shopmate";
				$arFile["old_file"] = $data["XML"];
				$data["XML"] = \CFile::SaveFile($arFile, "useroverhead");
			}

			if (in_array($status, array("A", "C")))
			{
				$obOverhead = new Overhead;
				if ($primary > 0)
				{
					$uo = self::getList(array("select" => array("OVERHEAD_ID"), "filter" => array("ID" => $primary)))->fetch();
					if ($uo["OVERHEAD_ID"] > 0)
						$res = $obOverhead->update($uo["OVERHEAD_ID"], $overhead);
					else
						$res = $obOverhead->add($overhead);
				}
				else
					$res = $obOverhead->add($overhead);

				if ($res->isSuccess())
					$data["OVERHEAD_ID"] = $res->getId();

				$store_id = Shopmate\Shops::getUserShop();
				$obUserOverheadProd = new Internals\UserOverheadProductTable;
				foreach ($overhead["ELEMENT"] as $element)
				{
					$obUserOverheadProd->add(array(
						"STORE_ID" => Shopmate\Shops::getUserShop(), 
						"PRODUCT_ID" => $element["ELEMENT_ID"],
						"CODE" => $element["1C_CODE"],
					));
				}
			}

			if ($primary > 0)
			{
				unset($data["STORE_ID"]);
				$res = $tobj->update($primary, $data);
				if(!$res) 
					$result->addError(new Entity\EntityError($tobj->LAST_ERROR));
			}
			else
			{
				global $USER;
				$data["USER_ID"] = $USER->GetId();
				$data["DATE"] = new \Bitrix\Main\Type\DateTime();
				$res = $tobj->add($data);
			}
			if(!$res->isSuccess())
				$result->addErrors($res->getErrors());
			else
				$primary = $res->GetID();

			if (!empty($description) || $status == "A")
			{
				$time = new \Bitrix\Main\Type\DateTime();
				//$desc_header = "<b>[".$time."]</b><br />";
				$desc_header = "";
				if (!empty($status))
					$desc_header .= Loc::getMessage("DESCRIPTION_ACT", array("#STATUS#" => Loc::getMessage("ACT_".$status)));
				if (!empty($message))
					$desc_header .= Loc::getMessage("DESCRIPTION_MESSAGE");
				if (!empty($act_accept))
					$desc_header .= Loc::getMessage("DESCRIPTION_ACT_ACCEPT");

				$res = Internals\UserOverheadChatTable::add(array("UO_ID" => $primary, "DATE" => $time, "USER_ID" => $USER->GetId(), "DESCRIPTION" => $desc_header.$description));

				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
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
		return Internals\UserOverheadTable::delete($primary);
	}

	public function getNewCnt()
	{
		$cnt = 0;
		global $USER;
		$res = Internals\UserOverheadTable::getList(array(
			"select" => array("CNT"), 
			"filter" => array(
				"ACTIVE" => "Y",
				array(
					"LOGIC" => "OR",
					"USER_ID" => $USER->getId(),
					"STORE_ID" => Shopmate\Shops::getUserShop()
				)
			), 
			"runtime" => array(new Entity\ExpressionField('CNT', 'COUNT(*)'))));
		if ($row = $res->fetch()) 
			$cnt = $row["CNT"];
		return $cnt;
	}
}