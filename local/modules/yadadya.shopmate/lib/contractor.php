<?
namespace Yadadya\Shopmate;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Contractor
{
	public static function getByINN($inn = "")
	{
		$arResult = getByINN($inn);
		return array(
			"PERSON_NAME" => $arResult["NAME"],
			"COMPANY" => $arResult["COMPANY"],
			"ADDRESS" => $arResult["ADDRESS"],
			"OGRN" => $arResult["OGRN"],
		);
	}
}