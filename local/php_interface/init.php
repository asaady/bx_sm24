<?
AddEventHandler("main", "OnProlog", "redirectFirstMenu");
function redirectFirstMenu()
{
	global $USER;
	if ($USER->IsAuthorized() && defined('EMPTY_REDIRECT') && EMPTY_REDIRECT == true)
	{
		global $APPLICATION;
		$dir = $APPLICATION->GetCurDir();
		$menu_tupe = $dir == "/" ? "top" : "left";
		$aMenuLinks = array();
		if (file_exists($_SERVER["DOCUMENT_ROOT"].$dir.".".$menu_tupe.".menu.php"))
			include_once($_SERVER["DOCUMENT_ROOT"].$dir.".".$menu_tupe.".menu.php");
		if (file_exists($_SERVER["DOCUMENT_ROOT"].$dir.".".$menu_tupe.".menu_ext.php"))
			include_once($_SERVER["DOCUMENT_ROOT"].$dir.".".$menu_tupe.".menu_ext.php");
		if (!empty($aMenuLinks) && $aMenuLinks[0][1] != $dir)
			LocalRedirect($aMenuLinks[0][1]);
	}
}

function print_p($val, $name, $die = false, $all = false)
{
	global $USER;
	//if (is_object($USER) && $USER->IsAdmin() || $all)
	{
		echo '<pre>'.(!empty($name) ? $name.': ' : '');print_r($val);echo '</pre>';
	}
	if($die) die;
}
function PriceFormat($price, $count = 0, $currency = "RUB")
{
	if(empty($currency)) $currency = "RUB";
	if(!$count) return CurrencyFormat($price, $currency);
	else return number_format($val, $count, ",", " ");
}
function getWord($number, $suffix)	//getWord(5, array('[1]минута', '[2]минуты', '[5]минут'));
{
	$keys = array(2, 0, 1, 1, 1, 2);
	$mod = $number % 100;
	$suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
	return $suffix[$suffix_key];
}
function ResizeImage($photo, $wi, $hi, $mode, &$arPhoto)	//ResizeImage($arPhoto, 640, 480, true[обрезать]);
{
	if(is_array($photo)) $photo = $photo["ID"];
	if($wi > 0 && $hi > 0)
	{
		$arPhoto = CFile::ResizeImageGet($photo, Array("width" => $wi, "height" => $hi), ($mode ? BX_RESIZE_IMAGE_EXACT : BX_RESIZE_IMAGE_PROPORTIONAL), true);
		$src = $arPhoto["src"];
	}
	else 
	{
		$arPhoto = CFile::GetFileArray($photo);
		$src = $arPhoto["SRC"];
	}
	return $src;
}
function SectionTree($arSections, $SECTION_ID = "", $PARENT_NAME = "IBLOCK_SECTION_ID")
{
	$arTreeList = array();
	foreach($arSections as $arSection)
		if($arSection[$PARENT_NAME] == $SECTION_ID)
		{
			$arSection["SECTIONS"] = SectionTree($arSections, $arSection["ID"]);
			if(!count($arSection["SECTIONS"])) unset($arSection["SECTIONS"]);
			$arTreeList[] = $arSection;
		}
	return $arTreeList;
}
AddEventHandler("main", "OnBuildGlobalMenu", "HideIblock");
function HideIblock(&$aGlobalMenu, &$aModuleMenu)
{
	$arHide = array();
	if(is_array($arHide) && count($arHide) && CModule::IncludeModule("iblock"))
	{
		$res = CIBlock::GetList(Array(), Array("ID" => $arHide));
		$arHide = array();
		while($ar_res = $res->Fetch()) $arHide[$ar_res["ID"]] = $ar_res["IBLOCK_TYPE_ID"];
	}
	if(count($arHide))
		foreach($aModuleMenu as $key1 => $arMenu)
			if($ib = array_search(str_ireplace("menu_iblock_/", "", $arMenu["items_id"]), $arHide))
				foreach($arMenu["items"] as $key2 => $arItem)
					if(in_array(str_ireplace("menu_iblock_/".$arHide[$ib]."/", "", $arItem["items_id"]), array_keys($arHide)))
						unset($aModuleMenu[$key1]["items"][$key2]);
}
AddEventHandler("main", "OnBuildGlobalMenu", "OnceIBlock");
function OnceIBlock(&$aGlobalMenu, &$aModuleMenu)
{
	foreach($aModuleMenu as $keyMenu => &$arMenu)
	{
		if($arMenu["module_id"] == "iblock" && is_array($arMenu["items"]) && count($arMenu["items"]) == 1)
		{
			reset($arMenu["items"]);
			$arIblock = current($arMenu["items"]);
			if($arMenu["text"] != $arIblock["text"]) $arIblock["text"] = $arMenu["text"]." (".$arIblock["text"].")";
			if($arMenu["title"] != $arIblock["title"]) $arIblock["title"] = $arMenu["title"]." (".$arIblock["title"].")";
			$arIblock["more_url"] = array_merge($arMenu["more_url"], $arIblock["more_url"]);
			$arIblock["parent_menu"] = $arMenu["parent_menu"];
			$arIblock["sort"] = $arMenu["sort"];
			$arMenu = $arIblock;
		}

	}
}
function getNextPrevByID($id, $arrSort, $arrFilter) // get the values for the Next and Previous links
{
	$arrSort = is_array($arrSort) && count($arrSort) ? $arrSort : array();
	foreach($arrSort as $sort_by => $sort_order)
		$arrSort[$sort_by] = strtoupper($sort_order) == "ASC" ? "DESC" : "ASC";
	$arrFilter = is_array($arrFilter) && count($arrFilter) ? $arrFilter : array();
	$arReturn = array();
	$res = CIBlockElement::GetByID($id);
	$arResult = $res->GetNext();
	if(isset($arResult["ID"]))
	{
		//WHERE
		$arFilter = array(
			"IBLOCK_ID" => $arResult["IBLOCK_ID"],
			"SECTION_ID" => $arResult["IBLOCK_SECTION_ID"],
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);
		//ORDER BY
		$arSort = array(
			"ID" => "DESC",
		);
		//EXECUTE
		$arReturn["NEXT"] = array();
		$arReturn["PREV"] = array();
		$rsElement = CIBlockElement::GetList(array_merge($arrSort, $arSort), array_merge($arrFilter, $arFilter), false, array("nElementID" => $arResult["ID"], "nPageSize" => 2));
		$end = false;

		while($sElement = $rsElement->GetNextElement())
		{
			$arElement = $sElement->GetFields();
			$arElement["PROPERTIES"] = $sElement->GetProperties();
			if($arElement["ID"]==$arResult["ID"])
			{
				$end = true;
				//$arReturn["CURRENT"]["NO"] = $arElement["RANK"];//???
			}
			elseif($end)
			{
				$arReturn["NEXT"][] = $arElement;
			}
			else
			{
				array_unshift($arReturn["PREV"], $arElement);
			}
		}
	}
	return $arReturn;
}
/*AddEventHandler("main",'OnFileSave','OnFileSave');
function OnFileSave(&$arFile, $fileName, $module)
{
	//сделать проверку на картинку CFile::IsImage($filename, $mime_type=false);
   $arNewFile = CIBlock::ResizePicture($arFile, array("WIDTH" => 1920, "HEIGHT" => 1920, "METHOD" => "resample"));
	if(is_array($arNewFile))
		$arFile = $arNewFile;
	else
		$APPLICATION->throwException("Ошибка масштабирования изображения в свойстве \"Файлы\":".$arNewFile);
}*/
AddEventHandler("main", "OnEndBufferContent", "ChangeMyContent");
function ChangeMyContent(&$content)
{
	GLOBAL $APPLICATION;
	if($APPLICATION->GetCurPage() == "/bitrix/admin/iblock_list_admin.php" && CModule::IncludeModule("iblock") && $_REQUEST["IBLOCK_ID"] && !$_REQUEST["find_section_section"])
	{
		$arIblockFields = CIBlock::GetFields($_REQUEST["IBLOCK_ID"]);
		if($arIblockFields["IBLOCK_SECTION"]["IS_REQUIRED"] == "Y") $content = str_ireplace("id=\"btn_new\"", "id=\"btn_new\" style=\"display:none;\"", $content);
	}
	/*if($APPLICATION->GetCurPage() == "/bitrix/admin/iblock_element_edit.php")
	{
		$ibsection_select = '';
		if (preg_match('/<select[^>]*name="IBLOCK_SECTION\[\]"[^>]*(size="\d*")[^>]*>/', $content, $matches))
		{
			$ibsection_select = $matches[0];
			if(strlen($matches[0]))
				$content = str_ireplace($matches[0], str_replace(array(" multiple", " ".$matches[1]), "", $matches[0]), $content);
		}
	}*/
}
AddEventHandler("main", "OnEpilog", "error_page");
function error_page()
{
	$page_404 = "/404.php";
	GLOBAL $APPLICATION;
	if(strpos($APPLICATION->GetCurPage(), $page_404) === false && defined("ERROR_404") && ERROR_404 == "Y")
	{
		$APPLICATION->RestartBuffer();
		CHTTP::SetStatus("404 Not Found");
		include($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/header.php");
		include($_SERVER["DOCUMENT_ROOT"].$page_404);
		include($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/footer.php");
		die();
	}
}

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "addCode");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "addCode");
AddEventHandler("iblock", "OnBeforeIBlockSectionAdd", "addCode");
AddEventHandler("iblock", "OnBeforeIBlockSectionUpdate", "addCode");
function addCode(&$arFields)
{
    if(!strlen($arFields["CODE"]))
		$arFields["CODE"] = Cutil::translit($arFields["NAME"], "ru", array("replace_space"=>"-","replace_other"=>"-"));
}

