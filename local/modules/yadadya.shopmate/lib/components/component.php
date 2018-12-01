<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc;

use Yadadya\Shopmate;

Loc::loadMessages(__FILE__);

class Component extends \CBitrixComponent
{
	protected $obj;

	protected $type;

	/**
	 * Primary data.
	 * @var array[]
	 */
	protected $id = 0;

	/**
	 * Primary data.
	 * @var array[]
	 */
	protected $item = array();

	/**
	 * Primary data.
	 * @var array[]
	 */
	protected $items = array();

	/**
	 * Filter to fetch items.
	 * Used in getList()
	 * @var string[]
	 */
	private $filter = array();

	/**
	 * Filter to fetch items.
	 * Used in getList()
	 * @var string[]
	 */
	private $filterRequest = array();

	/**
	 * Select fields for items.
	 * Used in getList()
	 * @var string[]
	 */
	private $selectFields = array();

	/**
	 * Page navigation for items.
	 * Used in getList()
	 * @var string[]
	 */
	public $pageNavigation = array();

	/**
	 * Errors list.
	 * @var string[]
	 */
	protected $errors = array();

	/**
	 * Warnings list.
	 * @var string[]
	 */
	protected $warnings = array();

	/**
	 * Util data for template.
	 * @var array
	 */
	protected $data = array();

	protected $_request = array();

	private static $__classes_map = array();
	private $classOfComponent = "";
	private $__bInited = false;

	/**
	 * Url templates for items
	 *
	 * @var array
	 */
	protected $urlTemplates = array();

	public function __construct($component = null)
	{
		if(!is_object($component) && !empty($component))
		{
			$componentName = $component;
			$component = new \CBitrixComponent;
			$path2Comp = \CComponentEngine::MakeComponentPath($componentName);
			$componentPath = getLocalPath("components".$path2Comp);
			if (!empty($componentPath))
			{
				if(!isset(self::$__classes_map[$componentPath]))
				{
					$beforeClasses = get_declared_classes();
					$beforeClassesCount = count($beforeClasses);
				}
				ob_start();
				$component->initComponent($componentName);
				ob_end_clean();
				if(!isset(self::$__classes_map[$componentPath]))
				{
					$afterClasses = get_declared_classes();
					$afterClassesCount = count($afterClasses);
					for ($i = $beforeClassesCount; $i < $afterClassesCount; $i++)
					{
						if (is_subclass_of($afterClasses[$i], "cbitrixcomponent") && !empty($afterClasses[$i]))
							self::$__classes_map[$componentPath] = $afterClasses[$i];
					}
				}
			}
			/*else
			{
				print_p(123);
				$componentPath = rtrim($_SERVER["DOCUMENT_ROOT"], "\\/") . "/bitrix/components" . $path2Comp;

				if (empty(self::$__classes_map[$componentPath]))
				{
					$cmpName = explode(":", $componentName);
					if ($cmpName[0] == "shopmate")
					{
						$classArr = explode(".", $cmpName[1]);
						foreach ($classArr as $key => $value) 
							$classArr[$key] = ucwords($value);
						$componentClass = "C".implode("", $classArr)."Component";
						self::$__classes_map[$componentPath] = $componentClass;
						eval("class ".$componentClass." extends \Yadadya\Shopmate\Components\Component {}");
						$this->classOfComponent = $componentClass;
						print_p($this->classOfComponent);
						$this->__name = $componentName;
						$this->__relativePath = $path2Comp;
						$this->__path = $componentPath;
						$this->arResult = array();
						$this->arParams = array();
						$this->__parent = null;
						$this->__arIncludeAreaIcons = array();
						$this->__cache = null;
						if ($componentTemplate !== false)
							$this->__templateName = $componentTemplate;

						$this->__bInited = true;
						print_p($this->__bInited);

						$component = new \CBitrixComponent($this);
					}
				}
			}*/

			$cObj = new self::$__classes_map[$componentPath];
			$init = $cObj->getInit();
		}
		else
			$init = $this->getInit();
		
		if(is_object($init))
			$init = array("object" => $init);

		$this->Init($init["object"], $init["type"]);
		parent::__construct($component);
	}

