<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StoreDocsElementTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_docs_element";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'DOCS_ELEMENT_ID' => array(
				'data_type' => 'integer',
				'required' => true
			),
			'DOC_AMOUNT' => array(
				'data_type' => 'float'
			),
			'SHOP_PRICE' => array(
				'data_type' => 'float'
			),
			'END_DATE' => array(
				'data_type' => 'datetime',
			),
			'PURCHASING_NDS' => array(
				'data_type' => 'float',
			),
			'DOCS_ELEMENT' => new \Bitrix\Main\Entity\ReferenceField(
				'DOCS_ELEMENT',
				'Yadadya\Shopmate\BitrixInternals\StoreDocsElement',
				array('=this.DOCS_ELEMENT_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			)
		);
	}
}