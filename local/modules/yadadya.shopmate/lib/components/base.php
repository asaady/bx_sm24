<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;
use Yadadya\Shopmate;
use Bitrix\Main\Entity;
use \Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

abstract class Base
{
	public $result;

	protected static $iblockID = 0;
	protected static $propList = array();
	protected static $filterList = array();
	protected static $currentSort = array("ID" => "DESC");
	public static $arParams = array();

	public function __construct(array $parameters = array())
	{
		$this->result = new Entity\Result();
		$this->setCatalogID($parameters["IBLOCK_ID"] > 0 ? $parameters["IBLOCK_ID"] : Shopmate\Options::getCatalogID());
	}

	public static function GetUserPermission($chapter_id = false, $shop_ignore = false)
	{
		global $USER;
		if (empty($chapter_id))
			$chapter_id = strtolower(basename(str_replace("\\", "\/", get_called_class())));
		/*$perm = $USER->GetParam("SM_PERM_".strtoupper($chapter_id));
		if (empty($perm))*/
			$perm = Shopmate\Rights::GetUserPermission($chapter_id, false, $shop_ignore);
		//$USER->SetParam("SM_PERM_".strtoupper($chapter_id), $perm);
		return $perm;
	}

	public static function userCanRead($chapter_id = false)
	{
		return static::GetUserPermission($chapter_id) >= "R";
	}

	public static function userCanEdit($chapter_id = false)
	{
		if (self::$arParams["ALLOW_EDIT"] == "N")
			return false;
		return static::GetUserPermission($chapter_id) >= "W";
	}

	public static function userCanAll($chapter_id = false)
	{
		if (self::$arParams["ALLOW_EDIT"] == "N" || self::$arParams["ALLOW_DELETE"] == "N")
			return false;
		return static::GetUserPermission($chapter_id) >= "X";
	}

	public static function GetGlobalGroups($group_id = "")
	{
		return Shopmate\Rights::GetGlobalGroups($group_id);
	}

	public function onProlog()
	{
		return true;
	}

	public function onPrepareParams($arParams)
	{
		self::$arParams = $arParams;
		return $arParams;
	}

	public static function getParametrs(array $order = array(), array $filter = array(), $page_size = 50, $select)
	{
		$parameters = array();

		if (!empty($select))
			$parameters["select"] = (array) $select;

		if(!empty($order))
			$parameters["order"] = $order;

		if(!empty($filter))
			$parameters["filter"] = $filter;

		if($page_size !== false || !empty($page_size))
		{
			$nav = self::getPageNavigation($page_size);
			$parameters["count_total"] = true;
			$parameters["offset"] = $nav->getOffset();
			$parameters["limit"] = $nav->getLimit();
		}


		return $parameters;
	}

	public static function getPageNavigation($page_size = 50)
	{
		$nav = new \Bitrix\Main\UI\PageNavigation("page");
		if($page_size !== false)
		{
			$page_size = intval($page_size);
			$page_size = $page_size > 0 ? $page_size : 50;

			$nav->allowAllRecords(true)
				->setPageSize($page_size)
				->initFromUri();
		}
		return $nav;
	}

	public static function getOrderList($sort_fields = array())
	{
		if(empty($sort_fields))
			$sort_fields = static::$currentFields;
		else
			$sort_fields = (array) $sort_fields;

		global $APPLICATION;
		$arSort = array();
		$sortIndex = 0;
		foreach($sort_fields as $field)
		{
			$sortIndex +=10;
			$arSort[$field] = array(
				"FIELD" => $field,
				"NAME" => Loc::getMessage($field."_TITLE"),
				"SORT" => $sortIndex,
				"ORDER" => $_REQUEST["SORT"] == $field ? (strtoupper($_REQUEST["ORDER"]) != "DESC" ? "DESC" : "ASC") : "ASC"
			);
			$arSort[$field]["URL"] = $APPLICATION->GetCurPageParam("SORT=".$arSort[$field]["FIELD"].($arSort[$field]["ORDER"] == "DESC" ? "&ORDER=".$arSort[$field]["ORDER"] : ""), array("SORT", "ORDER"));
		}
		return $arSort;
	}