	public function __call($name, $arguments)
	{
		if(method_exists($this->obj, $name))
			return call_user_func_array(array($this->obj, $name), $arguments);
	}

	public function __get($name)
	{
		if(property_exists($this->obj, $name))
			return $this->obj->$name;
	}

	public function __set($name, $value)
	{
		if(property_exists($this->obj, $name))
			$this->obj->$name = $value;
	}

	public function getInit()
	{
		$className = get_class($this);
		$prefix = "C";
		$postfix = "Component";
		$prefLen = strlen("C");
		$nameLen = strlen($className) - $prefLen - strlen("Component");
		$className = substr($className, $prefLen, $nameLen); //CClassNameComponent to ClassName
		list($object, $type) = self::_getClassByName($className);
		$object = "\\Yadadya\\Shopmate\\Components\\".$object;

		return array("object" => new $object, "type" => $type);
	}

	private static function _getClassByName($object)
	{
		$type = "";

		if(!empty($object) && class_exists("\\Yadadya\\Shopmate\\Components\\".$object))
			return array($object, $type);

		$objtolow = strtolower($object);
		for ($i = strlen($object) - 1; $i >= 0; $i--) 
		{
			if($object{$i} != $objtolow{$i})
			{
				$type = substr($objtolow, $i);
				$object = substr($object, 0, $i);
				if (class_exists("\\Yadadya\\Shopmate\\Components\\".$object))
					break;
			}
		}

		return array($object, $type);
	}

	public function onPrepareComponentParams($arParams)
	{
		if ($this->getName() == "shopmate:default" && !empty($arParams["COMPONENT_CLASS"]))
		{
			list($object, $type) = self::_getClassByName($arParams["COMPONENT_CLASS"]);
			$object = "\\Yadadya\\Shopmate\\Components\\".$object;
			$this->Init(new $object, $type);
		}
		return parent::onPrepareComponentParams($arParams);
	}

	public function Init($obj = null, $type = "", $request = array())
	{
		/*if(!is_object($obj) || !($obj instanceof Base))
			$obj = new Base();*/
		$this->obj = $obj;
		//$this->obj->component = &$this;
		$this->type = $type;
		$this->_request = empty($request) ? $_REQUEST : (array) $request;
	}

	public function getComponentObject()
	{
		return $this->obj;
	}

	/**
	 * Load language file.
	 */
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	/**
	 * Is AJAX Request?
	 * @return bool
	 */
	protected function isAjax()
	{
		return isset($this->_request['ajax_mode']) && $this->_request['ajax_mode'] == 'Y' && $this->arParams["AJAX_MODE"] != "N" && in_array($this->type, array("list", "detail"));
	}

