<?
IncludeModuleLangFile(__FILE__);

if(class_exists("statistic")) return;
Class statistic extends CModule
{
	var $MODULE_ID = "statistic";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	var $errors;

	function statistic()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = $STATISTIC_VERSION;
			$this->MODULE_VERSION_DATE = $STATISTIC_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("STAT_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("STAT_MODULE_DESCRIPTION");
		$this->MODULE_CSS = "/bitrix/modules/statistic/statistic.css";
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		$arAllErrors = array();

		// check if module was deinstalled without table save
		$DATE_INSTALL_TABLES = "";
		$no_tables = "N";
		if(!$DB->Query("SELECT count('x') FROM b_stat_day WHERE 1=0", true))
		{
			// last installation date have to be current
			$DATE_INSTALL_TABLES = date("d.m.Y H:i:s",time());
			$no_tables = "Y";
		}

		if($no_tables == "Y")
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/db/".strtolower($DB->type)."/install.sql");
		}

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		RegisterModule("statistic");

		RegisterModuleDependences("main", "OnPageStart", "statistic", "CStopList", "Check", "100");
		RegisterModuleDependences("main", "OnBeforeProlog", "statistic", "CStatistics", "Keep", "100");
		RegisterModuleDependences("search", "OnSearch", "statistic", "CStatistics", "OnSearch", "100");
		RegisterModuleDependences("main", "OnEpilog", "statistic", "CStatistics", "Set404", "100");
		RegisterModuleDependences("main", "OnBeforeProlog", "statistic", "CStatistics", "StartBuffer", "1000");
		RegisterModuleDependences("main", "OnAfterEpilog", "statistic", "CStatistics", "EndBuffer", "10");
		RegisterModuleDependences("main", "OnEventLogGetAuditTypes", "statistic", "CStatistics", "GetAuditTypes", 10);

		RegisterModuleDependences("statistic", "OnCityLookup", "statistic", "CCityLookup_geoip_mod", "OnCityLookup", "100");
		RegisterModuleDependences("statistic", "OnCityLookup", "statistic", "CCityLookup_geoip_extension", "OnCityLookup", "200");
		RegisterModuleDependences("statistic", "OnCityLookup", "statistic", "CCityLookup_geoip_pure", "OnCityLookup", "300");
		RegisterModuleDependences("statistic", "OnCityLookup", "statistic", "CCityLookup_stat_table", "OnCityLookup", "400");

		if (strlen($DATE_INSTALL_TABLES)>0)
		{
			COption::SetOptionString("main", "INSTALL_STATISTIC_TABLES", $DATE_INSTALL_TABLES, "Date of installation of statistics module tables");
		}

		// init counters
		if(array_key_exists("allow_initial", $arParams) && ($arParams["allow_initial"] == "Y"))
		{
			$strSql = "SELECT ID FROM b_stat_day";
			$e = $DB->Query($strSql, false, $err_mess.__LINE__);
			if (!($er = $e->Fetch()))
			{
				if (intval($arParams["START_HITS"])>0 || intval($arParams["START_HOSTS"])>0 || intval($arParams["START_GUESTS"])>0)
				{
					$arFields = Array(
						"DATE_STAT"	=> $DB->GetNowDate(),
						"HITS"		=> intval($arParams["START_HITS"]),
						"C_HOSTS"	=> intval($arParams["START_HOSTS"]),
						"GUESTS"	=> intval($arParams["START_GUESTS"]),
						"NEW_GUESTS"	=> intval($arParams["START_GUESTS"]),
						);
					$DB->Insert("b_stat_day",$arFields, $err_mess.__LINE__);
				}
			}
		}

		CAgent::AddAgent("CStatistics::SetNewDay();","statistic", "Y", 86400, "", "Y", "", 200);
		CAgent::AddAgent("CStatistics::CleanUpStatistics_1();","statistic", "N", 86400, "", "Y", "", 50);
		CAgent::AddAgent("CStatistics::CleanUpStatistics_2();","statistic", "N", 86400, "", "Y", "", 30);
		CAgent::AddAgent("CStatistics::CleanUpSessionData();","statistic","N",7200);
		CAgent::AddAgent("CStatistics::CleanUpPathCache();","statistic", "N", 3600);
		CAgent::RemoveAgent("SendDailyStatistics();","statistic");

		if(strpos($_SERVER["SERVER_SOFTWARE"], "(Win32)")<=0)
		{
			$arr = getdate();
			$ndate = mktime(9,0,0,$arr["mon"],$arr["mday"],$arr["year"]);
			CAgent::AddAgent("SendDailyStatistics();", "statistic", "Y", 86400, "", "Y", ConvertTimeStamp($ndate, "FULL"), 25);
		}

		if($no_tables=="Y")
		{
			$arAllErrors[] = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/statistic/install/db/".strtolower($DB->type)."/searchers.sql");
			$arAllErrors[] = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/statistic/install/db/".strtolower($DB->type)."/browsers.sql");
			$arAllErrors[] = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/statistic/install/db/".strtolower($DB->type)."/adv.sql");
		}

		// ip-to-country
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/ip_tools.php");
		i2c_load_countries();
		if(!array_key_exists("CREATE_I2C_INDEX", $arParams) || ($arParams["CREATE_I2C_INDEX"] == "Y"))
			i2c_create_db($total_reindex, $reindex_success, $step_reindex, $int_prev);

		$fname = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/db/".strtolower($DB->type)."/optimize.sql";
		if(file_exists($fname))
		{
			$arAllErrors[] = $DB->RunSQLBatch($fname);
		}

		$this->errors = array();
		foreach($arAllErrors as $ar)
		{
			if(is_array($ar))
			{
				foreach($ar as $strError)
					$this->errors[] = $strError;
			}
		}
		if(count($this->errors) < 1)
			$this->errors = false;

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if(!array_key_exists("savedata", $arParams) || ($arParams["savedata"] != "Y"))
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/db/".strtolower($DB->type)."/uninstall.sql");
			COption::RemoveOption("main","INSTALL_STATISTIC_TABLES");

			$db_res = $DB->Query("SELECT ID FROM b_file WHERE MODULE_ID = 'statistic'");
			while($arRes = $db_res->Fetch())
				CFile::Delete($arRes["ID"]);
		}

		UnRegisterModuleDependences("main", "OnPageStart", "statistic", "CStopList", "Check");
		UnRegisterModuleDependences("main", "OnBeforeProlog", "statistic", "CStatistics", "Keep");
		UnRegisterModuleDependences("search", "OnSearch", "statistic", "CStatistics", "OnSearch");
		UnRegisterModuleDependences("main", "OnEpilog", "statistic", "CStatistics", "Set404");
		UnRegisterModuleDependences("main", "OnEventLogGetAuditTypes", "statistic", "CStatistics", "GetAuditTypes");
		UnRegisterModuleDependences("main", "OnBeforeProlog", "statistic", "CStatistics", "StartBuffer");
		UnRegisterModuleDependences("main", "OnAfterEpilog", "statistic", "CStatistics", "EndBuffer");

		UnRegisterModule("statistic");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

	function InstallEvents()
	{
		global $DB;
		$sIn = "'STATISTIC_DAILY_REPORT', 'STATISTIC_ACTIVITY_EXCEEDING'";
		$rs = $DB->Query("SELECT count(*) C FROM b_event_type WHERE EVENT_NAME IN (".$sIn.") ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$ar = $rs->Fetch();
		if($ar["C"] <= 0)
		{
			include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/events/set_events.php");
		}
		return true;
	}

	function UnInstallEvents()
	{
		global $DB;
		$sIn = "'STATISTIC_DAILY_REPORT', 'STATISTIC_ACTIVITY_EXCEEDING'";
		$DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME IN (".$sIn.") ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME IN (".$sIn.") ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/public/bitrix", $_SERVER["DOCUMENT_ROOT"]."/bitrix", true, true);//all from bitrix
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/components/bitrix", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/public/bitrix/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/public/bitrix/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/statistic/");//icons
		DeleteDirFilesEx("/bitrix/images/statistic/");//images

		return true;
	}

	function DoInstall()
	{
		global $DB, $APPLICATION, $step;
		$STAT_RIGHT = $APPLICATION->GetGroupRight("statistic");
		if ($STAT_RIGHT>="W")
		{
			$step = IntVal($step);
			if($step < 2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage("STAT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/step1.php");
			}
			elseif($step == 2)
			{
				$db_install_ok = $this->InstallDB(array(
					"allow_initial" => $_REQUEST["allow_initial"],
					"START_HITS" => $_REQUEST["START_HITS"],
					"START_HOSTS" => $_REQUEST["START_HOSTS"],
					"START_GUESTS" => $_REQUEST["START_GUESTS"],
					"CREATE_I2C_INDEX" => $_REQUEST["CREATE_I2C_INDEX"],
				));
				if($db_install_ok)
				{
					$this->InstallEvents();
					$this->InstallFiles();
				}
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("STAT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/step2.php");
			}
		}
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step;
		$STAT_RIGHT = $APPLICATION->GetGroupRight("statistic");
		if ($STAT_RIGHT>="W")
		{
			$step = IntVal($step);
			if($step < 2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage("STAT_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/unstep1.php");
			}
			elseif($step == 2)
			{
				$this->UnInstallDB(array(
					"savedata" => $_REQUEST["savedata"],
				));
				//message types and templates
				if($_REQUEST["save_templates"] != "Y")
				{
					$this->UnInstallEvents();
				}
				$this->UnInstallFiles();
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("STAT_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/install/unstep2.php");
			}
		}
	}

	function GetModuleRightList()
	{
		$arr = array(
			"reference_id" => array("D","M","R","W"),
			"reference" => array(
				"[D] ".GetMessage("STAT_DENIED"),
				"[M] ".GetMessage("STAT_VIEW_WITHOUT_MONEY"),
				"[R] ".GetMessage("STAT_VIEW"),
				"[W] ".GetMessage("STAT_ADMIN"))
			);
		return $arr;
	}
}
?>