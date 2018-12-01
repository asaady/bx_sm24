<?
function URLDateInterval($date_from, $date_to)
{
	global $APPLICATION;


	return $APPLICATION->GetCurPageParam("DATE_FROM=".$date_from."&DATE_TO=".$date_to."&filter=Y", array_keys($_GET));
}

$cur_day = date("d");
$cur_month = date("m");
$cur_year = date("Y");
$cur_quarter = floor($cur_month/3)*3;
$cur_quarter = ($cur_quarter < 10 ? "0" : "").$cur_quarter;
$today = $cur_day.".".$cur_month.".".$cur_year;

$arResult["FILTER_DATE"] = array(
	"MONTH" => URLDateInterval("01.".$cur_month.".".$cur_year, $today),
	"QUARTER" => URLDateInterval("01.".$cur_quarter.".".$cur_year, $today),
	"YEAR" => URLDateInterval("01.01.".$cur_year, $today),
);

if (!isset($_REQUEST["DATE_FROM"]) && !isset($_REQUEST["DATE_TO"]))
{
	$_REQUEST["DATE_FROM"] = $arResult["ITEM"]["DATE_FROM"] = "01.".$cur_month.".".$cur_year;
	$_REQUEST["DATE_TO"] = $arResult["ITEM"]["DATE_TO"] = $today;
}