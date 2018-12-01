<?
function URLDateInterval($date_from, $date_to)
{
	global $APPLICATION;
	return $APPLICATION->GetCurPageParam("DATE_FROM=".ConvertTimeStamp($date_from, "SHORT")."&DATE_TO=".ConvertTimeStamp($date_to, "SHORT").($_REQUEST["DEDUCTED"] == "N" ? "&DEDUCTED=N" : "")."&filter=Y", array_keys($_GET));
}
$today = time();
$arResult["FILTER_DATE"] = array(
	"TODAY" => URLDateInterval($today, $today),
	"WEEK" => URLDateInterval($today - 7 * 24 * 60 * 60, $today),
	"MONTH" => URLDateInterval($today - 30 * 24 * 60 * 60, $today),
	"QUARTER" => URLDateInterval($today - 4 * 30 * 24 * 60 * 60, $today),
);