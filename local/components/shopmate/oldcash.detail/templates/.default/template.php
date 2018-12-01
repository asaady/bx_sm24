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

<?if($arResult["OPEN_PAYMENT"]):?>
<div class="modal fade bs-example-modal-lg-checkout modal_open" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<?/*<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>*/?>
			<h4 class="modal-title">Оплата</h4>
		</div>
		<div class="modal-body">
			<iframe src="<?=$APPLICATION->GetCurPageParam("pay_system_blank=html", array())?>" style="border: none;" height="250" width="350"></iframe>
		</div>
		</div>
	</div>
</div>
<?endif?>
<?if($arResult["OPEN_CANCEL"]):?>
<div class="modal fade bs-example-modal-lg-checkout modal_open" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<?/*<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>*/?>
			<h4 class="modal-title">Возврат денег</h4>
		</div>
		<div class="modal-body">
			<iframe src="<?=$APPLICATION->GetCurPageParam("cancel_blank=html", array())?>" style="border: none;"></iframe>
		</div>
		</div>
	</div>
</div>
<?endif?>
<?if (!empty($arResult["ERRORS"])):
	$arErrors = array();
	foreach($arResult["ERRORS"] as $arError) 
		$arErrors[] = is_array($arError) ? $arError["TEXT"] : $arError;?>
	<?ShowError(implode("<br />", $arErrors))?>
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
		case "H":
			for ($i = 0; $i<$inputNum; $i++)
			{
				if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
				{
					$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : (is_array($arResult["ELEMENT"][$propertyID]) ? $arResult["ELEMENT"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID]);
				}
				elseif ($i == 0)
				{
					$value = $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

				}
				else
				{
					$value = "";
				}
				if(!empty($value))
				{
			?>
				<input type="hidden" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][]" value="<?=$value?>" />								
			<?	}
			}
		break;
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
		case "N":
			for ($i = 0; $i<$inputNum; $i++)
			{
				if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
				{
					$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : (is_array($arResult["ELEMENT"][$propertyID]) ? $arResult["ELEMENT"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID]);
				}
				elseif ($i == 0)
				{
					$value = $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

				}
				else
				{
					$value = "";
				}
			?>
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime") CJSCore::Init(array('popup', 'date'));?>

			<input class="form-control <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CLASS"]?>"
				<?if($i==0):?> id="<?=$propertyID?>_INPUT"<?endif?> 
				<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?> onclick="BX.calendar({node:this, field:this, form: '', bTime: true, currentTime: '<?=(time()+date("Z")+CTimeZone::GetOffset())?>', bHideTime: false});"<?endif?>
				type="text" 
				name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][]"
				<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?> 
				<?if(!empty($arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_SET"])):?> data-info_set="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_SET"]?>"<?endif?> 
				<?if(!empty($arResult["PROPERTY_LIST_FULL"][$propertyID]["CALC_INPUT"])):?> data-calc_input="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CALC_INPUT"]?>"<?endif?> 
				size="25" 
				value="<?=$value?>" />								
			<?}?>
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] != "Y"):?>
			<input class="form-control inclone__block <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CLASS"]?>" type="text" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][]" size="25" value="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"]?>" />
			<br /><span class="btn btn-default inclone__btn">+</span>
			<?endif?>
		<?break;

		case "DOC_ELEMENT":?>
	<?if(is_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"])):
		//$col6size = array(4,1,1,2,2,1,1);
		//$col_size = floor(12/(count($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"]) + 1));?>
		<div class="mb30 inclone">
			<table class="table table-striped">
				<tr>
				<?foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"] as $colkey => $propID):?>
					<th><?if (intval($propID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST_FULL"][$propID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propID]) ? $arParams["CUSTOM_TITLE_".$propID] : GetMessage("IBLOCK_FIELD_".$propID)?><?if(in_array($propID, $arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?><?endif?></th>
				<?endforeach;?>
					<th></th>
				</tr>
			<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] != "Y")
			{
				$arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"][] = array(
					$propertyID."_ID" => $arResult["PROPERTY_LIST_FULL"][$propertyID]["arParams"]["ID"],
					"SAMPLE" => "Y"
				);
			}?>
			<?foreach($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $arDocElement):
				$arElemResult = $arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"];
				$arElemResult["ELEMENT"] = $arDocElement;
				$arElemParams = array("ID" => $arDocElement[$propertyID."_ID"]);
				?>
				<tr class="<?if($arDocElement["SAMPLE"] == "Y"):?>inclone__block <?else:?>deleted_block <?endif?>product_block">
				<?foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"] as $colkey => $propID):?>
					<td><?ShowInput($propID, $arElemResult, $arElemParams, "[".$propertyID."]");?></td>
				<?endforeach;?>
					<td>
						<?if($arDocElement["SAMPLE"] != "Y"):?><input class="product_basket_id" type="hidden" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>][ID][]" value="<?=$arElemParams["ID"]?>"><?endif?>
						<div class="clearfix">
						<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] != "Y"):?>
							<span class="btn pull-right btn-lg btn-danger <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ADDED_CLASS"]?> deleted_block_btn">X</span>
						<?endif?>
						</div>
					</td>
				</tr>
			<?endforeach?>
			</table>
		<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] != "Y"):?>
			<br />
			<span class="btn btn-primary inclone__btn scanner_detection_add">+</span>
		<?endif?>
		</div>
	<?endif?>
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
					foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
					{
						$checked = false;
						if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
						{
							if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID]))
							{
								foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum)
								{
									if ($arElEnum["VALUE"] == $key)
									{
										$checked = true;
										break;
									}
								}
							}
						}
						else
						{
							if ($arEnum["DEF"] == "Y") $checked = true;
						}

						?>
		<input type="<?=$type?>" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>"<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?> value="<?=$key?>" class="form-control" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> /><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label><br />
						<?
					}
				break;

				case "ajax":
				case "dropdown":
				case "multiselect":
				?>
		<select class="form-control<?if($type=="ajax"):?> ajax_select<?endif?> <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CLASS"]?>"<?if($type=="ajax"):?> data-url="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["URL"]?>"<?endif?><?if(strlen($arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_URL"])):?> data-info_url="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_URL"]?>"<?endif?> id="<?=$propertyID?>_INPUT" name="PROPERTY<?=$inputPrefix?>[<?=$propertyID?>]<?=$type=="multiselect" || $type=="ajax" ? "[]" : ""?>"<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] == "Y"):?> readonly<?endif?><?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y"):?> size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" multiple="multiple"<?endif?><?if(!empty($arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_SET"])):?> data-info_set="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["INFO_SET"]?>"<?endif?>>
			<option value=""><?echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA")?></option>
				<?
					if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
					else $sKey = "ELEMENT";

					foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
					{
						$checked = false;
						if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
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
						else
						{
							if ($arEnum["DEF"] == "Y") $checked = true;
						}
						?>
			<option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
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
<div class=" mb30 new-check">
	<h1><?if($arParams["ID"] <= 0):?>Новый чек<?else:?>Чек № <?=$arParams["ID"]?><?endif?></h1>
</div>
<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="CURRENT_SAVE" value="Y" />
	<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
	<?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
		<div class="form-group">
		<?if($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "DOC_ELEMENT"):?>
			<?ShowInput($propertyID, $arResult, $arParams);?>
		<?else:?>
			<div class="ml10 mb30" <?if($propertyID == 'PRICE_SUMM') echo ' style="display: none;"; '?>>
				<label class="control-label" for="<?=$propertyID?>_INPUT"><?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?></label>
				<?ShowInput($propertyID, $arResult, $arParams);?>
				<?/*<input type="text" placeholder="Нажми сюда" class="form-control input-lg width300" />*/?>
			</div>
		<?endif?>
		</div>
	<?endforeach;?>
<?endif?>
<?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"]))
	foreach ($arResult["PROPERTY_LIST"] as $propertyID)
	{
		if($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "DOC_ELEMENT")
		{
			if(is_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"]))
			{
					if($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["DISABLED"] != "Y")
						$arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"][] = array(
							$propertyID."_ID" => $arResult["PROPERTY_LIST_FULL"][$propertyID]["arParams"]["ID"],
							"SAMPLE" => "Y"
						);
					foreach($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $arDocElement)
					{
						$arElemResult = $arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"];
						$arElemResult["ELEMENT"] = $arDocElement;
						$arElemParams = array("ID" => $arDocElement[$propertyID."_ID"]);
						foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["arResult"]["PROPERTY_LIST"] as $colkey => $propID)
						{
							$arElemResult["PROPERTY_LIST_FULL"][$propID]["PROPERTY_TYPE"] = "H";
							ShowInput($propID, $arElemResult, $arElemParams, "_OLD[".$propertyID."]");
						}
					}
			}
		}
		else
		{
			$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "H";
			ShowInput($propertyID, $arResult, $arParams, "_OLD");
		}
	}
