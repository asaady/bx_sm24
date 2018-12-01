<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\BitrixInternals;
use Yadadya\Shopmate\Internals;

Loc::loadMessages(__FILE__);

class Contractor extends Base
{
	protected static $currentFields = array("ID", "NAME_FORMATED", "PERSON_TYPE_FORMATED", "DISCOUNT_FORMATED", "LAST_DATE_DOCUMENT", "DEBT");
	protected static $currentSort = array("ID" => "DESC");
	protected static $filterList = array(
		"SEARCH" => array(),
		"PERSON_TYPE" => array(
			"PROPERTY_TYPE" => "L",
			"ENUM" => array(
				1 => "fiz",
				2 => "yur",
			),
			"DEFAULT_VALUE" => 1
		),
	);
	protected static $propList = array(
		"PERSON_TYPE" => array(
			"PROPERTY_TYPE" => "L",
			"REQUIRED" => "Y",
			"ENUM" => array(
				1 => "fiz",
				2 => "yur",
			),
			"DEFAULT_VALUE" => 1
		),
		"INN" => array(
			"UNIQUE" => "Y",
			//"VERIFICATION" => "999999999999"
		),
		"COMPANY" => array(),
		"PERSON_NAME" => array(),
		"BIK" => array(),
		"OGRN" => array(),
		"TAX_TYPE" => array(
			"PROPERTY_TYPE" => "L",
			"LIST_TYPE" => "C",
			"ENUM" => array(
				"nds" => "full (with NDS)",
				"usn" => "usn",
				"patent" => "patent",
			)
		),
		"NDS" => array(
			"PROPERTY_TYPE" => "L",
			"REQUIRED" => "Y",
			"ENUM" => array(
				0 => "0%",
				18 => "18%",
			),
			"DEFAULT_VALUE" => 1
		),
		"PHONE" => array(
			"REQUIRED" => "Y",
			"UNIQUE" => "Y",
			"VERIFICATION" => "phone"
		),
		"EMAIL" => array(
			"REQUIRED" => "Y",
			"UNIQUE" => "Y"
		),
		"REGULAR" => array(
			"PROPERTY_TYPE" => "L",
			//"REQUIRED" => "Y",
			"ENUM" => array(
				1 => "yes",
				0 => "no",
			)
		),
		"CONTRACT" => array(),
		"CONTRACT_DATE" => array(
			"USER_TYPE" => "Date"
		),
		"DELAY" => array(),
		"ADDRESS" => array(
			"PROPERTY_TYPE" => "T",
		),
		"ADDRESS_FACT" => array(
			"PROPERTY_TYPE" => "T",
		),
		"NOTES" => array(
			"PROPERTY_TYPE" => "T",
		),
		"DISCOUNT" => array(),
	);

	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["PERSON_TYPE"]["ENUM"] = array(
			1 => array("VALUE" => Loc::getMessage("NAME_FORMATED_1")),
			2 => array("VALUE" => Loc::getMessage("NAME_FORMATED_2")),
		);

		$propList["REGULAR"]["ENUM"] = array(
			1 => array("VALUE" => Loc::getMessage("YES")),
			0 => array("VALUE" => Loc::getMessage("NO")),
		);

		$propList["TAX_TYPE"]["ENUM"] = array(
			"nds" => array("VALUE" => Loc::getMessage("TAX_TYPE_NDS")),
			"usn" => array("VALUE" => Loc::getMessage("TAX_TYPE_USN")),
			"patent" => array("VALUE" => Loc::getMessage("TAX_TYPE_PATENT")),
		);

