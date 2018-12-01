<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class SettingsRightTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_settings_right";
	}

	public static function getMap()
	{
		return array(
			'GROUP_ID' => array(
				'data_type' => 'integer',
				'primary' => true,
			),
			'CHAPTER_ID' => array(
				'data_type' => 'integer',
				'primary' => true,
			),
			'RIGHT' => array(
				'data_type' => 'string',
			),
			'GROUP' => new \Bitrix\Main\Entity\ReferenceField(
				'GROUP',
				'Yadadya\Shopmate\Internals\SettingsGroup',
				array('=this.GROUP_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
			'CHAPTER' => new \Bitrix\Main\Entity\ReferenceField(
				'CHAPTER',
				'Yadadya\Shopmate\Internals\ChapterGroup',
				array('=this.CHAPTER_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			),
		);
	}
}
