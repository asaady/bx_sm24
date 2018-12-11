<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2004 Bitrix                  #
# http://www.bitrix.ru                       #
# mailto:admin@bitrix.ru                     #
##############################################
*/

define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$STAT_RIGHT = $APPLICATION->GetGroupRight("statistic");
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/colors.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$width = intval($_GET["width"]);
$max_width = COption::GetOptionInt("statistic", "GRAPH_WEIGHT");
if($width <= 0 || $width > $max_width)
	$width = $max_width;

$height = intval($_GET["height"]);
$max_height = COption::GetOptionInt("statistic", "GRAPH_HEIGHT");
if($height <= 0 || $height > $max_height)
	$height = $max_height;

// create image canvas
$ImageHandle = CreateImageHandle($width, $height, "FFFFFF", true);

$colorFFFFFF = ImageColorAllocate($ImageHandle,255,255,255);
ImageFill($ImageHandle, 0, 0, $colorFFFFFF);

$arrX=Array();
$arrY=Array();
$arrayX=Array();
$arrayY=Array();

$M['WEEKDAY_0'] = "Sun";
$M['WEEKDAY_1'] = "Mon";
$M['WEEKDAY_2'] = "Tue";
$M['WEEKDAY_3'] = "Wed";
$M['WEEKDAY_4'] = "Thu";
$M['WEEKDAY_5'] = "Fri";
$M['WEEKDAY_6'] = "Sat";

$M['MONTH_1'] = "Jan";
$M['MONTH_2'] = "Feb";
$M['MONTH_3'] = "Mar";
$M['MONTH_4'] = "Apr";
$M['MONTH_5'] = "May";
$M['MONTH_6'] = "June";
$M['MONTH_7'] = "Jule";
$M['MONTH_8'] = "Aug";
$M['MONTH_9'] = "Sep";
$M['MONTH_10'] = "Oct";
$M['MONTH_11'] = "Nov";
$M['MONTH_12'] = "Dec";

/******************************************************
                Plot data
*******************************************************/

$site_filtered = (strlen($find_site_id)>0 && $find_site_id!="NOT_REF") ? true : false;
$arFilter = Array(
	"DATE1"		=> $find_date1,
	"DATE2"		=> $find_date2,
	"SITE_ID"	=> $find_site_id
	);

