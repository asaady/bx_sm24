<?
namespace Yadadya\Shopmate\Components;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Template
{
	public static function setAttr($attr = array())
	{
		$attr = (array) $attr;
		$res = array();

		$attr["name"] = (!empty($attr["prefix"]) ? $attr["prefix"]."[".$attr["name"]."]" : $attr["name"]).($attr["multiple"] ? "[]" : "");
		unset($attr["prefix"], $attr["multiple"]);

		$attr["type"] = !empty($attr["type"]) ? $attr["type"] : "text";

		$attr["class"] = implode(" ", (array) $attr["class"]);

		$attr["placeholder"] = $attr["disabled"] && !$attr["readonly"] ? "-" : ($attr["placeholder"] ? $attr["placeholder"] : "");

		$attr["required"] = !$attr["disabled"] && $attr["required"] ? "required" : "";

		$attr["readonly"] = $attr["disabled"] || $attr["readonly"] ? "readonly" : "";

		if($attr["disabled"] && $attr["readonly"])
			$attr["disabled"] = "disabled";
		else
			unset($attr["disabled"]);

		$attr["hidden"] = $attr["hidden"] ? "hidden" : "";

		if(is_array($attr["data"]))
			foreach($attr["data"] as $name => $val)
				$attr["data-".$name] = $val;
		unset($attr["data"]);

		if(is_array($attr["style"]))
		{
			$style = "";
			foreach($attr["style"] as $name => $val)
				$style .= $name.":".$val.";";
			$attr["style"] = $style;
		}

		$attrs = array();
		if(is_array($attr))
			foreach($attr as $name => $val)
				if(!empty($val))
					$attrs[] = $name."=\"".$val."\"";
			
		return implode(" ", $attrs);
	}

	public static function printField(array $parameters = array(), $show = true)
	{
		$field = "";
		switch ($parameters["field"]) 
		{
			case "select":
			case "checkbox":
			case "radio":
				$parameters["value"] = (array) $parameters["value"];
				if(is_array($parameters["enum"]))
					foreach($parameters["enum"] as $value => $description)
						if(in_array($value, $parameters["value"]))
							$field .= (empty($field) ? "" : ", ") . $description;

				break;
			
			case "textarea":

				$field = $parameters["value"];

				break;
			
			default:

				$field = $parameters["value"];

				break;
		}
		if($show) echo $field;
		return $field;
	}

