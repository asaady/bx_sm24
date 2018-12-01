<?php
define("NOT_CHECK_PERMISSIONS", true);
define("NO_AGENT_CHECK", true);
$GLOBALS["DBType"] = 'mysql';
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../../..';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
function initBitrixCore()
{
	// manual saving of DB resource
    global $DB;
    $app = \Bitrix\Main\Application::getInstance();
    $con = $app->getConnection();
    $DB->db_Conn = $con->getResource();
    // "authorizing" as admin
    $_SESSION["SESS_AUTH"]["USER_ID"] = 1;
	error_reporting(E_ERROR);
	ini_set('display_errors', 'Off');
}