		$propList["NDS"]["ENUM"] = Products::getVatEnumList();

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("contractors");
	}*/

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
					case "SEARCH":

						if(is_array($value)) $value = array_shift($value);

						$arFilter["SEARCH"] = parent::getSearchComboFilter($value);

						break;


					default:

						$arFilter[$field] = $value;

						break;
				}
		}
		
		return $arFilter;
	}

	public function getList(array $parameters = array())
	{
		$parameters["runtime"][] = new Entity\ReferenceField(
			'SMCONTRACTOR',
			'Yadadya\Shopmate\Internals\Contractor',
			array('=ref.CONTRACTOR_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);

		$q = new Entity\Query(BitrixInternals\StoreDocsTable::getEntity());
		$q->registerRuntimeField("SMDOC", new Entity\ReferenceField(
			'SMDOC',
			'Yadadya\Shopmate\Internals\StoreDocs',
			array('=ref.DOC_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		));
		$q->setSelect(array(
			"CONTRACTOR_ID", 
			"DATE_DOCUMENT",
			"DEBT" => new Entity\ExpressionField("DEBT", 
				"(%s - %s)", 
				array("TOTAL", "SMDOC.TOTAL_FACT")
			)
		));
		$q->setFilter(array("SMDOC.STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore()));
		$q->setOrder(array("DATE_DOCUMENT" => "DESC"));
		$q->setGroup("CONTRACTOR_ID");
		$parameters["runtime"][] = new Entity\ReferenceField(
			'STORE_DEBT',
			Base::getSqlFieldClass($q->getQuery(), array("CONTRACTOR_ID" => array("data_type" => "integer", "primary" => "true"), "DATE_DOCUMENT" => array("data_type" => "datetime"), "DEBT" => array("data_type" => "float")), "ContractorStoreDebt"),
			array('=ref.CONTRACTOR_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);

		if (isset($parameters["filter"]["SEARCH"]))
		{
			$searchFields = array("INN", "COMPANY", "PERSON_NAME", "SMCONTRACTOR.BIK", "SMCONTRACTOR.OGRN", "PHONE", "EMAIL", "SMCONTRACTOR.CONTRACT", "ADDRESS", "SMCONTRACTOR.ADDRESS_FACT", "SMCONTRACTOR.NOTES");
			$concatFields = "CONCAT(IFNULL(%s, \"\")".str_repeat(", \" \", IFNULL(%s, \"\")", count($searchFields) - 1).")";
			$parameters["runtime"]["SEARCH"] = new Entity\ExpressionField("SEARCH", 
				$concatFields,
				$searchFields
			);
		}

		/*$q = new Entity\Query(BitrixInternals\StoreDocsTable::getEntity());
		$q->registerRuntimeField("SMDOC", new Entity\ReferenceField(
			'SMDOC',
			'Yadadya\Shopmate\Internals\StoreDocs',
			array('=ref.DOC_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		));
		$q->setSelect(array("CONTRACTOR_ID", "DATE_DOCUMENT"));
		$q->setFilter(array("SMDOC.STORE_ID" => \Yadadya\Shopmate\Shops::getUserStore()));
		$q->setOrder(array("DATE_DOCUMENT" => "DESC"));
		$q->setGroup("CONTRACTOR_ID");
		$parameters["runtime"][] = new Entity\ReferenceField(
			'STORE_LAST_DATE',
			Base::getSqlFieldClass($q->getQuery(), array("CONTRACTOR_ID" => array("data_type" => "integer", "primary" => "true"), "DEBT" => array("data_type" => "float", "primary" => "true")), "ContractorStoreLastDate"),
			array('=ref.CONTRACTOR_ID' => 'this.ID'),
			array('join_type' => 'LEFT')
		);*/

		$referenceFields = array(
			"NAME_FORMATED" => new Entity\ExpressionField("NAME_FORMATED", 
				"(CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE CONCAT(%s, \" (\", %s, \")\")
				END)", 
				array("PERSON_TYPE", "COMPANY", "PERSON_NAME", "PERSON_NAME", "PHONE")
			),
			"PERSON_TYPE_FORMATED" => new Entity\ExpressionField("PERSON_TYPE_FORMATED", 
				"(CASE %s 
					WHEN \"2\" 
						THEN \"".Loc::getMessage("NAME_FORMATED_2")."\"
					ELSE \"".Loc::getMessage("NAME_FORMATED_1")."\"
				END)", 
				array("PERSON_TYPE")
			),
			"DISCOUNT_FORMATED" => new Entity\ExpressionField("DISCOUNT_FORMATED", 
				"(CASE  
					WHEN %1\$s > 0 
						THEN CONCAT(%1\$s, \"%%\")
					ELSE \"-\"
				END)", 
				array("SMCONTRACTOR.DISCOUNT")
			),
			"LAST_DATE_DOCUMENT" => "STORE_DEBT.DATE_DOCUMENT",
			"DEBT" => "STORE_DEBT.DEBT",
			/*"DEBT" => new Entity\ExpressionField("DEBT", 
				"(CASE
					WHEN %1\$s > 0 
						THEN CONCAT(%1\$s, \"руб.\")
					ELSE \"".Loc::getMessage("DEBT_NO")."\"
				END)", 
				array("STORE_DEBT.DEBT")
			),*/
			"BIK" => "SMCONTRACTOR.BIK",
			"OGRN" => "SMCONTRACTOR.OGRN",
			"TAX_TYPE" => "SMCONTRACTOR.TAX_TYPE",
			"NDS" => "SMCONTRACTOR.NDS",
			"REGULAR" => "SMCONTRACTOR.REGULAR",
			"CONTRACT" => "SMCONTRACTOR.CONTRACT",
			"CONTRACT_DATE" => "SMCONTRACTOR.CONTRACT_DATE",
			"DELAY" => "SMCONTRACTOR.DELAY",
			"ADDRESS_FACT" => "SMCONTRACTOR.ADDRESS_FACT",
			"NOTES" => "SMCONTRACTOR.NOTES",
			"DISCOUNT" => "SMCONTRACTOR.DISCOUNT",
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["count_total"] = false;
		
		return BitrixInternals\ContractorTable::getList($parameters);
	}

	public function getByID($primary = 0)
	{
		$result = self::getList(array(
			"select" => array_keys(static::$propList),
			"filter" => array("ID" => $primary)
		))->fetch();
		$result["TITLE"] = ($result["PERSON_TYPE"] == 2 ? $result["COMPANY"]." (".$result["PERSON_NAME"].")" : $result["PERSON_NAME"]).(strlen($result["PHONE"]) ? ", т. ".$result["PHONE"] : "");
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
			$tableFields = array(
				"CONTRACTOR" => array_keys(BitrixInternals\ContractorTable::getMap()),
				"SMCONTRACTOR" => array_keys(Internals\ContractorTable::getMap()),
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
						case "CONTRACTOR":

							$tobj = new BitrixInternals\ContractorTable;

							if ($primary > 0)
							{
								$res = $tobj->update($primary, $tData);
								if (!$res->isSuccess())
									$result->addErrors($res->getErrors());
							}
							else
							{
								if (empty($tData["PERSON_TYPE"])) $tData["PERSON_TYPE"] = 2;
								$res = $tobj->add($tData);
								if (!$res->isSuccess())
									$result->addErrors($res->getErrors());
								else
									$primary = $res->getID();
							}

							break;

						case "SMCONTRACTOR":

							$tobj = new Internals\ContractorTable;

							$res = $tobj->GetList(array(
								"select" => array("ID"),
								"filter" => array(
									"CONTRACTOR_ID" => $primary,
								)
							));

							$tData["CONTRACT_DATE"] = new \Bitrix\Main\Type\DateTime($tData["CONTRACT_DATE"]);

							if($row = $res->fetch())
							{
								$res = $tobj->update($row["ID"], $tData);
							}
							else
							{
								$tData["CONTRACTOR_ID"] = $primary;
								$res = $tobj->add($tData);
							}

							if(!$res->isSuccess())
								$result->addErrors($res->getErrors());

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

	public static function getInfo($filter = array(), $json = false, $select = array())
	{
		$result = array();

		if (!is_array($filter))
			$filter = array("ID" => $filter);

		$result = self::getList(array(
			"select" => empty($select) ? array_keys(static::$propList) : $select,
			"filter" => $filter
		))->fetch();
		//$result["TITLE"] = ($result["PERSON_TYPE"] == 2 ? $result["COMPANY"]." (".$result["PERSON_NAME"].")" : $result["PERSON_NAME"]).(strlen($result["PHONE"]) ? ", т. ".$result["PHONE"] : "");

		if($json)
			return json_encode($result);

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
				"COMPANY" => "%".$q."%",
				"PERSON_NAME" => "%".$q."%",
				"PHONE" => "%".$q."%",
			);
		}
		$result = BitrixInternals\ContractorTable::GetList(array(
			"select" => array("ID", "PERSON_TYPE", "COMPANY", "PERSON_NAME", "PHONE"),
			"filter" => $filter
		));
		while ($row = $result->fetch())
		{
			$arResult[$row["ID"]] = ($row["PERSON_TYPE"] == 2 ? $row["COMPANY"]." (".$row["PERSON_NAME"].")" : $row["PERSON_NAME"]).(strlen($row["PHONE"]) ? ", т. ".$row["PHONE"] : "");
			if(!empty($q)) $arResult[$row["ID"]] = str_ireplace($q, "<b>".$q."</b>", $arResult[$row["ID"]]);
		}
		if($json)
			return Base::getJSONEnumList($arResult);
		
		return $arResult;
	}
}