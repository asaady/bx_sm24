<?
if($USER->IsAdmin()):

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array(
		"DIV" => "permissions",
		"TAB" => GetMessage("SM_OPTIONS_TAB_PERMISSIONS"),
		"TITLE" => GetMessage("SM_OPTIONS_TAB_TITLE_PERMISSIONS"),
		"OPTIONS" => array(),
	),
	array(
		"DIV" => "groups",
		"TAB" => GetMessage("SM_OPTIONS_TAB_GROUPS"),
		"TITLE" => GetMessage("SM_OPTIONS_TAB_TITLE_GROUPS"),
		"OPTIONS" => array(),
	),
);

$arGroups = array("REFERENCE"=>array(), "REFERENCE_ID"=>array());
$rsGroups = CGroup::GetDropDownList();
while($ar = $rsGroups->Fetch())
{
	$arGroups["REFERENCE"][] = $ar["REFERENCE"];
	$arGroups["REFERENCE_ID"][] = $ar["REFERENCE_ID"];
}

$arPerms = array("REFERENCE"=>array(), "REFERENCE_ID"=>array());
$tmpPerms = SM::GetModuleRightList();
$arPerms["REFERENCE_ID"] = $tmpPerms["reference_id"];
$arPerms["REFERENCE"] = $tmpPerms["reference"];

$arParts = array();

$tmpParts = SM::GetModuleChapterList();
foreach($tmpParts["reference_id"] as $keyPart => $part_id)
	$arParts[$part_id] = !empty($tmpParts["reference"][$keyPart]) ? $tmpParts["reference"][$keyPart] : $part_id;

$arGlobalGroups = array();

$tmpGroups = SM::GetModuleGlobalGroupsList();
foreach($tmpGroups["reference_id"] as $keyGroup => $group_id)
	$arGlobalGroups[$group_id] = !empty($tmpGroups["reference"][$keyGroup]) ? $tmpGroups["reference"][$keyGroup] : $group_id;

$arPersonalGroups = array();

$tmpGroups = SM::GetModulePersonalGroupsList();
foreach($tmpGroups["reference_id"] as $keyGroup => $group_id)
	$arPersonalGroups[$group_id] = !empty($tmpGroups["reference"][$keyGroup]) ? $tmpGroups["reference"][$keyGroup] : $group_id;


$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
	if(strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption("yadadya.shopmate");
	}
	else
	{
		foreach ($arParts as $part_id => $part_title)
		{
			$arRights = array();
			if(
				isset($_POST["perm_right"][$part_id]) && is_array($_POST["perm_right"][$part_id])
				&& isset($_POST["group_right"][$part_id]) && is_array($_POST["group_right"][$part_id])
			)
			{
				$keys = array_keys($_POST["perm_right"][$part_id]);
				foreach($keys as $i)
				{
					if(
						array_key_exists($i, $_POST["perm_right"][$part_id])
						&& array_key_exists($i, $_POST["group_right"][$part_id])
					)
					{
						$arRights[$_POST["perm_right"][$part_id][$i]][] = $_POST["group_right"][$part_id][$i];
					}
				}
			}

			foreach($arRights as $type_id => $groups)
				SM::SetPermission($part_id, $type_id, $groups);
		}

		foreach($arGlobalGroups as $group_id => $group_title)
		{
			SM::SetGlobalGroups($group_id, $_POST["global_group_".$group_id], $group_title);
		}
		foreach($arPersonalGroups as $group_id => $group_title)
		{
			SM::SetPersonalGroups($group_id, $_POST["personal_group_".$group_id], $group_title);
		}	
	}

	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}

$tabControl->Begin();
?>
<script>
function addNewTableRow(tableID, regexp, rindex)
{
	var tbl = document.getElementById(tableID);
	var cnt = tbl.rows.length-1;
	var oRow = tbl.insertRow(cnt);
	var col_count = tbl.rows[cnt-1].cells.length;

	for(var i=0;i<col_count;i++)
	{
		var oCell = oRow.insertCell(i);
		var html = tbl.rows[cnt-1].cells[i].innerHTML;
		oCell.innerHTML = html.replace(regexp,
			function(html)
			{
				return html.replace('[n'+arguments[rindex]+']', '[n'+(1+parseInt(arguments[rindex]))+']');
			}
		);
	}
}
</script>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
<?
$tabControl->BeginNextTab();
	?>
<?foreach($arParts as $part_id => $part_title):?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("SM_OPTIONS_PART_TITLE")?> <?=$part_title?>
	<tr>
		<td valign="top" align="center" colspan="2">
		<table border="0" cellspacing="0" cellpadding="0" class="internal" align="center" id="tblRIGHTS_<?=$part_id?>">
			<tr class="heading">
				<td><?echo GetMessage("SM_OPTIONS_USER_GROUPS")?></td>
				<td><?echo GetMessage("SM_OPTIONS_RIGHTS")?></td>
			</tr>
	<?
	$i = 0;
	foreach(SM::GetPermission($part_id) as $type_id => $groups):
		foreach($groups as $group):
	?>
			<tr>
				<td><?echo SelectBoxFromArray("group_right[".$part_id."][n".$i."]", $arGroups, $group, GetMessage("SM_OPTIONS_CHOOSE_GROUP"))?></td>
				<td><?echo SelectBoxFromArray("perm_right[".$part_id."][n".$i."]", $arPerms, $type_id, GetMessage("SM_OPTIONS_CHOOSE_TYPE"))?></td>
			</tr>
	<?
		$i++;
		endforeach;
	endforeach;
	//if($i == 0)
	{
		?>
			<tr>
				<td><?echo SelectBoxFromArray("group_right[".$part_id."][n".$i."]", $arGroups, false, GetMessage("SM_OPTIONS_CHOOSE_GROUP"))?></td>
				<td><?echo SelectBoxFromArray("perm_right[".$part_id."][n".$i."]", $arPerms, false, GetMessage("SM_OPTIONS_CHOOSE_TYPE"))?></td>
			</tr>
		<?
	}
	?>
		<tr>
			<td colspan="2" style="border:none">
			<input type="button" value="<?echo GetMessage("SM_OPTIONS_ADD_RIGHT")?>" onClick="addNewTableRow('tblRIGHTS_<?=$part_id?>', /right\[<?=$part_id?>\]\[(n)([0-9]*)\]/g, 2)">
			</td>
		</tr>
		</table>
		</td>
	</tr>
<?endforeach?>
<?$tabControl->BeginNextTab();?>
<?foreach($arGlobalGroups as $group_id => $group_title):?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("SM_OPTIONS_GROUP_TITLE")?> <?=$group_title?></td>
	</tr>
	<tr>
		<td valign="top" width="50%"><?=GetMessage("MAIN_REGISTER_GROUP")?></td>
		<td valign="middle" width="50%">
			<?echo SelectBoxMFromArray("global_group_".$group_id."[]", $arGroups, SM::GetGlobalGroups($group_id), GetMessage("SM_OPTIONS_CHOOSE_GROUP"))?>
		</td>
	</tr>
<?endforeach?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("SM_OPTIONS_GROUP_PERSONAL_TITLE")?></td>
	</tr>
<?foreach($arPersonalGroups as $group_id => $group_title):?>
	<tr>
		<td valign="top" width="50%"><?=$group_title?></td>
		<td valign="middle" width="50%">
			<?echo SelectBoxMFromArray("personal_group_".$group_id."[]", $arGroups, SM::GetPersonalGroups($group_id), GetMessage("SM_OPTIONS_CHOOSE_GROUP"))?>
		</td>
	</tr>
<?endforeach?>
<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?endif;?>