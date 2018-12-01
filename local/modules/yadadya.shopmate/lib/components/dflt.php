<?
namespace Yadadya\Shopmate\Components;

/*use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\DB;
use Yadadya\Shopmate;
use Yadadya\Shopmate\Internals;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;

Loc::loadMessages(__FILE__);*/

class Dflt extends Base
{
	protected static $currentFields = array();

	public function getList(array $parameters = array())
	{
		return new \Bitrix\Main\DB\ArrayResult(array());
	}

	public function getByID($primary = 0)
	{
		return array();
	}
}