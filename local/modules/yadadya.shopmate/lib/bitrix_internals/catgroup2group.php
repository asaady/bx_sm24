<?php
namespace Yadadya\Shopmate\BitrixInternals;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CatGroup2GroupTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_catalog_group2group';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => new Main\Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
			)),
			'CATALOG_GROUP_ID' => new Main\Entity\IntegerField('CATALOG_GROUP_ID', array(
				'required' => true,
			)),
			'GROUP_ID' => new Main\Entity\IntegerField('GROUP_ID', array(
				'required' => true,
			)),
			'BUY' => new Main\Entity\BooleanField('BUY', array(
				'values' => array('N', 'Y'),
			)),
		);
	}
}