function GetVideoUrl($url)
{
	$video_url = "";
	if(stripos($url, "vimeo") !== false)
	{
		$video_id = 0;
		$arQuery = explode("/", parse_url($url, PHP_URL_PATH));
		if(is_array($arQuery) && count($arQuery) > 1) $video_id = (int) $arQuery[1];
		if(!$video_id) $video_id = (int) $url;
		$video_url = "http://player.vimeo.com/video/".$video_id."?title=0&amp;byline=0&amp;portrait=0&amp;color=acbe00";
	}
	elseif(stripos($url, "youtube") !== false)
	{
		$v = '';
		parse_str(parse_url($url, PHP_URL_QUERY));
		if(strlen($v)) $video_url = "http://www.youtube.com/embed/".$v;
	}
	if(strlen($video_url)) return $video_url;
	else return false;
}

function FileFormatSize($size, $precision = 2, $a = array("б", "Кб", "Мб", "Гб", "Тб"))
{
	//CFile::FormatSize();
	$pos = 0;
	while($size >= 1024 && $pos < 4)
	{
		$size /= 1024;
		$pos++;
	}
	return round($size, $precision)." ".$a[$pos];
}

function SetRequest($field = "", $value = "")
{
	if(strlen($field))
	{
		$_POST[$field] = $_GET[$field] = $_REQUEST[$field] = $value;
	}
}

