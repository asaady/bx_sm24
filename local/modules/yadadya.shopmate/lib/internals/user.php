<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class UserTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_user";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'autocomplete' => true,
				'primary' => true,
			),
			'USER_ID' => array(
				'data_type' => 'integer',
			),
			'USER_TYPE' => array(
				'data_type' => 'string',
			),
			'PERSON_TYPE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'CORPORATE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'INN' => array(
				'data_type' => 'string',
			),
			'BIK' => array(
				'data_type' => 'string',
			),
			'OGRN' => array(
				'data_type' => 'string',
			),
			'TAX_TYPE' => array(
				'data_type' => 'string',
			),
			'REGULAR' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'CONTRACT' => array(
				'data_type' => 'string',
			),
			'CONTRACT_DATE' => array(
				'data_type' => 'datetime',
			),
			'DELAY' => array(
				'data_type' => 'string',
			),
			'NOTES' => array(
				'data_type' => 'string',
			),
			'DEPARTMENT_ID' => array(
				'data_type' => 'integer',
			),
			'POSITION_ID' => array(
				'data_type' => 'integer',
			),
			'START_DATE' => array(
				'data_type' => 'datetime',
			),
			'SALARY' => array(
				'data_type' => 'float',
			),
			'RATE' => array(
				'data_type' => 'float',
			),
			'RATE_SECTIONS' => array(
				'data_type' => 'string',
			),
			'USERGROUP' => new \Bitrix\Main\Entity\ReferenceField(
				'USERGROUP',
				'Yadadya\Shopmate\BitrixInternals\UserGroup',
				array('=this.USER_ID' => 'ref.USER_ID'),
				array('join_type' => 'LEFT')
			),
			'DEPARTMENT' => new \Bitrix\Main\Entity\ReferenceField(
				'DEPARTMENT',
				'Yadadya\Shopmate\Internals\PersonalDepartment',
				array('=this.DEPARTMENT_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'POSITION' => new \Bitrix\Main\Entity\ReferenceField(
				'POSITION',
				'Yadadya\Shopmate\Internals\PersonalPosition',
				array('=this.POSITION_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
		);
	}
}