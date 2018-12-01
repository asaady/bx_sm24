<?php
namespace Yadadya\Shopmate\Internals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);

class SettingsChapterTable extends DataManager
{
	public static function getTableName()
	{
		return "b_sm_settings_chapter";
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'PARENT_ID' => array(
				'data_type' => 'integer',
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
			),
			'STRING_ID' => array(
				'data_type' => 'string',
				'required' => true,
			),
			'PARENT' => new \Bitrix\Main\Entity\ReferenceField(
				'PARENT',
				'Yadadya\Shopmate\Internals\SettingsChapter',
				array('=this.PARENT_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			)
		);
	}
}
