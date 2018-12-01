<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class FinanceMoney extends Base
{
	protected static $currentFields = array("ID", "DATE", "SUMM", "REASON", "MEMBERS", "OUTGO", "ITEM_TYPE", "TYPE", "ITEM_ID");
	protected static $currentSort = array("DATE" => "DESC");
	protected static $filterList = array();
	protected static $propList = array(
		//"DATE" => array(),
		"TYPE" => array(
			"PROPERTY_TYPE" => "L",
			"ENUM" => array(
				"cash" => "Cash",
				"clearing" => "Clearing",
			),
			"DEFAULT_VALUE" => "cash"
		),
		"OUTGO" => array(
			"PROPERTY_TYPE" => "L",
			"LIST_TYPE" => "C",
			"CLASS_LABEL" => "btn btn-default btn-bordered",
			"ENUM" => array(
				"report" => "on report",
				"contractor" => "contractor",
				"payroll" => "payroll",
			)
		),
		"ITEM_TYPE" => array(),
		"ITEM_ID" => array(),
		//"USER_ID" => array(),
		//"DESCRIPTION" => array(),
		"DESCRIPTION_FILE" => array(),
		"PRICE" => array(),
		"PRICE_GRAY" => array(),
		//"CURRENCY" => array(),
	);
	public static function getPropList()
	{
		$propList = static::$propList;

		$propList["TYPE"]["ENUM"] = array(
			"cash" => Loc::getMessage("TYPE_CASH"),
			"clearing" => Loc::getMessage("TYPE_CLEARING"),
		);

		$propList["OUTGO"]["ENUM"] = array(
			"report" => Loc::getMessage("OUTGO_REPORT"),
			"contractor" => Loc::getMessage("OUTGO_CONTRACTOR"),
			"payroll" => Loc::getMessage("OUTGO_PAYROLL"),
		);

		return $propList;
	}

	/*public static function GetUserPermission()
	{
		return parent::GetUserPermission("financemoney");
	}*/

	public function getList(array $parameters = array())
	{
		$parameters["filter"]["STORE_ID"] = Shopmate\Shops::getUserShop();

		$referenceFields = array(
			"SUMM" => new Entity\ExpressionField("SUMM", "IFNULL(PRICE, 0) + IFNULL(PRICE_GRAY, 0)"), 
			"REASON" => new Entity\ExpressionField("REASON", 
				"(CASE %s 
					WHEN \"report\" 
						THEN %s
					WHEN \"contractor\" 
						THEN CONCAT(\"".Loc::getMessage("OVERHEAD")." \", %s, \" ".Loc::getMessage("FROM")." \", %s)
					WHEN \"payroll\" 
						THEN %s 
					WHEN \"deposit\" 
						THEN  CONCAT(\"".Loc::getMessage("DEPOSIT").". \", IFNULL(%s, \"\"))
					ELSE \"-\" 
				END)", 
				array("OUTGO", "REPORT_TYPE.VALUE", "CONTRACTOR_TYPE.NUMBER_DOCUMENT", "CONTRACTOR_TYPE.DOC.DATE_DOCUMENT", "PAYROLL_TYPE.VALUE", "DESCRIPTION")
			),
			"MEMBERS" => new Entity\ExpressionField("MEMBERS", 
				"(CASE %s 
					WHEN \"report\" 
						THEN CONCAT(%s, \"/\", %s)
					WHEN \"contractor\" 
						THEN CONCAT(IF(%s=2, CONCAT(%s, \" (\", %s, \")\"), %s), \" \", IFNULL(%s, \"\"))
					WHEN \"payroll\" 
						THEN CONCAT(IFNULL(%s, \"\"), \" [\", IFNULL(%s, \"\"), \"]\")
					WHEN \"deposit\" 
						THEN  \"-\"
					ELSE \"-\" 
				END)", 
				array("OUTGO", "REPORT_ID.SUBDIR", "REPORT_ID.FILE_NAME", "CONTRACTOR_ID.PERSON_TYPE", "CONTRACTOR_ID.COMPANY", "CONTRACTOR_ID.PERSON_NAME", "CONTRACTOR_ID.PERSON_NAME", "CONTRACTOR_ID.PHONE", "PAYROLL_ID.NAME", "PAYROLL_ID.EMAIL")
			),
		);

		$parameters["runtime"][] = new Entity\ReferenceField(
			'REPORT_TYPE',
			'Yadadya\Shopmate\Internals\FinanceMoneyCat',
			array('=ref.ID' => 'this.ITEM_TYPE'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'REPORT_ID',
			'Bitrix\Main\File',
			array('=ref.ID' => 'this.ITEM_ID'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'CONTRACTOR_TYPE',
			'Yadadya\Shopmate\Internals\StoreDocs',
			array('=ref.DOC_ID' => 'this.ITEM_TYPE'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'CONTRACTOR_ID',
			'Yadadya\Shopmate\BitrixInternals\Contractor',
			array('=ref.ID' => 'this.ITEM_ID'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'PAYROLL_TYPE',
			'Yadadya\Shopmate\Internals\FinanceMoneyCat',
			array('=ref.ID' => 'this.ITEM_TYPE'),
			array('join_type' => 'LEFT')
		);
		$parameters["runtime"][] = new Entity\ReferenceField(
			'PAYROLL_ID',
			'Bitrix\Main\User',
			array('=ref.ID' => 'this.ITEM_ID'),
			array('join_type' => 'LEFT')
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["count_total"] = false;

		return Shopmate\Internals\FinanceMoneyTable::getList($parameters);
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = parent::checkFilterRequest($filter);

		$arFilter = $filter;

		return $arFilter;
	}

	public function getByID($primary = 0)
	{
		$parameters = array("filter" => array("ID" => $primary, "STORE_ID" => Shopmate\Shops::getUserShop()));
		return Shopmate\Internals\FinanceMoneyTable::getList($parameters)->fetch();
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

		if($result->isSuccess() && ($data["PRICE"] != 0 || $data["PRICE_GRAY"] != 0))
		{
			if($data["OUTGO"] == "report" && !empty($_FILES["ITEM_ID"]))
			{
				$ITEM_FILE = $_FILES["ITEM_ID"];
				$ITEM_FILE["old_file"] = $data["ITEM_ID"];
				//$ITEM_FILE["del"] = $data["ITEM_ID_del"];
				$arIMAGE["MODULE_ID"] = "yadadya.shopmate";
				if (strlen($ITEM_FILE["name"])>0 || strlen($ITEM_FILE["del"])>0) 
					$data["ITEM_ID"] = \CFile::SaveFile($ITEM_FILE, "finance_money_report");
			}

			if($primary > 0)
			{
				$res = Shopmate\Internals\FinanceMoneyTable::update($primary, $data);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
			}
			else
			{
				global $USER;
				$data["USER_ID"] = $USER->GetID();
				$data["STORE_ID"] = Shopmate\Shops::getUserShop();
				$data["DATE"] = new \Bitrix\Main\Type\DateTime();
				$res = Shopmate\Internals\FinanceMoneyTable::add($data);
				if(!$res->isSuccess())
					$result->addErrors($res->getErrors());
				else
					$primary = $res->GetID();
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
		
		$result = Shopmate\Internals\FinanceMoneyTable::delete($primary);

		return $result;
	}

	public static function getReportCategoryList()
	{
		$arResult = array();
		$res = \Yadadya\Shopmate\Internals\FinanceMoneyCatTable::getList(array(
			"select" => array("ID", "VALUE"),
			"order" => array("VALUE" => "ASC"),
			"filter" => array("OUTGO" => "report")
		));
		while($row = $res->Fetch())
			$arResult[$row["ID"]] = $row["VALUE"];
		return $arResult;
	}

	public static function getPayrollCategoryList()
	{
		$arResult = array();
		$res = \Yadadya\Shopmate\Internals\FinanceMoneyCatTable::getList(array(
			"select" => array("ID", "VALUE"),
			"order" => array("VALUE" => "ASC"),
			"filter" => array("OUTGO" => "payroll")
		));
		while($row = $res->Fetch())
			$arResult[$row["ID"]] = $row["VALUE"];
		return $arResult;
	}
}