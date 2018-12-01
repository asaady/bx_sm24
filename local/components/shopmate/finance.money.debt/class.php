<? class CFinanceMoneyDebtComponent extends \Yadadya\Shopmate\Components\Component 
{
	protected static $currentFields = array("ID", "MEMBERS", "SUMM", "REASON", "TOTAL_SUMM", "DATE", "ITEM_TYPE", "ITEM_ID");

	public function getList(array $parameters = array())
	{
		unset($parameters["filter"]["OUTGO"]);

		$q = new \Bitrix\Main\Entity\Query(\Yadadya\Shopmate\Internals\FinanceMoneyTable::getEntity());
		$q->setOrder(array("DATE"=>"DESC"));
		$q->setSelect(array("DATE"));
		$q->setFilter(array("ITEM_TYPE" => new \Bitrix\Main\DB\SqlExpression("%s")));
		$q->setLimit(1);
		$dateSelect = $q->getQuery();

		$referenceFields = array(
			"MEMBERS" => new \Bitrix\Main\Entity\ExpressionField("MEMBERS", "CONCAT(IF(%s=2, CONCAT(IFNULL(%s, \"\"), \" (\", IFNULL(%s, \"\"), \")\"), IFNULL(%s, \"\")), \" \", IFNULL(%s, \"\"))", array("CONTRACTOR.PERSON_TYPE", "CONTRACTOR.COMPANY", "CONTRACTOR.PERSON_NAME", "CONTRACTOR.PERSON_NAME", "CONTRACTOR.PHONE")),
			"SUMM" => new \Bitrix\Main\Entity\ExpressionField("SUMM", "IFNULL(%s, 0) - IFNULL(%s, 0)", array("TOTAL", "SMSTORE_DOCS.TOTAL_FACT")),
			"REASON" => new \Bitrix\Main\Entity\ExpressionField("REASON", "CONCAT(\"".\Bitrix\Main\Localization\Loc::getMessage("OVERHEAD")." \", %s, \" ".\Bitrix\Main\Localization\Loc::getMessage("FROM")." \", %s)", array("SMSTORE_DOCS.NUMBER_DOCUMENT", "DATE_DOCUMENT")),
			"TOTAL_SUMM" => new \Bitrix\Main\Entity\ExpressionField("TOTAL_SUMM", "IFNULL(%s, 0)", array("TOTAL")),
			"DATE" => new \Bitrix\Main\Entity\ExpressionField("DATE", "IFNULL((".$dateSelect."), \"-\")", array("ID")),
			"ITEM_TYPE" => "ID",
			"ITEM_ID" => "CONTRACTOR_ID"
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["filter"][">CONTRACTOR_ID"] = 0;

		$parameters["runtime"][] = new \Bitrix\Main\Entity\ReferenceField(
			'SMSTORE_DOCS',
			'Yadadya\Shopmate\Internals\StoreDocs',
			array('=ref.DOC_ID' => 'this.ID', '<ref.TOTAL_FACT' => 'this.TOTAL'),
			array('join_type' => 'INNER')
		);

		$parameters["runtime"][] = new \Bitrix\Main\Entity\ReferenceField(
			'CONTRACTOR',
			'Yadadya\Shopmate\BitrixInternals\Contractor',
			array('=ref.ID' => 'this.CONTRACTOR_ID'),
			array('join_type' => 'LEFT')
		);

		return \Yadadya\Shopmate\BitrixInternals\StoreDocsTable::GetList($parameters);
	}

	public function getInit()
	{
		return array(
			"object" => new \Yadadya\Shopmate\Components\FinanceMoney(),
			"type" => "list"
		);
	}
} 
?>