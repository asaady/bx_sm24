<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class ContractorTable extends DataManager
{
	public static function getTableName()
	{
		return "b_catalog_contractor";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'PERSON_TYPE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 1),
					);
				}
			),
			'PERSON_NAME' => array(
				'data_type' => 'string',
			),
			'PERSON_LASTNAME' => array(
				'data_type' => 'string',
			),
			'PERSON_MIDDLENAME' => array(
				'data_type' => 'string',
			),
			'EMAIL' => array(
				'data_type' => 'string',
			),
			'PHONE' => array(
				'data_type' => 'string',
			),
			'POST_INDEX' => array(
				'data_type' => 'string',
			),
			'COUNTRY' => array(
				'data_type' => 'string',
			),
			'CITY' => array(
				'data_type' => 'string',
			),
			'COMPANY' => array(
				'data_type' => 'string',
			),
			'ADDRESS' => array(
				'data_type' => 'string',
			),
			'INN' => array(
				'data_type' => 'string',
			),
			'KPP' => array(
				'data_type' => 'string',
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime',
			),
			'DATE_MODIFY' => array(
				'data_type' => 'datetime',
			),
			'CREATED_BY' => array(
				'data_type' => 'integer'
			),
			'MODIFIED_BY' => array(
				'data_type' => 'integer',
			),
		);
	}
}