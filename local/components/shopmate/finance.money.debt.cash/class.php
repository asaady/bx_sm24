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
			"MEMBERS" => new \Bitrix\Main\Entity\ExpressionField("MEMBERS", 
				"(CASE %s 
					WHEN \"2\" 
						THEN CONCAT(%s, \" (\", %s, \")\")
					ELSE %s 
				END)", 
				array("CUSER.SMUSER.PERSON_TYPE", "CUSER.WORK_COMPANY", "CUSER.NAME", "CUSER.NAME")
			),
			"SUMM" => new \Bitrix\Main\Entity\ExpressionField("SUMM", "IFNULL(%s, 0) - IFNULL(%s, 0)", array("PRICE", "SUM_PAID")),
			"REASON" => new \Bitrix\Main\Entity\ExpressionField("REASON", "CONCAT(\"".\Bitrix\Main\Localization\Loc::getMessage("CASH_CHECK")." \", %s, \" ".\Bitrix\Main\Localization\Loc::getMessage("FROM")." \", %s)", array("ACCOUNT_NUMBER", "DATE_INSERT")),
			"TOTAL_SUMM" => new \Bitrix\Main\Entity\ExpressionField("TOTAL_SUMM", "IFNULL(%s, 0)", array("PRICE")),
			"DATE" => new \Bitrix\Main\Entity\ExpressionField("DATE", "IFNULL((".$dateSelect."), \"-\")", array("ID")),
			"ITEM_TYPE" => "ID",
			"ITEM_ID" => "USER_ID"
		);

		$parameters["select"] = parent::getSelect($parameters["select"], self::$currentFields, $referenceFields);

		$parameters["filter"][">USER_ID"] = 0;
		$parameters["filter"]["CUSER.SMUSER.CORPORATE"] = "Y";
		$parameters["filter"]["!PAYED"] = "Y";

		return \Yadadya\Shopmate\Components\Cash::getList($parameters);
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