	public static function getOrder(array $order = array())
	{
		if(!empty($_REQUEST["SORT"]))
			$order = array($_REQUEST["SORT"] => strtoupper($_REQUEST["ORDER"]) != "DESC" ? "ASC" : "DESC");
		else
			$order = static::$currentSort;
		return $order;
	}

	public static function getSelect($select, array $currentFields = array(), array $referenceFields = array())
	{
		if(empty($select))
			$select = $currentFields;
		foreach($select as $key => $field) 
			if($field == "*")
			{
				foreach($referenceFields as $fieldAlias => $referenceField) 
					if(!in_array($referenceField, $select))
						$select[$fieldAlias] = $referenceField;
			}
			elseif(array_key_exists($field, $referenceFields))
			{
				$select[$field] = $referenceFields[$field];
				unset($select[$key]);
			}
		return $select;
	}

	public static function getFilter($filter = array())
	{
		$arFilter = array();
		$filter = (array) $filter;
		$filter = self::checkFilterRequest($filter);

		foreach($filter as $field => $value) 
			if(!empty($value))
				$arFilter[$field] = $value;

		return $arFilter;
	}

	public static function checkFilterRequest(array $filter = array())
	{
		foreach($filter as $key => $value) 
		{
			if(is_array($value))
				$value = self::checkFilterRequest($value);
			if(empty($value))
				unset($filter[$key]);
		}
		return $filter;
	}

	public static function getDateFilter($field, $date_from = null, $date_to = null)
	{
		$res = array();
		if(!empty($field))
		{
			if(!empty($date_from))
				$res[">=".$field] = new Type\DateTime(date("Y-m-d", strtotime($date_from)), "Y-m-d");
			if(!empty($date_to))
				$res["<".$field] = new Type\DateTime(date("Y-m-d", strtotime($date_to)+24*60*60), "Y-m-d");
		}
		return $res;
	}

	public static function getIblockSectionFilter($value = array(), $object = false)
	{
		$q = new Entity\Query(\Bitrix\Iblock\SectionElementTable::getEntity());
		$q->setSelect(array("IBLOCK_SECTION_ID"));
		$q->registerRuntimeField("BSubS",
			new Entity\ReferenceField(
				"BSubS",
				"Bitrix\Iblock\Section",
				array("=ref.ID" => "this.IBLOCK_SECTION_ID"),
				array("join_type" => "INNER")
			)
		);
		$q->registerRuntimeField("BS",
			new Entity\ReferenceField(
				"BS",
				"Bitrix\Iblock\Section",
				array(
					"=ref.IBLOCK_ID" => new \Bitrix\Main\DB\SqlExpression("`iblock_section_element_bsubs`.`IBLOCK_ID`"),
					"<=ref.LEFT_MARGIN" => new \Bitrix\Main\DB\SqlExpression("`iblock_section_element_bsubs`.`LEFT_MARGIN`"),
					">=ref.RIGHT_MARGIN" => new \Bitrix\Main\DB\SqlExpression("`iblock_section_element_bsubs`.`RIGHT_MARGIN`")
				),
				array("join_type" => "INNER")
			)
		);
		if ($value instanceof \Bitrix\Main\DB\SqlExpression)
			$q->setFilter(array("@BS.ID" => $value));
		else
			$q->setFilter(array("BS.ID" => (array) $value));

		if($object) return $q;
		else return $q->getQuery();
	}

