<?
namespace Yadadya\Shopmate;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Client
{
	public static function getByINN($inn = "")
	{
		$arResult = getByINN($inn);
		return array(
			"NAME" => $arResult["NAME"],
			"WORK_COMPANY" => $arResult["COMPANY"],
			"WORK_STREET" => $arResult["ADDRESS"],
			"OGRN" => $arResult["OGRN"],
		);
	}
}