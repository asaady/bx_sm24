<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StoreDocsElementTable extends DataManager
{
	public static function getTableName()
	{
		return "b_catalog_docs_element";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
			),
			'DOC_ID' => array(
				'data_type' => 'integer',
			),
			'STORE_FROM' => array(
				'data_type' => 'integer',
			),
			'STORE_TO' => array(
				'data_type' => 'integer',
			),
			'ELEMENT_ID' => array(
				'data_type' => 'integer',
			),
			'AMOUNT' => array(
				'data_type' => 'float'
			),
			'PURCHASING_PRICE' => array(
				'data_type' => 'float',
			),
			'DOC' => new \Bitrix\Main\Entity\ReferenceField(
				'DOC',
				'Yadadya\Shopmate\BitrixInternals\StoreDocs',
				array('=this.DOC_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'SMDOCS_ELEMENT' => new \Bitrix\Main\Entity\ReferenceField(
				'SMDOCS_ELEMENT',
				'Yadadya\Shopmate\Internals\StoreDocsElement',
				array('=this.ID' => 'ref.DOCS_ELEMENT_ID'),
				array('join_type' => 'LEFT')
			)
		);
	}
}