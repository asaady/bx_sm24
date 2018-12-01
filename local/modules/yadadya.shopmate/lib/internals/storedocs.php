<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class StoreDocsTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_store_docs";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'DOC_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'NUMBER_DOCUMENT' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1000),
					);
				},
				'required' => true
			),
			'TOTAL_FACT' => array(
				'data_type' => 'float',
			),
			'USER_ID' => array(
				'data_type' => 'integer',
			),
			'DOC' => new \Bitrix\Main\Entity\ReferenceField(
				'DOC',
				'Yadadya\Shopmate\BitrixInternals\StoreDocs',
				array('=this.DOC_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'USER' => new \Bitrix\Main\Entity\ReferenceField(
				'USER',
				'Yadadya\Shopmate\BitrixInternals\User',
				array('=this.USER_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
		);
	}
}