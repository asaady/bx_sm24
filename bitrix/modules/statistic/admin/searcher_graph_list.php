<?
/*
##############################################
# Bitrix: SiteManager                        #
# Copyright (c) 2004 Bitrix                  #
# http://www.bitrix.ru                       #
# mailto:admin@bitrix.ru                     #
##############################################
*/
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$sTableID = "tbl_graph_list";
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/prolog.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/statistic/colors.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$STAT_RIGHT = $APPLICATION->GetGroupRight("statistic");
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
InitSorting();
$err_mess = "File: ".__FILE__."<br>Line: ";
define("HELP_FILE","searcher_list.php");

/***************************************************************************
				Functions
***************************************************************************/

$arrDef = array();
$rs = CSearcher::GetList(($v1="s_total_hits"), ($v2="desc"), array(), $v3);
while ($ar = $rs->Fetch())
{
	if ($ar["DIAGRAM_DEFAULT"]=="Y") $arrDef[] = $ar["ID"];
	$arrSEARCHERS[$ar["ID"]] = $ar["NAME"]." [".$ar["ID"]."]";
}

if($lAdmin->IsDefaultFilter())
{
	if (is_array($arrSEARCHERS))
	{
		reset($arrSEARCHERS);
		while (list($key,$value)=each($arrSEARCHERS))
		{
			if ($i<=9 && in_array($key, $arrDef))
			{
				$find_searchers[] = $key;
				$i++;
			}
		}
	}
	$find_date1_DAYS_TO_BACK = 90;
	$set_filter="Y";
}

if (is_array($find_searchers)) $find_searchers = array_unique($find_searchers);

$arFilterFields = array(
	"find_searchers",
	"find_summa",
	"find_date1",
	"find_date2"
	);

$lAdmin->InitFilter($arFilterFields);
if(AdminListCheckDate($lAdmin, array("find_date1"=>$find_date1, "find_date2"=>$find_date2)))
{
	$str = (is_array($find_searchers)) ? implode(" | ",$find_searchers) : "";
	$arFilter = Array(
		"SEARCHER_ID"	=> $str,
		"DATE1"			=> $find_date1,
		"DATE2"			=> $find_date2,
		"SUMMA"			=> $find_summa
		);
}
else
{
	$arFilter = array();
}
$arrDays = CSearcher::GetGraphArray($arFilter, $arrLegend);

$aMenu = array();
$aMenu[] = array(
	"TEXT"	=> GetMessage("STAT_LIST"),
	"TITLE"=>GetMessage("STAT_LIST_TITLE"),
	"LINK"=>"searcher_list.php?lang=".LANG,
	"ICON" => "btn_list"
);
$aMenu[] = array("SEPARATOR"=>"Y");
$aMenu[] = array(
	"LINK"=>"searcher_diagram_list.php?lang=".LANGUAGE_ID."&set_default=Y",
	"ICON"=>"btn_stat_diagram",
	"TEXT"=>GetMessage("STAT_DIAGRAM_S"),
	"TITLE"=>GetMessage("STAT_DIAGRAM_TITLE"),
);

$lAdmin->AddAdminContextMenu($aMenu, false, false);
##### graf
$lAdmin->BeginCustomContent();

if (function_exists("ImageCreate")) :
if (strlen($strError)<=0 && count($arrLegend)>0 && count($arrDays)>1) :
	$width = COption::GetOptionString("statistic", "GRAPH_WEIGHT");
	$height = COption::GetOptionString("statistic", "GRAPH_HEIGHT");
