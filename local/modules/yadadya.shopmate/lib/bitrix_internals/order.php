<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class OrderTable extends \Bitrix\Sale\Internals\OrderTable 
{
	public static function getMap()
	{
		return parent::getMap() + array('CUSER' => new \Bitrix\Main\Entity\ReferenceField(
				'CUSER',
				'\Yadadya\Shopmate\BitrixInternals\User',
				array('=this.USER_ID' => 'ref.ID'),
				array('join_type' => 'INNER')
			));
	}
}