AddEventHandler('iblock', 'OnAfterIBlockElementAdd', array('ElemSectConnect', 'ESUElementUpdate'));
AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', array('ElemSectConnect', 'ESUElementUpdate'));
AddEventHandler('iblock', 'OnBeforeIBlockElementDelete', array('ElemSectConnect', 'ESUElementDelete'));
AddEventHandler('iblock', 'OnAfterIBlockSectionAdd', array('ElemSectConnect', 'ESUSectionUpdate'));
AddEventHandler('iblock', 'OnAfterIBlockSectionUpdate', array('ElemSectConnect', 'ESUSectionUpdate'));
AddEventHandler('iblock', 'OnBeforeIBlockSectionDelete', array('ElemSectConnect', 'ESUSectionDelete'));
Class ElemSectConnect 
{
	//связь элемента и раздела по xml_id
	function ElemSectUnion($ID, $is_element = true, $is_deleted = false)
	{
		if($ID > 0)
		{
			$arESUnionTmp = $arESUnion = array();
			//$arESUnionTmp[4] = 12;
			//$arESUnionTmp[COption::GetOptionString($MODULE_ID, "STROYKA_OBJECT")] = COption::GetOptionString($MODULE_ID, "STROYKA_ROOMS");
			foreach($arESUnionTmp as $element => $section)
				if(IntVal($element) > 0 && IntVal($section) > 0)
					$arESUnion[$element] = $section;
			$obElement = new CIBlockElement;
			$obSection = new CIBlockSection;
			global $NO_EDIT;
			if($NO_EDIT != "Y")
			{
				$NO_EDIT = "N";
				$name = $arElement["NAME"];
				if($arElement["IBLOCK_SECTION_ID"])
				{
					$arPath = array();
					$nav = CIBlockSection::GetNavChain(false, $arElement["IBLOCK_SECTION_ID"]);
					while($arSectionPath = $nav->GetNext())
						$arPath[] = $arSectionPath["NAME"];
					$arPath[] = $name;
					$name = implode("/", $arPath);
				}
				if($is_element)
				{
					$arElement = $obElement->GetByID($ID)->Fetch();
					if($arElement && in_array($arElement["IBLOCK_ID"], array_keys($arESUnion)))
					{
						$NO_EDIT = "Y";
						$dbSection = $obSection->GetList(Array("ID" => "ASC"), Array("IBLOCK_ID" => $arESUnion[$arElement["IBLOCK_ID"]], "XML_ID" => $arElement["ID"], "SECTION_ID" => false));
						if($dbSection->SelectedRowsCount() > 0)
						{
		  					$first = true;
		  					while($arSection = $dbSection->GetNext())
		  						if($first && !$is_deleted)
		  						{
		  							$first = false;
									$obSection->Update($arSection["ID"], array("NAME" => $name, "CODE" => $arElement["CODE"], "XML_ID" => $arElement["ID"]));
		  						}
		  						else $obSection->Delete($arSection["ID"]);
						}
		  				else
		  				{
							$obSection->Add(
								Array(
									"ACTIVE" => "Y",
									"IBLOCK_SECTION_ID" => false,
									"IBLOCK_ID" => $arESUnion[$arElement["IBLOCK_ID"]],
									"NAME" => $name,
									"CODE" => $arElement["CODE"],
									"XML_ID" => $arElement["ID"],
								)
							);
		  				}
					}
				}
				else
				{
					$arSection = $obSection->GetByID($ID)->Fetch();
					$arESUnion = array_flip($arESUnion);
					if($arSection && !$arSection["IBLOCK_SECTION_ID"] && in_array($arSection["IBLOCK_ID"], array_keys($arESUnion)))
					{
						$NO_EDIT = "Y";
						$name = end(explode("/", $arSection["NAME"]));
						$dbElement = $obElement->GetList(Array("ID" => "ASC"), Array("IBLOCK_ID" => $arESUnion[$arSection["IBLOCK_ID"]], "ID" => $arSection["XML_ID"] > 0 ? $arSection["XML_ID"] : false));
						if($dbElement->SelectedRowsCount() > 0)
						{
		  					if(($arElement = $dbElement->GetNext()) && !$is_deleted)
								$obElement->Update($arElement["ID"], array("NAME" => $name, "CODE" => $arSection["CODE"]));
		  					elseif($arSection["XML_ID"] > 0)
		  						$obElement->Delete($arSection["XML_ID"]);
						}
		  				else
		  				{
		  					$arSections = array();
		  					$dbSection = $obSection->GetList(Array("ID" => "ASC"), Array("IBLOCK_ID" => $arSection["IBLOCK_ID"], "SECTION_ID" => false));
		  					while($arSectionTmp = $dbSection->GetNext())
		  						$arSections[] = $arSectionTmp["ID"];
							if($elementID = $obElement->Add(
									Array(
										"ACTIVE" => "Y",
										"IBLOCK_SECTION" => $arSections,
										"IBLOCK_ID" => $arESUnion[$arSection["IBLOCK_ID"]],
										"NAME" => $name,
										"CODE" => $arSection["CODE"],
									)
								)
							)
								$obSection->Update($arSection["ID"], array("XML_ID" => $elementID, "CODE" => $arSection["CODE"]));

		  				}
					}
				}
			}
		}
	}
	
	//OnAfterIBlockElementAdd
	//OnAfterIBlockElementUpdate
	function ESUElementUpdate($arFields)
	{
		self::ElemSectUnion($arFields["ID"]);
	}
	//OnBeforeIBlockElementDelete
	function ESUElementDelete($ID)
	{
		self::ElemSectUnion($ID, true, true);
	}
	//OnAfterIBlockSectionAdd
	//OnAfterIBlockSectionUpdate
	function ESUSectionUpdate($arFields)
	{
		self::ElemSectUnion($arFields["ID"], false);
	}
	//OnBeforeIBlockSectionDelete
	function ESUSectionDelete($ID)
	{
		self::ElemSectUnion($ID, false, true);
	}
	//!связь элемента и раздела по xml_id
}

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("CloseIblock", "OnBeforeIBlockElementAddHandler"));
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", Array("CloseIblock", "OnBeforeIBlockElementDeleteHandler"));
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("CloseIblock", "OnBeforeIBlockElementUpdateHandler"));
AddEventHandler("main", "OnBuildGlobalMenu", Array("CloseIblock", "OnBuildGlobalMenuHandler"));