	public static function showField(array $parameters = array(), $show = true)
	{
		$field = "";
		if(is_array($parameters["enum"]))
		{
			$parameters["enum_data"] = array();
			foreach($parameters["enum"] as $value => $description)
				if(is_array($description))
				{
					$parameters["enum_data"][$value] = "";
					if(is_array($description["DATA"]))
						foreach($description["DATA"] as $name => $val)
							$parameters["enum_data"][$value] .= " data-".$name."=\"".$val."\"";
					$parameters["enum"][$value] = $description["VALUE"];
				}
		}

		if (is_array($parameters["value"]))
			foreach ($parameters["value"] as $key => $value) 
				$parameters["value"][$key] = htmlspecialchars($value);
		else $parameters["value"] = htmlspecialchars($parameters["value"]);

		if ($parameters["print"])
			return self::printField($parameters, $show);

		switch ($parameters["field"]) 
		{
			case "select":

				$parameters["value"] = (array) $parameters["value"];
				$field = "<select ".self::setAttr($parameters["attr"])." >";
				$field .= "	<option value=\"\">".($parameters["attr"]["placeholder"] ? " - ".$parameters["attr"]["placeholder"]." - " : "")."</option>";
				if(is_array($parameters["enum"]))
					foreach($parameters["enum"] as $value => $description)
						$field .= "	<option value=\"".$value."\"".(in_array($value, $parameters["value"]) ? " selected" : "").$parameters["enum_data"][$value].">".$description."</option>";
				$field .= "</select>";

				break;
			
			case "checkbox":
			case "radio":
			
				$parameters["value"] = (array) $parameters["value"];
				$parameters["attr"]["type"] = $parameters["field"];
				if(is_array($parameters["enum"]))
					foreach($parameters["enum"] as $value => $description)
						if (!empty($parameters["template"]))
						{
							$field .= str_replace("#INPUT#", "<label for=\"".$parameters["attr"]["name"]."_".$value."\" class=\"".$parameters["field"]." ".$parameters["class_label"].(in_array($value, $parameters["value"]) ? " active" : "")."\"><input id=\"".$parameters["attr"]["name"]."_".$value."\" value=\"".$value."\" ".self::setAttr($parameters["attr"]).(in_array($value, $parameters["value"]) ? " checked" : "").$parameters["enum_data"][$value]." />".(empty($description) ? $parameters["attr"]["placeholder"] : $description)."</label>", $parameters["template"]);;
							
						}
						else
							$field .= "<div class=\"".$parameters["field"]."\"><label for=\"".$parameters["attr"]["name"]."_".$value."\" class=\"".$parameters["class_label"].(in_array($value, $parameters["value"]) ? " active" : "")."\"><input id=\"".$parameters["attr"]["name"]."_".$value."\" value=\"".$value."\" ".self::setAttr($parameters["attr"]).(in_array($value, $parameters["value"]) ? " checked" : "").$parameters["enum_data"][$value]." />".(empty($description) ? $parameters["attr"]["placeholder"] : $description)."</label></div>";

				break;
			
			case "textarea":

				$field = "<textarea ".self::setAttr($parameters["attr"])." >".$parameters["value"]."</textarea>";

				break;
			
			default:

				$field = "<input value=\"".$parameters["value"]."\" ".self::setAttr($parameters["attr"])." />";

				if($parameters["attr"]["type"] == "file")
					$field .= "<br>".\CFile::ShowImage($parameters["value"], 200, 200, "border=0", "", true)." ".print_url(\CFile::GetPath($parameters["value"]), "download", "target=\"_blank\"")."<input value=\"".$parameters["value"]."\" type=\"hidden\" name=\"".$parameters["attr"]["name"]."\" />";

				if($parameters["mode"] == "DateTime" || $parameters["mode"] == "Date") 
				{

					ob_start();
					\CJSCore::Init(array('popup', 'date'));
					if(!$parameters["hide_icon"]) $field = "<div class=\"input-group\">"
						.ob_get_contents()
						.$field
						."<span class=\"input-group-addon\" onclick=\"BX.calendar({node:this, field:previousElementSibling, form: '', bTime: ".($parameters["mode"] == "DateTime" ? "true" : "false").", currentTime: '".(time()+date("Z")+\CTimeZone::GetOffset())."', bHideTime: ".($parameters["mode"] == "DateTime" ? "false" : "true")."});\"><i class=\"glyphicon glyphicon-calendar\" ></i></span>"
						."</div>";
					ob_end_clean();
				}


				/*if($parameters["attr"]["multiple"]) 
				{
					$add_param = $parameters["attr"];
					$add_param["class"][] = "inclone__block";
					$field .= "<input value=\"".$parameters["value"]."\" ".self::setAttr($add_param)." /><br /><span class=\"btn btn-default inclone__btn\">+</span>";
				}*/

				break;
		}
		if($parameters["error"])
			$field = "<div class=\"has-error\">".$field."</div>";
		if($show) echo $field;
		return $field;
	}

	public static function ShowMultipleInput($propertyID, $arProperty, $value, $prefix = "")
	{
		$value = (array) $value;
		$propertyID = $propertyID."[]";
		$arProperty["MULTIPLE"] = "N";

		echo "<div class=\"inclone\">";

		foreach ($value as $val) 
		{
			if($arProperty["DISABLED"] != "Y" && $arProperty["READONLY"] != "Y")
			{
				echo "<div class=\"input-group deleted_block\">";
				self::ShowInput($propertyID, $arProperty, $val, $prefix);
				echo "<span class=\"input-group-addon btn btn-danger glyphicon glyphicon-remove deleted_block_btn\"></span>";
				echo "</div>";
			}
			else
			{
				self::ShowInput($propertyID, $arProperty, $val, $prefix);
			}
		}

		if($arProperty["DISABLED"] != "Y" && $arProperty["READONLY"] != "Y")
		{
			echo "<div class=\"input-group inclone__block\">";
			self::ShowInput($propertyID, $arProperty, "", $prefix);
			echo "<span class=\"input-group-addon btn btn-danger glyphicon glyphicon-remove deleted_block_btn\"></span>";
			echo "</div>";

			echo "<span class=\"btn btn-primary glyphicon glyphicon-plus inclone__btn\"></span>";
		}

		echo "</div>";
	}

	public static function PrintInput($propertyID, $arProperty, $value = null, $prefix = "")
	{
		$arProperty["PRINT"] = "Y";
		self::ShowInput($propertyID, $arProperty, $value, $prefix);
	}