?>
<div class="graph">
<table cellpadding="0" cellspacing="0" border="0" class="graph">
	<tr>
		<td>
			<img class="graph" src="searcher_graph.php?<?=GetFilterParams($arFilterFields)?>&width=<?=$width?>&height=<?=$height?>&lang=<?=LANGUAGE_ID?>" width="<?=$width?>" height="<?=$height?>">

			<table border="0" cellspacing="0" cellpadding="0" class="legend">
			<?
			reset($arrLegend);
			while(list($keyL, $arrL) = each($arrLegend)) :
				$color = $arrL["COLOR"];
			?>
				<tr>
					<td><img src="/bitrix/admin/graph_legend.php?color=<?=$color?>" width="45" height="2"></td>
					<td nowrap>
					<?
					if ($arrL["COUNTER_TYPE"]=="DETAIL") :
						?>[<a title="<?=GetMessage("STAT_SEARCHER_LIST_OPEN")?> " href="/bitrix/admin/searcher_list.php?lang=<?=LANGUAGE_ID?>&amp;find_id=<?=$keyL?>&amp;set_filter=Y"><?=$keyL?></a>]&nbsp;<a title="<?=GetMessage("STAT_SEARCHER_DYNAMIC")?>" href="/bitrix/admin/searcher_dynamic_list.php?lang=<?=LANGUAGE_ID?>&amp;find_searcher_id=<?=$keyL?>&amp;find_date1=<?echo $arFilter["DATE1"]?>&amp;find_date2=<?=$arFilter["DATE2"]?>&amp;set_filter=Y"><?=$arrL["NAME"]?></a><?
					else :
						?><?=GetMessage("STAT_SUMMARIZED")?><?
					endif;
					?></td>
				</tr>
			<?endwhile;?>
			</table>
		</td>
	</tr>
</table>
</div>

<?
else :
	$lAdmin->AddFilterError(GetMessage("STAT_NO_DATA"));
endif;?>
<?
else:
	$lAdmin->AddFilterError(GetMessage("STAT_GD_NOT_INSTALLED"));
endif;

$lAdmin->EndCustomContent();
########### end of graph
$lAdmin->CheckListMode();


$APPLICATION->SetTitle(GetMessage("STAT_RECORDS_LIST"));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<form name="form1" method="POST" action="<?=$APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
		GetMessage("STAT_FL_SEACHERS"),
        )
);

$oFilter->Begin();
?>
<tr>
	<td width="0%" nowrap><?echo GetMessage("STAT_F_PERIOD")." (".CLang::GetDateFormat("SHORT")."):"?></td>
	<td width="0%" nowrap><?echo CalendarPeriod("find_date1", $find_date1, "find_date2", $find_date2, "form1", "Y")?></td>
</tr>
<?
if (is_array($arrSEARCHERS))
{
	reset($arrSEARCHERS);
	function multiselect_sort($a,$b)
	{
		global $find_searchers, $arrSEARCHERS;
		$ret=0;
		$ka = false;
		$kb = false;
		if (is_array($find_searchers))
		{
			$ka = array_search($a, $find_searchers);
			$kb = array_search($b, $find_searchers);
		}
		if ($ka!==false && $kb!==false)
		{
			if ($ka==$kb) $ret=0;
			elseif (strtolower($ka)>strtolower($kb)) $ret=1;
			else $ret=-1;
		}
		if ($ka===false && $kb!==false) $ret=1;
		if ($ka!==false && $kb===false) $ret=-1;
		if ($ret==0)
		{
			if (strtolower($arrSEARCHERS[$a])>strtolower($arrSEARCHERS[$b])) $ret=1;
			if (strtolower($arrSEARCHERS[$a])<strtolower($arrSEARCHERS[$b])) $ret=-1;
		}
		return $ret;
	}
	uksort($arrSEARCHERS, "multiselect_sort");
	$ref = array();
	$ref_id = array();
	if (is_array($arrSEARCHERS))
	{
		$ref = array_values($arrSEARCHERS);
		$ref_id = array_keys($arrSEARCHERS);
	}
}
?>
<tr valign="top">
	<td width="0%" nowrap><?echo GetMessage("STAT_F_SEACHERS")?><br><IMG SRC="/bitrix/images/statistic/mouse.gif" WIDTH="44" HEIGHT="21" BORDER=0 ALT=""></td>
	<td width="0%" nowrap><?
	$arr = array("reference"=>array(GetMessage("STAT_SEPARATED"), GetMessage("STAT_SUMMA")), "reference_id"=>array("N","Y"));
	echo SelectBoxFromArray("find_summa", $arr, htmlspecialchars($find_summa), "", " style=\"width:100%\"")."<br>";
	echo SelectBoxMFromArray("find_searchers[]",array("REFERENCE"=>$ref, "REFERENCE_ID"=>$ref_id), $find_searchers,"",false,"11", "class=\"typeselect\" style=\"width:100%\"");?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage()));
$oFilter->End();
#############################################################
?>
</form>

<?
if($message)
	echo $message->Show();
$lAdmin->DisplayList();
?>

<?require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
