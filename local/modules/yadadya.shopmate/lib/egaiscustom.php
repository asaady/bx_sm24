<?php
namespace Yadadya\Shopmate\Egais;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class EgaisCustom
{
	private static $save_dir = "/upload/egais/";

	private static function delListFile($store_id)
	{
		if(!file_exists($_SERVER["DOCUMENT_ROOT"].self::$save_dir)) mkdir($_SERVER["DOCUMENT_ROOT"].self::$save_dir, 0777);
		return $_SERVER["DOCUMENT_ROOT"].self::$save_dir."del_opt_".$store_id.".txt";
	}

	private static function removeListFile($store_id)
	{
		unlink(self::delListFile($store_id));
	}

	private static function delListXML($store_id)
	{
		if(!file_exists($_SERVER["DOCUMENT_ROOT"].self::$save_dir)) mkdir($_SERVER["DOCUMENT_ROOT"].self::$save_dir, 0777);
		return $_SERVER["DOCUMENT_ROOT"].self::$save_dir."del_opt_".$store_id.".xml";
	}

	public static function removeDelXML($store_id)
	{
		unlink(self::delListXML($store_id));
	}

	public static function addDelList($store_id, $url)
	{
		file_put_contents(self::delListFile($store_id), $url."\n", FILE_APPEND);
	}

	public static function refreshDel($store_id)
	{
		$store_id = IntVal($store_id);
		self::removeListFile($store_id);
		if($store_id > 0)
		{
			$res = EgaisTable::getList(array(
				"select" => array("ID", "STORE_ID", "URL", "XML"),
				"filter" => array("!URL" => false, "!XML" => false, "URL" => "http%", "STORE_ID" => $store_id),
			));
			while($row = $res->fetch())
				self::onUpdateOpt($row["ID"]);
		}
	}

	public static function onUpdateOpt($id, $row)
	{
		if(!isset($row["STORE_ID"]) || !isset($row["URL"]) || !isset($row["XML"]))
			$row = EgaisTable::getRowById($id);
		if(stripos($row["URL"], "http") === 0 && $row["STORE_ID"] > 0 && !empty($row["XML"]))
		{
			self::addDelList($row["STORE_ID"], $row["URL"]);
		}
	}

	public static function getDelXML($store_id)
	{
		/*if(!file_exists(self::delListFile($store_id)))
			self::refreshDel($store_id);*/
		$xml_docs = "";
		if(!file_exists(self::delListXML($store_id)))
		{
			file_put_contents(self::delListXML($store_id), "<A>"."\n", FILE_APPEND);
			$urls = file(self::delListFile($store_id), FILE_IGNORE_NEW_LINES);
			if(is_array($urls))
				foreach($urls as $url)
					if(!empty($url))
						file_put_contents(self::delListXML($store_id), "<url>".$url."</url>"."\n", FILE_APPEND);
			file_put_contents(self::delListXML($store_id), "</A>", FILE_APPEND);
			self::removeListFile($store_id);
		}
		return file_get_contents(self::delListXML($store_id));
	}

	public static function getDelList($store_id)
	{
		return file(self::delListFile($store_id), FILE_IGNORE_NEW_LINES);
	}
}