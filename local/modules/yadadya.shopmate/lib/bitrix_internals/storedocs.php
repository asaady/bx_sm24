<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StoreDocsTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return "b_catalog_store_docs";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
			),
			'DOC_TYPE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 2),
					);
				}
			),
			'CONTRACTOR_ID' => array(
				'data_type' => 'integer',
			),
			'DATE_MODIFY' => array(
				'data_type' => 'datetime',
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime',
			),
			'CREATED_BY' => array(
				'data_type' => 'integer',
			),
			'MODIFIED_BY' => array(
				'data_type' => 'integer',
			),
			'CURRENCY' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 3),
					);
				}
			),
			'STATUS' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'DATE_STATUS' => array(
				'data_type' => 'datetime',
			),
			'DATE_DOCUMENT' => array(
				'data_type' => 'datetime',
			),
			'STATUS_BY' => array(
				'data_type' => 'integer',
			),
			'TOTAL' => array(
				'data_type' => 'float',
			),
			'COMMENTARY' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1000),
					);
				}
			),
		);
	}
}