	public static function ShowInput($propertyID, $arProperty, $value = null, $prefix = "")
	{
		if (!empty($arProperty["VERIFICATION"]))
		{
			$arProperty["DATA"] = (array) $arProperty["DATA"];
			$arProperty["DATA"]["verification"] = $arProperty["VERIFICATION"];
		}
		$field = array(
			"attr" => array(
				"name" => $propertyID,
				"prefix" => $prefix,
				"disabled" => $arProperty["DISABLED"] == "Y" ? true : false,
				"readonly" => $arProperty["READONLY"] == "Y" ? true : false,
				"required" => $arProperty["REQUIRED"] == "Y" ? true : false,
				"multiple" => $arProperty["MULTIPLE"] == "Y" ? true : false,
				"placeholder" => isset($arProperty["PLACEHOLDER"]) ? $arProperty["PLACEHOLDER"] : $arProperty["TITLE"],
				"class" => array($arProperty["LIST_TYPE"] != "C" && $arProperty["PROPERTY_TYPE"] != "CUSTOM" ? "form-control" : "", $arProperty["CLASS"]),
				"data" => (array) $arProperty["DATA"],
				"style" => (array) $arProperty["STYLE"],
			),
			"value" => $value === null ? $arProperty["DEFAULT_VALUE"] : $value,
			"error" => $arProperty["ERROR"] == "Y" ? true : false,
			"print" => $arProperty["PRINT"] == "Y" ? true : false,
			"template" => $arProperty["TEMPLATE"]
		);
		if (!empty($arProperty["ATTR"]))
		{
			$arProperty["ATTR"] = (array) $arProperty["ATTR"];
			foreach ($arProperty["ATTR"] as $key => $val) 
				$field["attr"][$key] = $val;
		}

		if($arProperty["MULTIPLE"] == "Y" && !in_array($arProperty["PROPERTY_TYPE"], array("SUBLIST"/*, "L"*/)))
		{
			self::ShowMultipleInput($propertyID, $arProperty, $value, $prefix = "");
			return;
		}

		switch($arProperty["PROPERTY_TYPE"]) 
		{
			case "SUBLIST":
				$dataAttr = "";
				if(is_array($arProperty["DATA"]))
					foreach($arProperty["DATA"] as $name => $val)
						$dataAttr .= " data-".$name."=\"".$val."\"";
				?>
				<div class="mb30 inclone <?=$arProperty["CLASS"]?>"<?=$dataAttr?>>
					<table class="table table-striped<?if($arProperty["NUM_LIST"] == "Y"):?> nmrc<?endif?>"<?if ($arProperty["PRINT"] == "Y"):?> border="1" rules="all"<?endif?>>
						<tr>
							<?if($arProperty["NUM_LIST"] == "Y"):?><th>№</th><?endif?>
					<?foreach($arProperty["PROPERTY_LIST"] as $propID => $arProp)
						if($arProp["PROPERTY_TYPE"] != "H"):?>
							<th><?=$arProp["TITLE"]?><?if($arProp["REQUIRED"] == "Y"):?> <span class="starrequired">*</span><?endif?></th>
						<?endif?>
						<?if ($arProperty["PRINT"] != "Y"):?>
							<th></th>
						<?endif?>
						</tr>
					<?foreach($value as $keyValue => $arValue):
						$errorRow = intval($keyValue) <= 0 && $keyValue !== 0 ? true : false;?>
						<tr class="deleted_block <?=$arProperty["CLASS_ROW"]?>"<?/*if($errorRow):?> style="border: 2px solid lightcoral;"<?endif*/?> data-item="<?=$keyValue?>">
							<?if($arProperty["NUM_LIST"] == "Y"):?><td class="nmrc_item"><?=($keyValue + 1)?></td><?endif?>
					<?foreach($arProperty["PROPERTY_LIST"] as $propID => $arProp)
						if($arProp["PROPERTY_TYPE"] != "H"):
							if ($arProperty["PRINT"] == "Y") $arProp["PRINT"] = "Y";
							if($arProperty["DISABLED"] == "Y" || ($arProperty["DISABLED_SAVED"] == "Y" && !$errorRow)) $arProp["DISABLED"] = "Y";?>
							<td><?Template::ShowInput($propID, $arProp, $arValue[$propID], $propertyID."[".$keyValue."]");?></td>
						<?endif?>
						<?if ($arProperty["PRINT"] != "Y"):?>
							<td>
						<?foreach($arProperty["PROPERTY_LIST"] as $propID => $arProp)
							if($arProp["PROPERTY_TYPE"] == "H")
							{
								if($arProperty["DISABLED"] == "Y" || ($arProperty["DISABLED_SAVED"] == "Y" && !$errorRow)) $arProp["DISABLED"] = "Y";
								Template::ShowInput($propID, $arProp, $arValue[$propID], $propertyID."[".$keyValue."]");
							}?>
								<div class="clearfix">
								<?if($arProp["DISABLED"] != "Y" && $arProperty["READONLY"] != "Y"):?>
									<span class="btn pull-right btn-lg btn-danger deleted_block_btn">X</span>
								<?endif?>
								</div>
							</td>
						<?endif?>
						</tr>
					<?endforeach?>
				<?if($arProperty["DISABLED"] != "Y" && $arProperty["READONLY"] != "Y" && $arProperty["PRINT"] != "Y"):?>
						<tr class="inclone__block <?=$arProperty["CLASS_ROW"]?>" data-item="new_id">
							<?if($arProperty["NUM_LIST"] == "Y"):?><td class="nmrc_item"></td><?endif?>
					<?foreach($arProperty["PROPERTY_LIST"] as $propID => $arProp)
						if($arProp["PROPERTY_TYPE"] != "H"):?>
							<td><?Template::ShowInput($propID, $arProp, "", $propertyID."[new_id]");?></td>
						<?endif;?>
							<td>
						<?foreach($arProperty["PROPERTY_LIST"] as $propID => $arProp)
							if($arProp["PROPERTY_TYPE"] == "H")
								Template::ShowInput($propID, $arProp, "", $propertyID."[new_id]");?>
								<div class="clearfix">
								<?if($arProp["DISABLED"] != "Y" && $arProperty["READONLY"] != "Y"):?>
									<span class="btn pull-right btn-lg btn-danger deleted_block_btn">X</span>
								<?endif?>
								</div>
							</td>
						</tr>
				<?endif?>
					</table>
				<?if($arProperty["DISABLED"] != "Y" && $arProperty["READONLY"] != "Y"):?>
					<br />
					<span class="btn btn-primary inclone__btn scanner_detection_add">+</span>
				<?endif?>
				</div>
				<?

				break;

			case "L":

				$field["enum"] = (array) $arProperty["ENUM"];

				if($arProperty["LIST_TYPE"] == "C")
				{
					$field["field"] = $arProperty["MULTIPLE"] == "Y" || $arProperty["USER_TYPE"] == "checkbox" ? "checkbox" : "radio";
					$field["class_label"] = $arProperty["CLASS_LABEL"];
					/*echo "<div class=\"ckbox ckbox-primary inline-block\">";
					self::showField($field);
					echo "</div>";*/

				}
				else
				{
					$field["field"] = "select";
					if($arProperty["MULTIPLE"] == "Y"/* || $arProperty["LIST_TYPE"] == "AJAX"*/) $field["attr"]["multiple"] = "multiple";
					if($arProperty["LIST_TYPE"] == "AJAX") $field["attr"]["class"][] = "ajax_select";
				}

				if ($arProperty["LIST_TYPE"] == "AJAX")
				{
					?>
						<?self::showField($field);?>
					<?
				}
				else
					self::showField($field);

				break;
			

			case "T":

				$field["field"] = "textarea";
				self::showField($field);

				break;


			case "H":

				$field["attr"]["type"] = "hidden";
				self::showField($field);

				break;

			case "F":

				$field["attr"]["type"] = "file";
				self::showField($field);

				break;

			case "CUSTOM":

				$field["attr"]["type"] = $arProperty["USER_TYPE"];
				self::showField($field);

				break;
			

			case "S":

			case "N":

			default:

				if($arProperty["USER_TYPE"] == "DateTime" || $arProperty["USER_TYPE"] == "Date") 
				{
					$field["mode"] = $arProperty["USER_TYPE"];
					$field["attr"]["data"]["verification"] = strtolower($arProperty["USER_TYPE"]);
					if($arProperty["HIDE_ICON"] == "Y") $field["hide_icon"] = true;
				}

				self::showField($field);

				break;
		}
	}

