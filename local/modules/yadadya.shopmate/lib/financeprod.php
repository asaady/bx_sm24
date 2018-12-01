<?php
namespace Yadadya\Shopmate;

use Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceProd
{
	public function getAmountList(array $parameters = array())
	{
		return Internals\ProdAmountTable::getList($parameters);
	}
	public function getPriceList(array $parameters = array())
	{
		return Internals\ProdPriceTable::getList($parameters);
	}

	public function getAvgPriceList(array $filter = array())
	{
		$parameters["select"] = array("PRODUCT_ID", "STORE_ID", "CATALOG_GROUP_ID", "PRICE_AVG" => new Entity\ExpressionField("PRICE_AVG", "AVG(PRICE)"));
		$parameters["filter"] = $filter;
		$parameters["filter"]["!CURRENCY"] = false;
		$parameters["group"] = array("PRODUCT_ID", "STORE_ID", "CATALOG_GROUP_ID", "CURRENCY");
		return Internals\ProdPriceTable::getList($parameters);
	}
}
