<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use \Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ContractorTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_contractor";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'CONTRACTOR_ID' => array(
				'data_type' => 'integer',
			),
			'BIK' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 145),
					);
				}
			),
			'OGRN' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 145),
					);
				}
			),
			'TAX_TYPE' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 145),
					);
				}
			),
			'NDS' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 145),
					);
				}
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
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 145),
					);
				}
			),
			'CONTRACT_DATE' => array(
				'data_type' => 'datetime'
			),
			'DELAY' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 100),
					);
				}
			),
			'ADDRESS_FACT' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
			),
			'NOTES' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
			),
			'PRODUCTION' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
			),
			'DISCOUNT' => array(
				'data_type' => 'string',
				'validation' => function() {
					return array(
						new Entity\Validator\Length(null, 100),
					);
				}
			),
		);
	}
}