	public static function getIblockPropertyFilter($field, $value = array())
	{
		$query = "SELECT `b_iblock_element_property`.`IBLOCK_ELEMENT_ID` FROM `b_iblock_element_property` 
			LEFT JOIN `b_iblock_property` ON `b_iblock_property`.`ID` = `b_iblock_element_property`.`IBLOCK_PROPERTY_ID`
			LEFT JOIN  `b_iblock_property_enum` ON  `b_iblock_property_enum`.`PROPERTY_ID` = `b_iblock_property`.`ID`
			WHERE `b_iblock_property`.`CODE` = 'PACK'
			AND `b_iblock_element_property`.`VALUE` IN (".implode(",", (array) $value).")";
		return $query;
	}

	public static function getSearchComboFilter($str = "", $delimiter = " ", $glue = "%", $lr = "%")
	{
		$combos = array();
		$str = (string) $str;
		$str_arr = explode($delimiter, $str);
		$combos = array_combos($str_arr);
		foreach ($combos as $key => $combo) 
			$combos[$key] = $lr.implode($glue, $combo).$lr;
		return $combos;
	}

	public function setInputLangTitle(array $propList = array(), $prefix = "")
	{
		foreach($propList as $prop => $arProp) 
		{
			$propList[$prop]["TITLE"] = Loc::getMessage($prefix.$prop."_TITLE");
			if(is_array($propList[$prop]["PROPERTY_LIST"]))
				$propList[$prop]["PROPERTY_LIST"] = self::setInputLangTitle($propList[$prop]["PROPERTY_LIST"], $prefix.$prop."_");
		}
		return $propList;
	}

	public function prepareProps(array $propList = array(), $data = array(), $prefix = "")
	{
		global $APPLICATION;
		foreach($propList as $prop => $arProp) 
		{
			if ($arProp["PROPERTY_TYPE"] == "L" && $arProp["LIST_TYPE"] == "AJAX" && !empty($arProp["REF_ENTITY"]))
			{
				$propList[$prop]["DATA"]["url"] = $APPLICATION->GetCurPageParam("get_search=".$prefix.$prop, ["get_search"]);
				if (isset($arProp["DATA"]["info_url"]))
					$propList[$prop]["DATA"]["info_url"] = $APPLICATION->GetCurPageParam("get_info=".$prefix.$prop, ["get_search"]);

				if (!empty($prefix) && is_array($data))
				{
					$ids = [];
					foreach ($data as $value) 
						if (!empty($value[$prop]))
							$ids[] = $value[$prop];
				}
				elseif (!empty($data[$prop]))
					$ids = [$data[$prop]];

				if (!empty($ids))
					$propList[$prop]["ENUM"] = $arProp["REF_ENTITY"]::getEnumByID($ids);
			}
			if(is_array($propList[$prop]["PROPERTY_LIST"]))
				$propList[$prop]["PROPERTY_LIST"] = self::prepareProps($propList[$prop]["PROPERTY_LIST"], $data[$prop], $prefix.$prop.".");
		}
		return $propList;
	}

	public static function disableInput(array &$propList = array(), $fields = array(), $prefix = "")
	{
		$fields = (array) $fields;
		foreach($propList as $prop => $arProp) 
		{
			if(empty($fields) || in_array($prefix.$prop, $fields))
				$propList[$prop]["DISABLED"] = "Y";
			if(is_array($propList[$prop]["PROPERTY_LIST"]))
				self::disableInput($propList[$prop]["PROPERTY_LIST"], $fields, $prefix.$prop."_");
		}
	}

