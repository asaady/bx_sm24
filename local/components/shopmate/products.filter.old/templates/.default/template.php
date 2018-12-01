<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);?>

<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<?function ShowInput($propertyID, $arResult, $arParams, $inputPrefix = "")
{
	global $APPLICATION;
	if (intval($propertyID) > 0)
	{
		if (
			$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
			&&
			$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
		)
			$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
		elseif (
			(
				$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
				||
				$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
			)
			&&
			$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
		)
			$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";
	}
	elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
		$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

	if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y")
	{
		$inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? (intval($propertyID) > 0 ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : count($arResult["ELEMENT"][$propertyID])) : 0;
		$inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
	}
	else
	{
		$inputNum = 1;
	}

	if($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
		$INPUT_TYPE = "USER_TYPE";
	else
		$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];
	switch ($INPUT_TYPE):
		case "USER_TYPE":
			for ($i = 0; $i<$inputNum; $i++)
			{
				if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
				{
					$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
					$description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
				}
				elseif ($i == 0)
				{
					$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
					$description = "";
				}
				else
				{
					$value = "";
					$description = "";
				}
				echo call_user_func_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
					array(
						$arResult["PROPERTY_LIST_FULL"][$propertyID],
						array(
							"VALUE" => $value,
							"DESCRIPTION" => $description,
						),
						array(
							"VALUE" => "PROPERTY".$inputPrefix."[".$propertyID."][".$i."][VALUE]",
							"DESCRIPTION" => "PROPERTY".$inputPrefix."[".$propertyID."][".$i."][DESCRIPTION]",
							"FORM_NAME"=>"iblock_add",
						),
					));
			?><br /><?
			}
		break;
		case "TAGS":
			$APPLICATION->IncludeComponent(
				"bitrix:search.tags.input",
				"",
				array(
					"VALUE" => $arResult["ELEMENT"][$propertyID],
					"NAME" => "PROPERTY".$inputPrefix."[".$propertyID."][]",
					"TEXT" => 'size="'.$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"].'"',
				), null, array("HIDE_ICONS"=>"Y")
			);
			break;
		case "HTML":
			$LHE = new CLightHTMLEditor;
			$LHE->Show(array(
				'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY".$inputPrefix."[".$propertyID."][]"),
				'width' => '100%',
				'height' => '200px',
				'inputName' => "PROPERTY".$inputPrefix."[".$propertyID."][]",
				'content' => $arResult["ELEMENT"][$propertyID],
				'bUseFileDialogs' => false,
				'bFloatingToolbar' => false,
				'bArisingToolbar' => false,
				'toolbarConfig' => array(
					'Bold', 'Italic', 'Underline', 'RemoveFormat',
					'CreateLink', 'DeleteLink', 'Image', 'Video',
					'BackColor', 'ForeColor',
					'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
					'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
					'StyleList', 'HeaderList',
					'FontList', 'FontSizeList',
				),
			));
			break;
		case "T":
			for ($i = 0; $i<$inputNum; $i++)
			{

				if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
				{
					$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
				}
				elseif ($i == 0)
				{
					$value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
				}
				else
				{
					$value = "";
				}
			?>
	<textarea class="form-control" id="<?=$propertyID?>_INPUT" cols="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][]"<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?>><?=$value?></textarea>
			<?
			}
		break;

		case "S":
		case "N":?>
		<div class="input-group">
			<?for ($i = 0; $i<$inputNum; $i++)
			{
				//if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
				{
					$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : (is_array($arResult["ELEMENT"][$propertyID]) ? $arResult["ELEMENT"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID]);
				}
				/*elseif ($i == 0)
				{
					$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

				}
				else
				{
					$value = "";
				}*/
			?>
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime") CJSCore::Init(array('popup', 'date'));?>
			<input class="form-control <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CLASS"]?>"
				<?if($i==0):?> id="<?=$propertyID?>_INPUT"<?endif?> 
				<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?> onclick="BX.calendar({node:this, field:this, form: '', bTime: false, currentTime: '<?=(time()+date("Z")+CTimeZone::GetOffset())?>', bHideTime: true});"<?endif?>
				type="text" 
				name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][]"
				<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?> 
				<?if(!empty($arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_SET"])):?> data-info_set="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_SET"]?>"<?endif?> 
				size="25" 
				placeholder="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?>"
				value="<?=$value?>" />	
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span><?endif?>
			<?}?>
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] != "Y"):?>
			<input class="form-control inclone__block <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CLASS"]?>" type="text" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][]" size="25" value="" />
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span><?endif?>
			<br /><span class="btn btn-default inclone__btn">+</span>
			<?endif?>
		</div>
		<?break;
		case "F":
			for ($i = 0; $i<$inputNum; $i++)
			{
				$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
				?>
	<input type="hidden" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
	<input class="form-control" id="<?=$propertyID?>_INPUT" type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" /><br />
				<?

				if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
				{
					?>
<input type="checkbox" name="DELETE_FILE[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" class="form-control" id="file_delete_<?=$propertyID?>_<?=$i?>" value="Y" /><label for="file_delete_<?=$propertyID?>_<?=$i?>"><?=GetMessage("IBLOCK_FORM_FILE_DELETE")?></label><br />
					<?

					if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"])
					{
						?>
<img src="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>" height="<?=$arResult["ELEMENT_FILES"][$value]["HEIGHT"]?>" width="<?=$arResult["ELEMENT_FILES"][$value]["WIDTH"]?>" border="0" /><br />
						<?
					}
					else
					{
						?>
<?=GetMessage("IBLOCK_FORM_FILE_NAME")?>: <?=$arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"]?><br />
<?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?> b<br />
[<a href="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>"><?=GetMessage("IBLOCK_FORM_FILE_DOWNLOAD")?></a>]<br />
						<?
					}
				}
			}

		break;
		case "L":

			if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
				$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
			elseif ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "AJAX")
				$type = "ajax";
			else
				$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

			switch ($type):
				case "checkbox":
				case "radio":
					if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
					else $sKey = "ELEMENT";
					foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
					{
						$checked = false;
						//if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
						{
							if(is_array($arResult[$sKey][$propertyID]))
							{
								foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
									if ($key == $arElEnum["VALUE"])
									{
										$checked = true;
										break;
									}
							}
							elseif($key == $arResult[$sKey][$propertyID]) $checked = true;
						}
						/*else
						{
							if ($arEnum["DEF"] == "Y") $checked = true;
						}*/

						?>
		<input type="<?=$type?>" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>"<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?> value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label><br />
						<?
					}
				break;

				case "ajax":
				case "dropdown":
				case "multiselect":
				?>
		<select class="form-control<?if($type=="ajax"):?> ajax_select<?endif?> <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CLASS"]?>"<?if($type=="ajax"):?> data-url="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["URL"]?>"<?endif?><?if(strlen($arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_URL"])):?> data-info_url="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_URL"]?>"<?endif?> id="<?=$propertyID?>_INPUT" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>]<?=$type=="multiselect" || $type=="ajax" ? "[]" : ""?>"<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?><?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y"):?> size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" multiple="multiple"<?endif?> placeholder="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?>">
			<option value=""> - <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?> - </option>
				<?
					if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
					else $sKey = "ELEMENT";

					foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
					{
						$checked = false;
						//if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
						{
							if(is_array($arResult[$sKey][$propertyID]))
							{
								foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
									if ($key == $arElEnum["VALUE"])
									{
										$checked = true;
										break;
									}
							}
							elseif($key == $arResult[$sKey][$propertyID]) $checked = true;
						}
						/*else
						{
							if ($arEnum["DEF"] == "Y") $checked = true;
						}*/
						?>
			<option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?><?if(is_array($arEnum["DATA"])) foreach($arEnum["DATA"] as $dname => $dval) echo " data-".$dname."=\"".$dval."\"";?>><?=$arEnum["VALUE"]?></option>
						<?
					}
				?>
		</select>
				<?
				break;

			endswitch;
		break;
	endswitch;

}?>
<form action="<?=POST_FORM_ACTION_URI?>" method="get" enctype="multipart/form-data">
	<?/*<p>Мои фильтры:&nbsp;&nbsp; <a href="#">Товары этого месяца</a>&nbsp;&nbsp;<a href="#">Неоплаченные товары</a></p>*/?>
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
	<div class="row mb20">
	<?foreach ($arResult["PROPERTY_LIST_GROUP"][0] as $propertyID):?> 
		<?/*if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif*/?>
		<div class="col-lg-3">
			<?$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] = intval($propertyID) > 0 ? $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] : (!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID));?>
			<?ShowInput($propertyID, $arResult, $arParams);?>
		</div>
	<?endforeach;?>
	</div>
	<div class="row mb20">
	<?foreach ($arResult["PROPERTY_LIST_GROUP"][2] as $propertyID):?> 
		<?/*if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif*/?>
		<div class="col-lg-3">
			<?$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] = intval($propertyID) > 0 ? $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] : (!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID));?>
			<?ShowInput($propertyID, $arResult, $arParams);?>
		</div>
	<?endforeach;?>
	</div>
	<div class="row">
		<div class="col-md-10">
		<?foreach ($arResult["PROPERTY_LIST_GROUP"][3] as $key => $propertyID):?> 
			<div class="<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C"):?>ckbox ckbox-primary <?endif?>inline-block<?if($key > 0):?> ml10<?endif?>">
				<?$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] = intval($propertyID) > 0 ? $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] : (!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID));?>
				<?ShowInput($propertyID, $arResult, $arParams);?>
				<?/*<label for="<?=$propertyID?>_INPUT"><?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?></label>*/?>
			</div>
		<?endforeach;?>
			<!-- rdio --> 
		</div>
		<div class="col-md-2">
			<div class="btn-list clearfix">
			<?if (strlen($arParams["LIST_URL"]) > 0):?>
				<input class="btn btn-primary pull-right" type="submit" name="submit" value="Найти" />
			<?endif?>
			<?if (strlen($arParams["LIST_URL"]) > 0 && !empty($_REQUEST["submit"])):?>
				<input class="btn btn-danger pull-right"
					type="button"
					name="iblock_cancel"
					value="Сбросить"
					onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
				>
			<?endif?>
			</div>
		</div>
	</div>
<?endif?>
</form>