	public static function ShowButton($arButton)
	{
		if (empty($arButton["PROPERTY_TYPE"])) $arButton["PROPERTY_TYPE"] = "CUSTOM";
		if (empty($arButton["USER_TYPE"])) $arButton["USER_TYPE"] = "submit";
		$arButton["CLASS"] = "btn btn-lg ".$arButton["CLASS"];

		self::ShowInput($arButton["NAME"], $arButton, $arButton["VALUE"], $arButton["PREFIX"]);
	}

	public static function propListMerge(array $propList = array(), array $propListTemplate = array())
	{
		foreach($propListTemplate as $key => $val)
		{
			if(is_array($val))
				$propList[$key] = self::propListMerge((array) $propList[$key], $val);
			else
				$propList[$key] = $val;
		}

		return $propList;
	}

	public static function propListDisable(&$propList)
	{
		$propList = (array) $propList;
		foreach($propList as $key => $val)
		{
			$propList[$key]["DISABLED"] = "Y";
			if(is_array($propList[$key]["PROPERTY_LIST"]))
				self::propListDisable($propList[$key]["PROPERTY_LIST"]);
		}
	}

	function QuantityFormat($val, $count = 3, $measure = "")
	{
		return number_format($val, $count, ".", " ")." ".$measure;
	}

	function PriceFormat($price, $count = 0, $currency = "RUB")
	{
		if (empty($currency)) $currency = "RUB";
		if (!$count) return CurrencyFormat($price, $currency);
		else return number_format($price, $count, ".", " ");
	}
}