<?
$MODULE_ID = "yadadya.shopmate";

$time = new DateTime();
$time->add(new DateInterval('PT5M'));

CAgent::RemoveModuleAgents($MODULE_ID);

if(CModule::IncludeModule("catalog"))
{
	$result = \Bitrix\Catalog\StoreTable::GetList(array(
		"select" => array("ID"), 
		"filter" => array("ACTIVE" => "Y")
	));
	while($row = $result->fetch())
	{
		CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::cron(".$row["ID"].", \"SALE\");", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
		CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::cron(".$row["ID"].", \"PURCHASE\");", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
		CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::cron(".$row["ID"].", \"PROFIT\");", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
		CAgent::AddAgent("\Yadadya\Shopmate\FinanceReport::removeOldTasks();", "yadadya.shopmate", "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
	}
}
CAgent::AddAgent("SMDnc::Cron();", $MODULE_ID, "N", 300, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
CAgent::AddAgent("SMDnc::BadCron();", $MODULE_ID, "N", 12*60*60, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
CAgent::AddAgent("cronSendWithTime();", $MODULE_ID, "N", 12*60*60, $time->format('d.m.Y H:i:s'), "Y", $time->format('d.m.Y H:i:s'), 30);
?>