?>
	<?/*<div class="mb30">
		<h3>Выбранный клиент</h3>
		<table class="table table-striped">
			<tr>
				<th>ФИО</th>
				<th>Телефон</th>
				<th>Статус клиента</th>
				<th>Скидка</th>
			</tr>
			<tr>
				<td><input type="text" class="form-control" value="Кузьма Прутков"/></td>
				<td><input type="text" class="form-control" value="+7 903 227 88 74"/></td>
				<td><select id="select-client-status" data-placeholder="Выберите" class="width100p">
						<option value="">Категория</option>
						<option value="AK">Обычный посетитель</option>
						<option value="A5">Необычный посетитель</option>
					</select></td>
				<td><input type="text" value="25%" class="form-control" /></td>
			</tr>
		</table>
	</div>
	<div class="mb30">
		<h3>Поменять кол-во товара в текущей строке</h3>
		<div class="btn-list">
			<button class="btn btn-info btn-bordered btn btn-lg">1</button>
			<button class="btn btn-info btn-bordered btn-lg">2</button>
			<button class="btn btn-info btn-bordered btn-lg">3</button>
			<button class="btn btn-info btn-bordered btn-lg">4</button>
			<button class="btn btn-info btn-bordered btn-lg">5</button>
			<button class="btn btn-info btn-bordered btn-lg">6</button>
			<button class="btn btn-info btn-bordered btn-lg">7</button>
			<button class="btn btn-info btn-bordered btn-lg">8</button>
			<button class="btn btn-info btn-bordered btn-lg">9</button>
			<button class="btn btn-info btn-bordered btn-lg">10</button>
		</div>
	</div>*/?>
