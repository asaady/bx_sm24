<? class CFinanceMoneyBalanceComponent extends \Yadadya\Shopmate\Components\Component 
{
	protected static $propList = array(
		"CASH_DEPOSIT" => array("DISABLED" => "Y"),
		"CLEARING_DEPOSIT" => array("DISABLED" => "Y"),
		"CLIENTS_DEBT" => array("DISABLED" => "Y"),
		"CONTRACTORS_PREPAYMENT" => array("DISABLED" => "Y"),
		"CONTRACTORS_CREDIT" => array("DISABLED" => "Y"),
		"SUMM" => array("DISABLED" => "Y"),
	);

	public function getProps()
	{
		\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
		return \Yadadya\Shopmate\Components\Base::setInputLangTitle(static::$propList);
	}

	public function getById($primary = 0)
	{
		return array(
			"CASH_DEPOSIT" => 0,
			"CLEARING_DEPOSIT" => 0,
			"CLIENTS_DEBT" => 0,
			"CONTRACTORS_PREPAYMENT" => 0,
			"CONTRACTORS_CREDIT" => 0,
			"SUMM" => 0,
		);
	}
	
	public function getInit()
	{
		return array(
			"object" => new \Yadadya\Shopmate\Components\FinanceMoney(),
			"type" => "detail"
		);
	}
} 
?>