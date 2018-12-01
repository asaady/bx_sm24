<?
IncludeModuleLangFile(__FILE__);
Class yadadya_shopmate extends CModule
{
	const MODULE_ID = 'yadadya.shopmate';
	var $MODULE_ID = 'yadadya.shopmate'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("yadadya.shopmate_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("yadadya.shopmate_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("yadadya.shopmate_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("yadadya.shopmate_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $APPLICATION;
		global $DB;
		global $errors;

		$errors = $DB->RunSQLBatch(__DIR__."/db/".strtolower($DB->type)."/install.sql");
		if (!empty($errors))
		{
			$APPLICATION->ThrowException(implode("", $errors));
			//return false;
		}
		
		include("internals.php");

		$modules_up = array("products_up.sql", "finance_up.sql", "dnc_up.sql", "overhead_up.sql");
		foreach($modules_up as $sql)
		{
			$errors = $DB->RunSQLBatch(__DIR__."/db/".strtolower($DB->type)."/".$sql);
			if (!empty($errors))
			{
				$APPLICATION->ThrowException(implode("", $errors));
				//return false;
			}
		}

		include("events.php");

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $APPLICATION;
		$errors = $DB->RunSQLBatch(__DIR__."/db/".strtolower($DB->type)."/uninstall.sql");
		if (!empty($errors))
		{
			$APPLICATION->ThrowException(implode("", $errors));
		}
		$DB->Query("DELETE FROM b_module_to_module WHERE TO_MODULE_ID=\"".self::MODULE_ID."\"");

		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}

		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		include("agents.php");
		include("mess_events.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;
		CAgent::RemoveModuleAgents(self::MODULE_ID);
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