	/**
	 * Check Required Modules
	 * @throws Exception
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('catalog'))
			throw new Main\SystemException('Catolog module not installed');
		$this->isCurrency = true;
		if (!Main\Loader::includeModule('sale'))
			throw new Main\SystemException('Sale module not installed');
	}

	/**
	 * Process request actions list
	 * @return void
	 */
	protected function doActionsList()
	{
		/*if (!empty($this->_request["search"]))
		{
			$arProp = $this->getPropList();
			$searchPropId = explode(".", $this->_request["search"]);
			foreach ($searchPropId as $key) 
				$arProp = isset($arProp[$key]) ? $arProp[$key] : (isset($arProp["PROPERTY_LIST"][$key]) ? $arProp["PROPERTY_LIST"][$key] : []);
			if (!empty($arProp["REF_ENTITY"]))
			{
				print_p($this->arResult);
				$this->data["REF_ENTITY"] = $arProp["REF_ENTITY"];
				$this->type = "search";
			}
		}*/
		foreach ($this->_request as $param => $propId) 
		{
			$getPrefix = "get_";
			if (strpos($param, $getPrefix) === 0)
			{
				$arProp = $this->getPropList();
				$propId = explode(".", $this->_request[$param]);
				foreach ($propId as $key) 
				{
					if (isset($arProp[$key]) || isset($arProp["PROPERTY_LIST"][$key]))
						$arProp = isset($arProp[$key]) ? $arProp[$key] : (isset($arProp["PROPERTY_LIST"][$key]) ? $arProp["PROPERTY_LIST"][$key] : []);
					else
					{
						$arProp = [];
						break;
					}
				}
				if (!empty($arProp["REF_ENTITY"]))
				{
					header('Content-Type: application/json; charset=' . LANG_CHARSET);
					global $APPLICATION;
					$APPLICATION->RestartBuffer();
					$getFunc = substr($param, strlen($getPrefix));
					switch ($getFunc) {
						case "search":
							//print_p($arProp["REF_ENTITY"]);
							echo $arProp["REF_ENTITY"]::getEnumList(array("SEARCH" => $_REQUEST["q"]), true);
							break;

						case "info":
							//print_p($arProp["REF_ENTITY"]);
							echo $arProp["REF_ENTITY"]::getInfo($_REQUEST["pid"], true);
							break;
						
						default:
							# code...
							break;
					}
					die();
				}
			}
		}

		switch ($this->type) 
		{
			case "list":
			
				$this->filterRequest = $this->checkFields($this->_request, $this->getFilterList());
				$this->filter = $this->getFilter($this->filterRequest);
				$FILTER_NAME = $this->getFilterName();
				global ${$FILTER_NAME};
				${$FILTER_NAME} = array_merge((array) ${$FILTER_NAME}, (array) $this->filter);

			case "detail":

				if(check_bitrix_sessid() && $this->userCanEdit() && (!empty($this->_request["submit"]) || !empty($this->_request["apply"])))
				{
					$data = $_POST;

					$this->result = $this->save($this->id, $data);

					if($this->result->isSuccess())
					{
						$this->id = $this->result->getId();

						$this->redirectAfterSave(!empty($this->_request["submit"]), $this->result);
					}
					else
					{
						$errors = $this->result->getErrors();

						$propList = $this->getProps();

						foreach($errors as $error)
						{
							$this->errors[] = $error->getMessage();
							$code = $error->getCode();
							if(strpos($code, "FIELD_") === 0 && isset($propList[substr($code, strlen("FIELD_"))]))
								$propList[substr($code, strlen("FIELD_"))]["ERROR"] = "Y";
						}

						$this->setPropList($propList);
					}
				}

				if($this->userCanAll() && !empty($this->_request["delete"]))
				{
					$id = $this->_request["CODE"] > 0 ? $this->_request["CODE"] : $this->result->getId();
					$this->result = $this->delete($id);
					$this->redirectAfterDelete($this->result);
				}

				break;

			case "filter":

				$this->filterRequest = $this->checkFields($this->_request, $this->getFilterList());
				/*$this->filter = $this->getFilter($this->filterRequest);
				$FILTER_NAME = $this->getFilterName();
				global ${$FILTER_NAME};
				${$FILTER_NAME} = $this->filter;*/

				break;
			
			default:
				# code...
				break;
		}
	}

	/**
	 * Process incoming request.
	 * @return void
	 */
	protected function processRequest()
	{
		global $APPLICATION;
		try
		{
			$this->doActionsList();
		}
		catch (Main\SystemException $e)
		{
			if ($this->isAjax())
			{
				$APPLICATION->restartBuffer();
				echo CUtil::PhpToJSObject(array('STATUS' => 'ERROR', 'MESSAGE' => $e->getMessage()));
				die();
			}
			else
			{
				$this->errors[] = Main\Text\HtmlFilter::encode($e->getMessage());
			}
		}
	}

	protected function fillUrlTemplates()
	{
		$this->urlTemplates["temp"] = "";
	}

	/**
	 * Get common data from cache.
	 * @return mixed[]
	 */
	protected function getReferences()
	{
		/*$this->arParams['CACHE_GROUPS'] = (isset($this->arParams['CACHE_GROUPS']) && $this->arParams['CACHE_GROUPS'] == 'N' ? 'N' : 'Y');
		$obCache = new CPHPCache;

		if ($this->arParams['CACHE_GROUPS'] == 'Y')
		{
			$userGroups = implode(",", Main\UserTable::getUserGroupIds($this->getUserId()));
			$cacheId = implode("-", array(__CLASS__, $this->getLanguageId(), $this->getSiteId(), $userGroups));
		}
		else
			$cacheId = implode("-", array(__CLASS__, $this->getLanguageId(), $this->getSiteId()));

		$cached = array();
		if ($obCache->StartDataCache($this->arParams["CACHE_TIME"], $cacheId, $this->getSiteId().'/'.$this->getRelativePath().'/reference'))
		{
			$cached = array();
			
			$obCache->EndDataCache($cached);
		}
		else
		{
			$cached = $obCache->GetVars();
		}*/

		$cached = array();

		return $cached;
	}