<?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
	<div class="form-group">
		<label for="<?=$propertyID?>_INPUT" class="col-sm-4">Текст с картинки<span class="starrequired">*</span></label>
		<div class="col-sm-8">
			<input type="text" name="captcha_word" maxlength="50" value="" class="form-control" id="<?=$propertyID?>_INPUT">
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
		</div>
	</div>
<?endif?>
<?if($arResult["ORDER"]["CANCELED"] != "Y"):?>
<div class="floated-buttons-wrapper">
	<div class="mb30 keyboard_block" style="display: none;">
		<h3>Поменять кол-во товара в текущей строке</h3>
		<div class="btn-list">
			<button class="btn btn-info btn-bordered btn-lg kb_p">1</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">2</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">3</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">4</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">5</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">6</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">7</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">8</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">9</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">0</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">00</button>
			<button class="btn btn-info btn-bordered btn-lg kb_p">.</button>
			<button class="btn btn-info btn-bordered btn-lg kb_bs">Bksp</button>
		</div>
	</div>
	<div class="row btn-list">
		<div class="col-lg-10">
		<?if($arResult["ORDER"]["PAYED"] != "Y"):?>
			<?foreach($arResult["PAY_SYSTEM"] as $arPaySystem):?>
			<button class="btn btn-primary btn-lg" type="submit" name="pay_system" value="<?=$arPaySystem["ID"]?>"><?=$arPaySystem["NAME"];?></button>
			<?endforeach?>
		<?else:?>
			<input class="btn btn-success btn-lg" type="submit" name="apply" value="Сохранить изменения" />
			<?if($arParams["ID"] > 0 && $arResult["ORDER"]["CANCELED"] != "Y"):?>
			<input class="btn btn-danger"
				type="submit"
				name="order_cancel"
				value="Вернуть все позиции"
			>
			<?endif?>
		<?endif?>
			<?/*<button class="btn btn-primary btn-lg"  data-toggle="modal" data-target=".bs-example-modal-sm2">Продать в долг</button>
			<button class="btn btn-default btn-lg">Выставить в счёт</button>
			<button class="btn btn-default btn-lg"   data-toggle="modal" data-target=".bs-example-modal-sm" >Наличные</button>
			<button class="btn btn-default btn-lg">Картой</button>*/?>
		</div>
		<div class="col-lg-2">
		<?if($arParams["ID"] <= 0):?>
			<input class="btn pull-right width100p btn-danger btn-lg"
				type="button"
				name="refresh"
				value="Отменить операцию"
				onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"]."?edit=Y")?>';"
			>
		<?endif?>
			<?/*<button class="btn pull-right width100p btn-danger btn-lg">Отменить операцию</button>*/?>
		</div>
	</div>
	<div id="priceMirrorBlock">
		<label id='priceMirrorLabel'>Сумма</label><br/>
		<label id="pricemirror"/>0</label>
	</div>
</div>
<?endif?>
</form>
<script type="text/javascript">
$(function(){

	setInterval(function(){
		if($('#PRICE_SUMM_INPUT').val() != '')
			$('#pricemirror').text($('#PRICE_SUMM_INPUT').val());
	}, 500);

});
</script>