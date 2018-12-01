<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class UserTable extends \Bitrix\Main\UserTable
{
	public static function getMap()
	{
		return parent::getMap() + array('SMUSER' => new \Bitrix\Main\Entity\ReferenceField(
				'SMUSER',
				'Yadadya\Shopmate\Internals\User',
				array('=this.ID' => 'ref.USER_ID'),
				array('join_type' => 'LEFT')
			));
	}
}