<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class UserOverheadTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_user_overhead";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'DATE' => array(
				'data_type' => 'datetime'
			),
			'USER_ID' => array(
				'data_type' => 'integer'
			),
			'STORE_ID' => array(
				'data_type' => 'integer'
			),
			'OVERHEAD_ID' => array(
				'data_type' => 'integer'
			),
			'XML' => array(
				'data_type' => 'integer'
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'STATUS' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				},
				'values' => array('N', 'A', 'C', 'R'), // none, accepted, changed, rejected
				'default_value' => 'N',
			),
			'ACCEPTED' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				},
				'values' => array('N', 'Y'),
				'default_value' => 'N',
			),
			'USER' => new \Bitrix\Main\Entity\ReferenceField(
				'USER',
				'Yadadya\Shopmate\BitrixInternals\User',
				array('=this.USER_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'STORE' => new \Bitrix\Main\Entity\ReferenceField(
				'STORE',
				'Yadadya\Shopmate\BitrixInternals\Store',
				array('=this.STORE_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			)
		);
	}
}