class CloseIblock
{
	public static $iblocks = array(5, 8, 10, 11, 14, 15);

	function getElementById($id)
	{
		CModule::IncludeModule("iblock");
		return CIBlockElement::GetByID($id)->GetNext();
	}

	function setElementToMenu(&$arMenu)
	{
		CModule::IncludeModule("iblock");
		$arFind = explode("/", $arMenu["items_id"]);
		if($arFind[2] && in_array($arFind[2], self::$iblocks))
		{

			$rsElements = CIBlockElement::GetList(Array(), Array("IBLOCK_ID" => $arFind[2]), false, false, array("ID"));
			if(intval($rsElements->SelectedRowsCount()) == 1)
				foreach($arMenu["more_url"] as $url)
					if(stripos($url, "iblock_element_edit.php") !== false && ($arElement = $rsElements->GetNext()))
						$arMenu["url"] = $url."&WF=Y&find_section_section=0&ID=".$arElement["ID"];
		}
	}

	function OnBeforeIBlockElementAddHandler(&$arFields)
	{
		if(in_array($arFields["IBLOCK_ID"], self::$iblocks))
		{
			global $APPLICATION;
			$APPLICATION->throwException("Инфоблок защищен от редактирования");
			return false;
		}
	}

	function OnBeforeIBlockElementUpdateHandler(&$arFields)
	{
		$arElement = self::getElementById($arFields["ID"]);
		if(in_array($arElement["IBLOCK_ID"], self::$iblocks))
			$arFields["CODE"] = $arElement["CODE"];
	}

