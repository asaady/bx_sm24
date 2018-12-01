<? 
namespace Yadadya\Shopmate\BitrixInternals;
class SectionTable extends \Bitrix\Iblock\SectionTable 
{
	public static function getMap()
	{
		$arMap = parent::getMap();
		$arMap['TIMESTAMP_X'] = array(
			'data_type' => 'datetime',
			'default_value' => new \Bitrix\Main\Type\DateTime(),
			'default' => 'CURRENT_TIMESTAMP',
			'required' => true
		);
		return $arMap;
	}
} 
?>