	/**
	 * Prepares $this->filter for getList() method.
	 * @return void
	 */
	protected function prepareFilter()
	{
		$FILTER_NAME = $this->getFilterName();
		global ${$FILTER_NAME};
		$this->filter = (array) ${$FILTER_NAME};
	}

	protected function getFilterName()
	{
		if (strlen($this->arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"]))
			$this->arParams["FILTER_NAME"] = "arrFilter";
		return $this->arParams["FILTER_NAME"];
	}

	/**
	 * Prepares $this->selectFields for getList() method.
	 * @return void
	 */
	protected function prepareSelectFields()
	{
		$this->selectFields = array();
	}

	/**
	 * Get items for view.
	 * @return mixed[]  array('ID' => array(), 'ID' => array(), ...)
	 */
	protected function getItems()
	{
		$items = array();
		$this->pageNavigation = $this->getPageNavigation($this->arParams["NAV_ON_PAGE"]);

		$parameters = $this->getParametrs($this->getOrder(), $this->filter, $this->arParams["NAV_ON_PAGE"], $this->arParams["SELECT"]);
		$result = $this->getList($parameters);
		while($fields = $result->fetch())
			$items[$fields["ID"]] = $fields;

		try
		{
			if ($parameters["count_total"])
				$this->pageNavigation->setRecordCount($result->getCount());
		}
		catch (\Bitrix\Main\ObjectPropertyException $e) {}
		
		return $items;
	}

	/**
	 * Get main data
	 * @return void
	 */
	protected function prepareData()
	{
		$this->fillUrlTemplates();

		$this->data = $this->getReferences();

		$this->prepareFilter();
		$this->prepareSelectFields();
		$this->items = $this->getItems();
	}

	public static function addPostData(&$data, array $postData = array())
	{
		$data = (array) $data;
		foreach($postData as $key => $val)
		{
			if(is_array($val))
			{
				$data[$key] = (array) $data[$key];
				foreach($val as $k => $v)
					if (intval($k) > 0)
						$data[$key][$k] = $v;
			}
			else
				$data[$key] = $val;
		}
	}

	protected function getButtons()
	{
		$arButtons = [
			[
				"NAME" => "apply",
				"VALUE" => Loc::GetMessage("BUTTON_APPLY")
			]
		];
		if (strlen($this->arParams["LIST_URL"]) > 0 && $_REQUEST["iframe"] != "y" && $_REQUEST["ajax"] != "y")
		{
			array_unshift($arButtons, [
				"NAME" => "submit",
				"VALUE" => Loc::GetMessage("BUTTON_SAVE"),
				"CLASS" => "btn-primary"
			]);
			$arButtons[] = [
				"NAME" => "cancel",
				"USER_TYPE" => "button",
				"VALUE" => Loc::GetMessage("BUTTON_CANCEL"),
				"CLASS" => "btn-link pull-right",
				"ATTR" => [
					"onclick" => "location.href='".\CUtil::JSEscape($this->arParams["LIST_URL"])."';"
				]
			];
		}
		return $arButtons;
	}

	/**
	 * Prepare data to render.
	 * @return void
	 */
	protected function formatResult()
	{
		$this->prepareResult();
		$this->arResult["CAN_EDIT"] = $this->userCanEdit() ? "Y" : "N";
		$this->arResult["CAN_DELETE"] = $this->userCanAll() ? "Y" : "N";

		switch ($this->type) 
		{
			case "list":

				global $APPLICATION;
			
				$this->arResult["SORTS"] = $this->getOrderList($this->arParams["SELECT"]);

				$this->arResult["FILTER"] = $this->filter;

				$this->arResult["ITEMS"] = $this->items;
				
				$this->arParams["PAGER_TEMPLATE"] = trim($this->arParams["PAGER_TEMPLATE"]);
				ob_start();
				$APPLICATION->IncludeComponent(
					"bitrix:main.pagenavigation",
					$this->arParams["PAGER_TEMPLATE"],
					array(
						"NAV_OBJECT" => $this->pageNavigation,
						//"SEF_MODE" => "Y",
					),
					null,
					array('HIDE_ICONS' => 'Y')
				);
				$this->arResult["NAV_STRING"] = ob_get_contents();
				ob_end_clean();

				break;

			case "detail":

				$this->arParams['ID'] = $this->id;
				$this->arResult["PROPERTY_LIST"] = $this->getProps();
				$this->arResult['ITEM'] = $this->item;
				$this->arResult['BUTTONS'] = $this->getButtons();
				$this->arResult["PROPERTY_LIST"] = $this->prepareProps($this->arResult["PROPERTY_LIST"], $this->arResult['ITEM']);

				break;

			case "filter":

				$this->arResult['ITEM'] = $this->filterRequest;

				break;
			
			default:
				# code...
				break;
		}

		$this->arResult["MESSAGE"] = htmlspecialcharsex($this->_request["strIMessage"]);
		$this->arResult['ERRORS'] = $this->errors;
		$this->arResult['WARNINGS'] = $this->warnings;
	}

	protected function executeComponentList()
	{
		global $APPLICATION;

		if ($this->userCanRead())
		{
			$this->processRequest();
			$this->prepareData();
			$this->getItems();
			$this->formatResult();
		}
		else
		{
			$APPLICATION->AuthForm("");
		}
	}

	protected function executeComponentDetail()
	{
		global $APPLICATION;

		if ($this->userCanRead())
		{
			$this->id = !empty($this->arParams["CODE"]) ?  $this->arParams["CODE"] : $this->_request["CODE"];

			$propList = $this->getProps();

			$propList = $this->setInputLangTitle($propList);

			if (!$this->userCanEdit())
				Base::disableInput($propList);

			$this->setPropList($propList);

			$this->processRequest();

			$this->item = !empty($this->id) ? $this->getById($this->id) : array();

			$this->addPostData($this->item, $this->_request);
			
			$this->formatResult();
		}
		else
		{
			$APPLICATION->AuthForm("");
		}
	}

	protected function executeComponentFilter()
	{
		global $APPLICATION;
		if ($this->userCanRead())
		{
			$this->processRequest();

			$this->arResult["PROPERTY_LIST"] = $this->getFilterList();
			$this->arResult["PROPERTY_LIST"] = $this->setInputLangTitle($this->arResult["PROPERTY_LIST"]);
			$this->formatResult();
		}
		else
		{
			$APPLICATION->AuthForm("");
		}
	}

	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		/** @global CMain $APPLICATION */
		global $APPLICATION;
		try
		{
			if (!$this->userCanRead())
				$APPLICATION->AuthForm("");

			$this->checkModules();

			if($this->arParams["IBLOCK_ID"] > 0)
				$this->setCatalogID($this->arParams["IBLOCK_ID"]);

			$this->obj->onProlog();

			$this->arParams = $this->obj->onPrepareParams($this->arParams);

			switch ($this->type) 
			{
				case "list":
					$this->executeComponentList();
					break;

				case "detail":

					if (!empty($this->_request["print"]))
						$componentPage = "print";

					$this->executeComponentDetail();
					break;

				case "filter":
					$this->executeComponentFilter();
					break;
				
				case "complex":
				default:
					
					if (!empty($this->_request["edit"]))
						$componentPage = "detail";
					else
						$componentPage = "list";

					$this->arParams["EDIT_URL"] = $APPLICATION->GetCurPage("", array("edit", "delete", "CODE"));
					if (empty($this->arParams["LIST_URL"])) $this->arParams["LIST_URL"] = $this->arParams["EDIT_URL"];

					$this->checkDoc();

					break;
			}
			$this->arResult = $this->resultModifier($this->arResult, $this->arParams);

			if ($this->isAjax())
			{
				$APPLICATION->RestartBuffer();
				header('Content-Type: application/json; charset=' . LANG_CHARSET);
				echo json_encode($this->arResult);
				die();
			}

			if ($componentPage == "print")
				$APPLICATION->RestartBuffer();

			if (!$this->initComponentTemplate($componentPage))
			{
				$templateName = $this->getTemplateName();
				if (empty($templateName)) $templateName = ".default";

				if (!is_dir($_SERVER["DOCUMENT_ROOT"].$this->getPath()."/templates/".$templateName))
					mkdir($_SERVER["DOCUMENT_ROOT"].$this->getPath()."/templates/".$templateName, BX_DIR_PERMISSIONS, true);
				$customTemplateType = "default".(empty($this->type) || $this->type == "complex" ? "" : ".".$this->type);
				$dirPath = str_replace("\\", "/", __DIR__);
				$dirPath = strpos($dirPath, "/local/") !== false ? strstr($dirPath, "/local/") : strstr($dirPath, "/bitrix/");
				$customTemplatePath = $dirPath."/templates/".$customTemplateType."/".$templateName;
				$this->includeComponentTemplate($componentPage, $customTemplatePath);
			}
			else
				$this->includeComponentTemplate($componentPage);

			if ($componentPage == "print")
				die();
		}
		catch (Main\SystemException $e)
		{
			if ($this->isAjax())
			{
				$APPLICATION->restartBuffer();
				echo CUtil::PhpToJSObject(array('STATUS' => 'ERROR', 'MESSAGE' => $e->getMessage()));
				die();
			}

			ShowError($e->getMessage());
		}
	}

	public static function getComponentParametrs()
	{
		if(!\CModule::IncludeModule("iblock"))
			return;

		$arIBlockType = \CIBlockParameters::GetIBlockTypes();

		$arIBlock=array();
		$rsIBlock = \CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y"));
		while($arr=$rsIBlock->Fetch())
		{
			$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
		}

		$arComponentParameters = array(
			"GROUPS" => array(
				"PARAMS" => array(
					"NAME" => Loc::GetMessage("PARAMETRS_IBLOCK_PARAMS"),
					"SORT" => "200"
				),
			),

			"PARAMETERS" => array(
				//"SEF_MODE" => Array(),
				"IBLOCK_TYPE" => array(
					"PARENT" => "DATA_SOURCE",
					"NAME" => Loc::GetMessage("PARAMETRS_IBLOCK_TYPE"),
					"TYPE" => "LIST",
					"ADDITIONAL_VALUES" => "Y",
					"VALUES" => $arIBlockType,
					"REFRESH" => "Y",
				),

				"IBLOCK_ID" => array(
					"PARENT" => "DATA_SOURCE",
					"NAME" => Loc::GetMessage("PARAMETRS_IBLOCK_IBLOCK"),
					"TYPE" => "LIST",
					"ADDITIONAL_VALUES" => "Y",
					"VALUES" => $arIBlock,
					"REFRESH" => "Y",
				),

				"NAV_ON_PAGE" => array(
					"PARENT" => "PARAMS",
					"NAME" => Loc::GetMessage("PARAMETRS_NAV_ON_PAGE"),
					"TYPE" => "TEXT",
					"DEFAULT" => "50",
				),
			),
		);
		return $arComponentParameters;
	}

	protected function checkDoc()
	{
		global $APPLICATION, $USER;
		$readme = $_SERVER["DOCUMENT_ROOT"].$APPLICATION->GetCurDir()."README.md";

		if (!file_exists($readme) || ($USER->IsAdmin() && $_REQUEST["clear_cache"] == "Y"))
		{
			$doc = self::createDoc();
			$doc = "<!--AUTODOC-->
".$doc."
<!--/AUTODOC-->";
			if (file_exists($readme))
				$lastDoc = file_get_contents($readme);

			$pattern = "/".preg_quote("<!--AUTODOC-->", "/").".*".preg_quote("<!--/AUTODOC-->", "/")."/Us";

			if (preg_match($pattern, $lastDoc))
				$doc = preg_replace($pattern, $doc, $lastDoc);

			file_put_contents($readme, $doc);
		}
	}

	/*public function IncludeComponent($componentName, $componentTemplate, $arParams = array(), $parentComponent = null, $arFunctionParams = array())
	{
		print_p(1234);
		die();
	}*/
}