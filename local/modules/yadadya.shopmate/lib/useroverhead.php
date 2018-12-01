<?
namespace Yadadya\Shopmate;
use \Bitrix\Main\Localization\Loc;

/*use \Bitrix\Catalog,
	\Bitrix\Main\UserTable,
	\Bitrix\Main\GroupTable;*/

Loc::loadMessages(__FILE__);

class UserOverhead
{
	public static function loadByID($fileid = 0)
	{
		if ($filename = \CFile::GetPath($fileid))
			return self::loadXML($_SERVER["DOCUMENT_ROOT"].$filename);
		return false;
	}

	public static function loadXML($filename = "")
	{
		if ($data = file_get_contents($filename))
			return self::loadString($data);
		return false;
	}

	public static function loadString($data = "")
	{
		if ($xml = simplexml_load_string($data, "\Yadadya\Shopmate\XMLElement"))
		{
			$arData = array();
			$xml_attr = $xml->attrArray();
			$verProg = $xml_attr['ВерсПрог'];
			$verForm = $xml_attr['ВерсФорм'];

			if ($verProg == "1С:Предприятие 8" && $verForm == "5.01")
			{
				$overhead_attr = $xml->{'Документ'}->{'СвТНО'}->{'ТН'}->attrArray();
				$arData["OVERHEAD"] = array(
					"DOCUMENT_NUMBER" => $overhead_attr['НомТН'],
					"DOCUMENT_DATE" => $overhead_attr['ДатаТН'],
				);

				$contractor_attr = $xml->{'Документ'}->{'СвТНО'}->{'Поставщик'}->{'ИдСв'}->{'СвЮЛ'}->attrArray();
				$address = array();
				foreach ($xml->{'Документ'}->{'СвТНО'}->{'Поставщик'}->{'Адрес'}->{'АдрРФ'}->attrArray() as $key => $val)
					$address[] = $key.": ".$val;
				$arData["CONTRACTOR"] = array(
					"NAME" => $contractor_attr['НаимОрг'],
					"INN" => $contractor_attr['ИННЮЛ'],
					"KPP" => $contractor_attr['КПП'],
					"ADDRESS" => implode($address, "; ")
				);

				$store_attr = $xml->{'Документ'}->{'СвТНО'}->{'Плательщик'}->{'ИдСв'}->{'СвЮЛ'}->attrArray();
				$address = array();
				foreach ($xml->{'Документ'}->{'СвТНО'}->{'Плательщик'}->{'Адрес'}->{'АдрРФ'}->attrArray() as $key => $val)
					$address[] = $key.": ".$val;
				$arData["STORE"] = array(
					"NAME" => $store_attr['НаимОрг'],
					"INN" => $store_attr['ИННЮЛ'],
					"KPP" => $store_attr['КПП'],
					"ADDRESS" => implode($address, "; ")
				);

				$arData["ITEMS"] = array();
				foreach ($xml->{'Документ'}->{'СвТНО'}->{'ТН'}->{'Таблица'}->{'СвТов'} as $xml_item)
				{
					$item_attr = $xml_item->attrArray();
					$arData["ITEMS"][] = array(
						"SORT" => $item_attr['НомТов'],
						"NAME" => $item_attr['НаимТов'],
						"1C_ARTICUL" => $item_attr['АртикулТов'],
						"1C_CODE" => $item_attr['КодТов'],
						"AMOUNT" => $item_attr['Нетто'],
						"MEASURE" => $item_attr['НаимЕдИзм'],
						"PRICE" => $item_attr['Цена'],
						"SUMM" => $item_attr['СумБезНДС'],
						"NDS" => $item_attr['СтавкаНДС'],
						"NDS_VALUE" => $item_attr['СумНДС'],
						"SUMM_NDS" => $item_attr['СумУчНДС'],
					);
				}
			}

			return $arData;
		}
		return false;
	}

	public function getNewCnt()
	{
		$cnt = 0;
		global $USER;
		$res = Internals\UserOverheadTable::getList(array(
			"select" => array("CNT"), 
			"filter" => array(
				"ACTIVE" => "Y",
				array(
					"LOGIC" => "OR",
					"USER_ID" => $USER->getId(),
					"STORE_ID" => Shops::getUserStore()
				)
			), 
			"runtime" => array(new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'))));
		if ($row = $res->fetch()) 
			$cnt = $row["CNT"];
		return $cnt;
	}
}