	public function save($primary, array $data)
	{
		$primary = intval($primary);

		$component_name = basename(str_replace("\\", "\/", get_class($this)));

		$event_data = $data;
		$event_data["ID"] = $primary;
		$this->addEvent("OnBeforeShopmateComponent" . ($primary > 0 ? "Update" : "Add"), array("ENTITY" => $this, "VALUE" => $event_data, "COMPONENT" => $component_name), null, $result);
		$this->addEvent("OnBeforeShopmateC" . $component_name . ($primary > 0 ? "Update" : "Add"), array("ENTITY" => $this, "VALUE" => $event_data), null, $result);

		if($primary > 0)
			$result = static::update($primary, $data);
		else
			$result = static::add($data);

		if(is_object($this) && $result->isSuccess())
		{
			$event_data = $data;
			$event_data["ID"] = $primary > 0 ? $primary : $result->getId();

			$this->addEvent("OnShopmateComponent" . ($primary > 0 ? "Update" : "Add"), array("ENTITY" => $this, "VALUE" => $event_data, "COMPONENT" => $component_name), null, $result);
			$this->addEvent("OnShopmateC" . $component_name . ($primary > 0 ? "Update" : "Add"), array("ENTITY" => $this, "VALUE" => $event_data), null, $result);
		}

		return $result;
	}

	public function addEvent($type, $parameters = array(), $filter = null, &$result)
	{
		$event = new \Bitrix\Main\Event("yadadya.shopmate", $type, $parameters, $filter);
		$event->send();

		foreach ($event->getResults() as $eventResult)
		{
			switch($eventResult->getType())
			{
				case \Bitrix\Main\EventResult::ERROR:
					$result->addErrors($eventResult->getParameters());
					break;
				case \Bitrix\Main\EventResult::SUCCESS:
					break;
				case \Bitrix\Main\EventResult::UNDEFINED:
					break;
			}
		}
	}

	public function add(array &$data)
	{
		$result = new Entity\AddResult();

		$data = self::checkFields($data, array(), $errors);

		if(!empty($errors)) $result->addErrors($errors);

		return $result;
	}

	public function update($primary, array &$data)
	{
		$result = new Entity\UpdateResult();

		$data = self::checkFields($data, array(), $errors, true);

		if(!empty($errors)) $result->addErrors($errors);

		return $result;
	}

	public function delete($primary)
	{
		$result = new Entity\DeleteResult();
		if(empty($primary))
			$result->addError(new Entity\EntityError("Primary is empty."));
		return $result;
	}

	public static function checkErrors(array $data, array $propList = array(), &$errors, $ignoreRequired = false)
	{
		$errors = (array) $errors;

		if(empty($propList)) $propList = self::getProps();
		foreach($propList as $prop => $arProp)
		{
			if(!$ignoreRequired && $arProp["REQUIRED"] == "Y" && strlen($data[$prop]) <= 0)
				$errors[] = new Entity\EntityError(Loc::getMessage("ERROR_EMPTY")." \"".Loc::getMessage($prop."_TITLE")."\"", "FIELD_".$prop);
		}

		return $errors;
	}

	public function checkFields(array $data, array $propList = array(), &$errors, $ignoreRequired = false)
	{
		$_data = array();
		if(empty($propList)) $propList = self::getProps();
		foreach($propList as $prop => $arProp)
		{
			if($arProp["PROPERTY_TYPE"] == "SUBLIST" && is_array($arProp["PROPERTY_LIST"]))
				foreach($data[$prop] as $key => $arSub) 
					$_data[$prop][$key] = self::checkFields((array) $arSub, $arProp["PROPERTY_LIST"], $errors, $ignoreRequired);
			elseif($arProp["DISABLED"] != "Y")
				$_data[$prop] = $data[$prop];

			if(!empty($arProp["VERIFICATION"]) && !empty($_data[$prop]))
			{
				switch ($arProp["VERIFICATION"]) {
					case "float":
						
						$_data[$prop] = preg_replace(array('/[^0-9,.]/', '/,/'), array('', '.'), $_data[$prop]);
						$last_delim = strrpos($_data[$prop], ".");
						if($last_delim !== false)
						{
							$last_delim = $last_delim - strlen($_data[$prop])+1;
							$_data[$prop] = str_replace(".", "", $_data[$prop]);
							$_data[$prop] = substr_replace($_data[$prop], ".", $last_delim, 0);
						}

						break;
					
					default:

						# code...
					
						break;
				}
			}
		}
		$errors = array_merge((array) $errors, static::checkErrors($data, $propList, $errors, $ignoreRequired));
		return $_data;
	}