	function OnBeforeIBlockElementDeleteHandler($ID)
	{
		$arElement = self::getElementById($ID);
		if(in_array($arElement["IBLOCK_ID"], self::$iblocks))
		{
			global $APPLICATION;
			$APPLICATION->throwException("Инфоблок защищен от редактирования");
			return false;
		}
	}

	function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu)
	{
		foreach($aModuleMenu as &$arMenu)
		{
			if($arMenu["module_id"] == "iblock")
			{
				
				if(self::setElementToMenu($arMenu))
					continue;
				elseif(is_array($arMenu["items"]) && count($arMenu["items"]))
				{
					foreach($arMenu["items"] as &$arIBlock)
					{
						self::setElementToMenu($arIBlock);
					}
				}
			}
		}
	}
}

function array_combo($array = array())
{
	$combos = array();
	if(count($array) == 1)
		$combos[] = $array;
	else foreach ($array as $key => $value) 
		{
			$_combos = array_combo(array_diff($array, array($value)));
			foreach($_combos as $_combo) 
				$combos[] = array_merge(array($value), $_combo);
		}
	return $combos;
}

function array_combos($array = array())
{
	$combos = array();
	$array = (array) $array;
	$array = array_unique($array);
	$array = array_diff($array, array(null));
	$combos = array_combo($array);
	return $combos;
}

function str_combos($str = "", $delimiter = " ", $glue = "%", $lr = "%")
{
	$combos = array();
	$str = (string) $str;
	$str_arr = explode($delimiter, $str);
	$combos = array_combos($str_arr);
	foreach ($combos as $key => $combo) 
		$combos[$key] = $lr.implode($glue, $combo).$lr;
	return $combos;
}

AddEventHandler("main", "OnProlog", "includeUtils");
function includeUtils()
{
	$dir = __DIR__."/include/utils";
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) 
			if(strlen(str_replace(array(" ", "."), "", $file)) && stripos($file, ".php") !== false)
				include_once($dir."/".$file);
		closedir($handle); 
	}
}

