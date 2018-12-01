<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;
use Bitrix\Main\Entity;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class FinanceLogTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_finance_log";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'column_type' => 'int(18)',
				'primary' => true,
				'autocomplete' => true,
			),
			'TIMESTAMP_X' => array(
				'data_type' => 'datetime',
				'column_type' => 'timestamp',
				'default_value' => new Type\DateTime(),
				'default' => 'CURRENT_TIMESTAMP',
				'required' => true
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
				'column_type' => 'int(18)',
				'required' => true
			),
			'TYPE' => array(
				'data_type' => 'string',
				'values' => array('IN', 'OUT'),
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 50),
					);
				},
				'required' => true
			),
			'TRANSACTION' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 50),
					);
				},
				'required' => true
			),
			'ITEM_ID' => array(
				'data_type' => 'string',
				'required' => true
			),
			'REMOTE_ADDR' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 40),
					);
				},
			),
			'USER_AGENT' => array(
				'data_type' => 'text'
			),
			'REQUEST_URI' => array(
				'data_type' => 'text'
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 2),
					);
				},
				'column_type' => 'char(2)',
			),
			'USER_ID' => array(
				'data_type' => 'integer',
				'column_type' => 'int(18)',
			),
			'GUEST_ID' => array(
				'data_type' => 'integer',
				'column_type' => 'int(18)',
			),
			'DESCRIPTION' => array(
				'data_type' => 'text',
				'column_type' => 'mediumtext',
			),
			'PRICE' => array(
				'data_type' => 'float',
				'column_type' => 'decimal(18,2)',
				'required' => true
			),
			'CURRENCY' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 3),
					);
				},
				'column_type' => 'char(3)',
				'required' => true
			),
			'CREDIT' => array(
				'data_type' => 'string',
				'values' => array('Y', 'N'),
				'default_value' => 'N',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				},
				'required' => true
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'values' => array('Y', 'N'),
				'default_value' => 'Y',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				},
				'required' => true
			),
		);
	}

	public static function add(array $data)
	{
		$result = new Main\Entity\AddResult();
		$result->addError(new Main\Entity\EntityError(
			Loc::getMessage('ELEMENT_ENTITY_MESS_ADD_BLOCKED')
		));
		return $result;
	}

	public static function update($primary, array $data)
	{
		$result = new Main\Entity\UpdateResult();
		$result->addError(new Main\Entity\EntityError(
			Loc::getMessage('ELEMENT_ENTITY_MESS_UPDATE_BLOCKED')
		));
		return $result;
	}

	public static function delete($primary)
	{
		$result = new Main\Entity\DeleteResult();
		$result->addError(new Main\Entity\EntityError(
			Loc::getMessage('ELEMENT_ENTITY_MESS_DELETE_BLOCKED')
		));
		return $result;
	}
}
