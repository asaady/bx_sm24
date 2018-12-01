<?
define("STOP_STATISTICS", true);
$SITE_ID = '';
if(
	isset($_REQUEST["SITE_ID"])
	&& is_string($_REQUEST["SITE_ID"])
	&& strlen($_REQUEST["SITE_ID"]) > 0
)
{
	$SITE_ID = substr(preg_replace("/[^a-z0-9_]/i", "", $_REQUEST["SITE_ID"]), 0, 2);
	define("SITE_ID", $SITE_ID);
}

if(
	isset($_REQUEST["ADMIN_SECTION"])
	&& is_string($_REQUEST["ADMIN_SECTION"])
	&& trim($_REQUEST["ADMIN_SECTION"]) == "Y"
)
{
	define("ADMIN_SECTION", true);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("yadadya.shopmate");
use \Yadadya\Shopmate\Egais;

if(!empty($_REQUEST["shop"]) || $_REQUEST["action"] == "conf")
{

	switch ($_REQUEST["action"]) 
	{
		case "del_list":

			if($_SERVER["REQUEST_METHOD"] == "DELETE")
			{
				Egais\EgaisCustom::removeDelXML($_REQUEST["shop"]);
			}
			else
			{
				$xml = Egais\EgaisCustom::getDelXML($_REQUEST["shop"]);
				global $APPLICATION;
				header("Content-Type: text/xml");
				$APPLICATION->RestartBuffer();
				echo $xml;
				die();
			}
			break;

		case "refresh":

			Egais\EgaisCustom::refreshDel($_REQUEST["shop"]);

			break;

		case "conf":

			global $APPLICATION;
			header("Content-Type: text/xml");
			$APPLICATION->RestartBuffer();
			CModule::IncludeModule("yadadya.shopmate");
			echo "<conf>
	<site>".$_SERVER["HTTP_HOST"]."</site>
	<shop>".SMShops::getUserShop()."</shop>
</conf>";
			die();

			break;
		
		default:
			# code...
			break;
	}
}
else
{?>
<a href="/egais/yadadya.egais.custom.exe" download>Скачать</a> скрипт для чистки документов с УТМ
<br />
<a href="/egais/?action=conf" download="conf.txt">Скачать</a>Файл конфигурации магазина для скрипта
<?}



require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
?>