AddEventHandler("main", "OnProlog", "tests");
function tests()
{
	global $USER;
	if($_REQUEST["tests"] == "Y" && $USER->IsAdmin())
	{
		echo "<pre>" . shell_exec("cd " . $_SERVER["DOCUMENT_ROOT"] . "/local/ && codecept run --no-colors" . $fileTest) . "</pre>";
		die();
	}
}

AddEventHandler("main", "OnAfterGroupAdd", "add_to_compisite");
function add_to_compisite($arFields = array())
{
	if($arFields["ID"] > 0)
	{
		$arHTMLCacheOptions = CHTMLPagesCache::getOptions();
		$arHTMLCacheOptions["GROUPS"][] = $arFields["ID"];
		CHTMLPagesCache::setOptions($arHTMLCacheOptions);
	}
}

AddEventHandler('iblock', 'OnAfterIBlockElementAdd', array('NewsSendMessage', 'ElementAdd'));
class NewsSendMessage
{
	function ElementAdd($arFields)
	{
		mail("anton@yadadya.com", "На сайте Storemate создана новая новость", "На сайте создана новость\"".$arFields["NAME"]."\". Для ее активации перейдите по ссылке http://lk.storemate.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=3&type=app_news&ID=".$arFields["ID"]."&lang=ru&find_section_section=-1&WF=Y");
	}
}

AddEventHandler("main", "OnProlog", array("wikiDoc", "helpParser"), 500);
class wikiDoc
{
	function parseFile($file = "")
	{
		if (file_exists($file))
			return self::parseText(file_get_contents($file));
		return false;
	}

	function parseText($text = "")
	{
		$text = str_replace(array("<!--AUTODOC-->", "<!--/AUTODOC-->"), array("\n<!--AUTODOC-->\n", "\n<!--/AUTODOC-->\n"), $text); //bug fix
		if (class_exists("Markdown"))
			return Markdown::defaultTransform($text);
		elseif (class_exists("Parsedown"))
		{
			$Parsedown = new Parsedown();
			return $Parsedown->text($text);
		}
		return false;
	}

	function helpParser()
	{
		global $APPLICATION, $USER;
		if($_REQUEST["help"] == "Y" && $USER->IsAuthorized())
		{
			$readme = $_SERVER["DOCUMENT_ROOT"].$APPLICATION->GetCurDir()."README.md";
			if (file_exists($readme) && (class_exists("Markdown") || class_exists("Parsedown")))
			{
				$APPLICATION->RestartBuffer();
				echo self::parseFile($readme);
				die();
			}
		}
	}

	function updateWikiFromReadme()
	{
		$files = self::scanDoc();
		foreach ($files as $file) 
		{
			$doc = file_get_contents($file);
			preg_match("/".preg_quote("<!--WIKI_URL=", "/")."(.*)".preg_quote("-->", "/")."/U", $doc, $matches);
			if (!empty($matches[1]))
			{
				$page_id = 0;
				$arQuery = explode("/", parse_url($matches[1], PHP_URL_PATH));
				if (is_array($arQuery)) $page_id = array_pop($arQuery);
				if (!$page_id) $page_id = $matches[1];

				self::updateWiki($page_id, self::parseText($doc));
			}
		}
	}

	function scanDoc($dir = "")
	{
		$result = array();
		$skip = empty($dir) ? array(".", "..", "bitrix", "upload", "local", ".git") : array(".", "..");

		if (empty($dir))
			$dir = $_SERVER["DOCUMENT_ROOT"];

		if (file_exists($dir."/README.md"))
			$result[] = $dir."/README.md";

		$scan = scandir($dir);
		foreach ($scan as $file)
			if(!in_array($file, $skip) && is_dir($dir."/".$file))
				$result = array_merge($result, self::scanDoc($dir."/".$file));

		return $result;
	}

	function updateWiki($id = "", $data = "")
	{
		if (class_exists("Altassian"))
		{
			$lastPage = Altassian::getContent($id);
			Altassian::setContent($id, array(
				"version" => array(
					"number" => $lastPage["version"]["number"]+1
				),
				"title" => $lastPage["title"],
				"type" => $lastPage["type"],
				"body" => array(
					"storage" => array(
						"value" => $data,
						"representation" => "storage"
					)
				)
			));
		}
	}
}
?>