	public static function getPropList()
	{
		return static::$propList;
	}

	public function getProps()
	{
		return is_object($this) && isset($this->propList) ? $this->propList : static::getPropList();
	}

	public function setPropList(array $propList = array())
	{
		if(is_object($this)) $this->propList = $propList;
	}

	public function getCatalogID()
	{
		return !empty(self::$iblockID) ? self::$iblockID : Shopmate\Options::getCatalogID();
	}

	public function setCatalogID($iblockID = 0)
	{
		if(is_object($this))
			$this->iblockID = $iblockID > 0 ? $iblockID : Shopmate\Options::getCatalogID();
	}

	public static function getFilterList()
	{
		return static::$filterList;
	}

	public static function redirectAfterSave($to_list = true, \Bitrix\Main\Result $result = null)
	{
		global $APPLICATION;
		if(!is_object($result))
			$to_list = true;
		else
			$id = $result->getId();

		if($to_list)
			$sRedirectUrl = $APPLICATION->GetCurPageParam("", array("edit", "CODE", "successMessage"), $get_index_page=false);
		else
			$sRedirectUrl = $APPLICATION->GetCurPageParam("edit=Y&CODE=".$id, array("edit", "CODE", "successMessage"), $get_index_page=false);

		$sAction = ($result instanceof \Bitrix\Main\Entity\AddResult) ? "ADD" : "UPDATE";
		$sRedirectUrl .= (strpos($sRedirectUrl, "?") === false ? "?" : "&") . "successMessage=" . $sAction;

		LocalRedirect($sRedirectUrl);
		exit();
	}

	public static function redirectAfterDelete(\Bitrix\Main\Result $result = null)
	{
		global $APPLICATION;

		$sRedirectUrl = $APPLICATION->GetCurPageParam("", array("edit", "delete", "CODE", "successMessage"), $get_index_page=false);

		$sRedirectUrl .= (strpos($sRedirectUrl, "?") === false ? "?" : "&") . "successMessage=DELETE";

		LocalRedirect($sRedirectUrl);
		exit();
	}

	public static function addPostData(&$data, array $postData = array())
	{
		foreach($postData as $key => $val)
		{
			if(is_array($val))
			{
				$data[$key] = (array) $data[$key];
				foreach($val as $k => $v)
					$data[$key]["post".$k] = $v;
			}
			else
				$data[$key] = $val;
		}
	}

	public function getSqlFieldClass($sql = "", $map = array(), $className = "") 
	{
		$namespace = "Yadadya\Shopmate\FakeInternals";
		if (!class_exists($namespace."\\".$className."Table"))
			eval("namespace ".$namespace.";
				class ".$className."Table extends \Bitrix\Main\Entity\DataManager
				{
					public static function getTableName()
					{
						return \"".(strpos($sql, " ") !== false ? "(".$sql.")" : $sql)."\";
					}

					public static function getMap()
					{
						return json_decode('".json_encode($map)."', true);
					}
				}"
			);
		return $namespace."\\".$className;
	}

	public function prepareResult() {}

	public function resultModifier($arResult, $arParams) 
	{
		return $arResult;
	}

	public static function getJSONEnumList(array $enum = array())
	{
		$json = array();
		foreach ($enum as $id => $text)
			$json["items"][] = array("id" => $id, "text" => is_array($text) ? $text["VALUE"] : $text);
		return json_encode($json);
	}

	public static function searchInfo()
	{
		$result = "";
		return $result;
	}

	abstract function getList(array $parameters = array());
	abstract function getByID($primary = 0);

