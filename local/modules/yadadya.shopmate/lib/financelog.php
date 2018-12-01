<?php
namespace Yadadya\Shopmate;

use Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceLog
{
	public function getLog(array $parameters = array())
	{
		if(empty($parameters["filter"]["ACTIVE"])) $parameters["filter"]["ACTIVE"] = "Y";
		return Internals\FinanceLogTable::getList($parameters);
	}
}