if ($find_graph_type!="date")
{
	$dt = $find_graph_type;
	$rs = CTraffic::GetSumList($find_graph_type, $arFilter);
	$ar = $rs->Fetch();

	switch ($dt)
	{
		case "hour":
			$start = 0; $end = 23; break;
		case "weekday":
			$start = 0; $end = 6; break;
		case "month":
			$start = 1; $end = 12; break;
	}
	$dtu = ToUpper($dt);
	for ($i=$start; $i<=$end; $i++)
	{
		$arrX[] = $i;
		if ($find_hit=="Y")		$arrY_hit[] = $ar[$dtu."_HIT_".$i];
		if ($find_host=="Y")	$arrY_host[] = $ar[$dtu."_HOST_".$i];
		if ($find_session=="Y")	$arrY_session[] = $ar[$dtu."_SESSION_".$i];
		if ($find_event=="Y")	$arrY_event[] = $ar[$dtu."_EVENT_".$i];
		if (!$site_filtered)
		{
			if ($find_guest=="Y")		$arrY_guest[] = $ar[$dtu."_GUEST_".$i];
			if ($find_new_guest=="Y")	$arrY_new_guest[] = $ar[$dtu."_NEW_GUEST_".$i];
		}
		if ($dt=="hour") $val = $i;
		elseif (LANGUAGE_ID=="ru" && function_exists("ImageTTFText")) $val = GetMessage("STAT_".$dtu."_".$i."_S");
		else $val = $M[$dtu."_".$i];
		$arrayX[] = $val;
	}
	/******************************************************
				axis X
	*******************************************************/

	$MinX = $start;
	$MaxX = $end;

	/******************************************************
				axis Y
	*******************************************************/

	$arrY = array();
	if ($find_hit=="Y")		$arrY = array_merge($arrY,$arrY_hit);
	if ($find_host=="Y")	$arrY = array_merge($arrY,$arrY_host);
	if ($find_session=="Y")	$arrY = array_merge($arrY,$arrY_session);
	if ($find_event=="Y")	$arrY = array_merge($arrY,$arrY_event);
	if (!$site_filtered)
	{
		if ($find_guest=="Y")		$arrY = array_merge($arrY,$arrY_guest);
		if ($find_new_guest=="Y")	$arrY = array_merge($arrY,$arrY_new_guest);
	}
	$arrayY = GetArrayY($arrY, $MinY, $MaxY);
}
else
{
	$rsDays = CTraffic::GetDailyList(($by="s_date"), ($order="asc"), $v1, $arFilter, $v2);
	while($arData = $rsDays->Fetch())
	{
		$date = mktime(0, 0, 0, $arData["MONTH"], $arData["DAY"], $arData["YEAR"]);
		$date_tmp = 0;
		// when dates come not in order
		$next_date = AddTime($prev_date, 1, "D");
		if(($date > $next_date) && (intval($prev_date) > 0))
		{
			// fill date gaps
			$date_tmp = $next_date;
			while($date_tmp < $date)
			{
				$arrX[] = $date_tmp;
				if ($find_hit=="Y") $arrY_hit[] = 0;
				if ($find_host=="Y") $arrY_host[] = 0;
				if ($find_session=="Y") $arrY_session[] = 0;
				if ($find_event=="Y") $arrY_event[] = 0;
				if (!$site_filtered)
				{
					if ($find_guest=="Y") $arrY_guest[] = 0;
					if ($find_new_guest=="Y") $arrY_new_guest[] = 0;
				}
				$date_tmp = AddTime($date_tmp,1,"D");
			}
		}
		$arrX[] = $date;
		if ($find_hit=="Y") $arrY_hit[] = intval($arData["HITS"]);
		if ($find_host=="Y") $arrY_host[] = intval($arData["C_HOSTS"]);
		if ($find_session=="Y") $arrY_session[] = intval($arData["SESSIONS"]);
		if ($find_event=="Y") $arrY_event[] = intval($arData["C_EVENTS"]);
		if (!$site_filtered)
		{
			if ($find_guest=="Y") $arrY_guest[] = intval($arData["GUESTS"]);
			if ($find_new_guest=="Y") $arrY_new_guest[] = intval($arData["NEW_GUESTS"]);
		}
		$prev_date = $date;
	}

	/******************************************************
				axis X
	*******************************************************/

	$arrayX = GetArrayX($arrX, $MinX, $MaxX);

	/******************************************************
				axis Y
	*******************************************************/

	$arrY = array();
	if ($find_hit=="Y")		$arrY = array_merge($arrY,$arrY_hit);
	if ($find_host=="Y")	$arrY = array_merge($arrY,$arrY_host);
	if ($find_session=="Y")	$arrY = array_merge($arrY,$arrY_session);
	if ($find_event=="Y")	$arrY = array_merge($arrY,$arrY_event);
	if (!$site_filtered)
	{
		if ($find_guest=="Y")		$arrY = array_merge($arrY,$arrY_guest);
		if ($find_new_guest=="Y")	$arrY = array_merge($arrY,$arrY_new_guest);
	}

	$arrayY = GetArrayY($arrY, $MinY, $MaxY);
}

//EchoGraphData($arrayX, $MinX, $MaxX, $arrayY, $MinY, $MaxY, $arrX, $arrY);

/******************************************************
		coordinate grid
*******************************************************/

if (($dt=="weekday" || $dt=="month") && LANGUAGE_ID!="en")
{
	$arrTTF_FONT["X"] = array(
		"FONT_PATH"		=> "/bitrix/modules/statistic/ttf/verdana.ttf",
		"FONT_SIZE"		=> 8,
		"FONT_SHIFT"	=> 12
		);
}

DrawCoordinatGrid($arrayX, $arrayY, $width, $height, $ImageHandle, "FFFFFF", "B1B1B1", "000000", 15, 2, $arrTTF_FONT);

/******************************************************
		data plot
*******************************************************/

if ($find_hit=="Y")
	Graf($arrX, $arrY_hit, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["HITS"], "N");

if ($find_host=="Y")
	Graf($arrX, $arrY_host, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["HOSTS"], "N");

if ($find_session=="Y")
	Graf($arrX, $arrY_session, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["SESSIONS"], "N");

if ($find_event=="Y")
	Graf($arrX, $arrY_event, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["EVENTS"], "N");

if (!$site_filtered)
{
	if ($find_guest=="Y")
		Graf($arrX, $arrY_guest, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["GUESTS"], "N");

	if ($find_new_guest=="Y")
		Graf($arrX, $arrY_new_guest, $ImageHandle, $MinX, $MaxX, $MinY, $MaxY, $arrColor["NEW_GUESTS"], "N");
}

/******************************************************
		send image to client
*******************************************************/

ShowImageHeader($ImageHandle);
?>