	public static function createDoc()
	{
		$doc = array();
		$doc[] = "## ".Loc::GetMessage("AUTODOC_HEADER_LIST");
		self::getDocList(static::getOrderList(), "LIST", $doc);
		if (!empty(static::getFilterList()))
		{
			$doc[] = "";
			$doc[] = "## ".Loc::GetMessage("AUTODOC_HEADER_FILTER");
			self::getDocList(static::getFilterList(), "FILTER", $doc);
		}
		if (!empty(static::getPropList()))
		{
			$doc[] = "";
			$doc[] = "## ".Loc::GetMessage("AUTODOC_HEADER_ITEM", array("#ITEM#" => Loc::GetMessage("SITE_SECTION_NAME")));
			self::getDocList(static::getPropList(), "ITEM", $doc);
		}
		return implode("
", $doc);
	}



	protected static function getDocList(array $list = array(), $header = "", &$doc, $level = 0, $prefix = "")
	{
		if (empty($doc)) 
			$doc = array();
		
		foreach ($list as $code => $field) 
			if ($field["PROPERTY_TYPE"] != "H")
			{
				$tags = array();
				switch ($field["PROPERTY_TYPE"]) 
				{
					case "L":

						$listEnum = array();
						if (is_array($field["ENUM"]))
							foreach ($field["ENUM"] as $enumVal => $enumDesc) 
								$listEnum[] = "[".$enumVal."] ".(is_array($enumDesc) ? $enumDesc["VALUE"] : $enumDesc);

						$listType = array(Loc::GetMessage("AUTODOC_TYPE_LIST").(!empty($listEnum) ? "(".(count($listEnum) > 15 ? "..." : implode(", ", $listEnum)).")" : ""));

						if ($field["MULTIPLE"] == "Y") 
							$listType[] = Loc::GetMessage("AUTODOC_TYPE_LIST_MULTIPLE");
						if ($field["LIST_TYPE"] == "AJAX") 
							$listType[] = Loc::GetMessage("AUTODOC_TYPE_LIST_AJAX");

						$tags[] = Loc::GetMessage("AUTODOC_TYPE_TITLE")." - ".implode(", ", $listType);

						break;

					case "T":

						$tags[] = Loc::GetMessage("AUTODOC_TYPE_TITLE")." - ".Loc::GetMessage("AUTODOC_TYPE_TEXTAREA");
						
						break;
					
					case "SUBLIST":

						$tags[] = Loc::GetMessage("AUTODOC_TYPE_TITLE")." - ".Loc::GetMessage("AUTODOC_TYPE_SUBLIST");
						
						break;
					
					default:
						
						//$tags[] = Loc::GetMessage("AUTODOC_TYPE_TITLE")." - ".Loc::GetMessage("AUTODOC_TYPE_STRING");

						break;
				}

				if ($field["REQUIRED"] == "Y")
					$tags[] = Loc::GetMessage("AUTODOC_REQUIRED");

				if ($field["UNIQUE"] == "Y")
					$tags[] = Loc::GetMessage("AUTODOC_UNIQUE");

				if ($field["READONLY"] == "Y" || $field["DISABLED"] == "Y")
					$tags[] = Loc::GetMessage("AUTODOC_READONLY");

				$doc[] = str_repeat("	", $level)."* <!--[".(empty($header) ? $prefix : $header."_")."CODE=".$prefix.$code."]--><b>".(empty(Loc::getMessage($prefix.$code."_TITLE")) ? $code : Loc::getMessage($prefix.$code."_TITLE"))."</b> [".$code."]".(!empty(Loc::getMessage($prefix.$code."_DESCRIPTION")) ? " (".Loc::getMessage($prefix.$code."_DESCRIPTION").")" : "").(!empty($tags) ? ": ".implode("; ", $tags) : "");
				if ($field["PROPERTY_TYPE"] == "SUBLIST")
					self::getDocList($field["PROPERTY_LIST"], $header, $doc, $level+1, $code."_");
			}
		return $doc;
	}

	public static function Loc($code, $replace = null, $language = null)
	{
		$cl = new \ReflectionClass(get_called_class()); 
		Loc::loadCustomMessages($cl->getFileName());
		return Loc::getMessage($code);
	}
}