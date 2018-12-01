<?
IncludeModuleLangFile(__FILE__);

function float($str, $set=FALSE)
{           
	if(preg_match("/([0-9\.,-]+)/", $str, $match))
	{
		// Found number in $str, so set $str that number
		$str = $match[0];
	   
		if(strstr($str, ','))
		{
			// A comma exists, that makes it easy, cos we assume it separates the decimal part.
			$str = str_replace('.', '', $str);    // Erase thousand seps
			$str = str_replace(',', '.', $str);    // Convert , to . for floatval command
		   
			return floatval($str);
		}
		else
		{
			// No comma exists, so we have to decide, how a single dot shall be treated
			if(preg_match("/^[0-9-]*[\.]{1}[0-9-]+$/", $str) == TRUE && $set['single_dot_as_decimal'] == TRUE)
			{
				// Treat single dot as decimal separator
				return floatval($str);
			   
			}
			else
			{
				// Else, treat all dots as thousand seps
				$str = str_replace('.', '', $str);    // Erase thousand seps
				return floatval($str);
			}               
		}
	}
   
	else
	{
		// No number found, return zero
		return 0;
	}

	// Examples
	/*echo float('foo 123,00 bar'); // returns 123.00
	echo float('foo 123.010 bar', array('single_dot_as_decimal'=> TRUE)); //returns 123.000
	echo float('foo 123.00 bar', array('single_dot_as_decimal'=> FALSE)); //returns 123000
	echo float('foo 222.123.00 bar', array('single_dot_as_decimal'=> TRUE)); //returns 222123000
	echo float('foo 222.123.00 bar', array('single_dot_as_decimal'=> FALSE)); //returns 222123000

	// The decimal part can also consist of '-'
	echo float('foo 123,-- bar'); // returns 123.00*/
}

function floatCase($input = array(), $replace = array())
{
	$input = (array) $input;
	$replace = (array) $replace;
	foreach ($replace as $kr => $rplc) 
	{
		if(is_array($rplc))
		{
			$input[$kr] = floatCase($input[$kr], $rplc);
		}
		else
		{
			if(is_array($input[$rplc]))
			{
				foreach($input[$rplc] as $ki => $itm)
					if(!empty($itm))
						$input[$rplc][$ki] = float($itm, array('single_dot_as_decimal'=> TRUE));
			}
			elseif(!empty($input[$rplc]))
				$input[$rplc] = float($input[$rplc], array('single_dot_as_decimal'=> TRUE));
		}
	}
	return $input;
}

function getByINN($inn = "")
{
	$arResult = array();
	if(!empty($inn) && class_exists("phpQuery"))
	{
		$key = $val = "";
		$tmpResult = array();
		$file = file_get_contents("http://online.igk-group.ru/ru/home?name=&ogrn=&inn=".$inn);
		$document = \phpQuery::newDocument($file);
		$result = $document->find("#home_bottom_results div[id^=tab]:visible table tr:eq(1) td table tr")->children();
		foreach ($result as $cell) 
		{
			if($cell->tagName == "th")
			{
				$key = $cell->textContent;
			}
			elseif($cell->tagName == "td" && !empty($key))
			{
				$val = $cell->textContent;
				$tmpResult[trim($key)] = trim($val);
				$key = $val = "";
			}
			else
			{
				$key = $val = "";
			}
		}

		$arResult = array(
			"NAME" => $tmpResult["Руководство"],
			"COMPANY" => $tmpResult["Краткое название"],
			"COMPANY_FULL" => $tmpResult["Полное название"],
			//"INN" => $tmpResult["ИНН"],
			"ADDRESS" => $tmpResult["Адрес"],
			"OGRN" => $tmpResult["ОГРН"],
			"OKPO" => $tmpResult["ОКПО"],
			"DATE" => $tmpResult["Период регистрации"],
		);
	}
	
	return $arResult;
}

function sendWithTime(array $data)
{
	if (!empty($data["DATE_SEND"]))
	{

		$data["DATE_INSERT"] = is_object($data["DATE_SEND"]) ? $data["DATE_SEND"] : new \Bitrix\Main\Type\DateTime($data["DATE_SEND"]);
		$data["SUCCESS_EXEC"] = "Y";
		unset($data["DATE_SEND"]);
	}
	return \Bitrix\Main\Mail\Event::send($data);
}

function cronSendWithTime()
{
	$res = \Bitrix\Main\Mail\Internal\EventTable::getList(
		array(
			"select" => array("ID"),
			"filter" => array(
				"DATE_EXEC" => false,
				"SUCCESS_EXEC" => "Y",
				"<=DATE_INSERT" => new \Bitrix\Main\Type\DateTime()
			)
		)
	);
	while ($row = $res->fetch())
		\Bitrix\Main\Mail\Internal\EventTable::update($row["ID"], array("SUCCESS_EXEC" => "N"));
	return "cronSendWithTime();";
}

function parentTree($list, $parent_id = "", $parent_field = "PARENT", $level = 1)
{
	$tree = array();
	foreach($list as $item)
		if($item[$parent_field] == $parent_id)
		{
			$item["DEPTH_LEVEL"] = $level;
			$item["ITEMS"] = parentTree($list, $item["ID"], $parent_field, $level+1);
			if(!count($item["ITEMS"])) unset($item["ITEMS"]);
			$tree[] = $item;
		}
	return $tree;
}

function _treeSort($tree, $items_field = "ITEMS")
{
	$sort_list = array();
	foreach ($tree as $item) 
	{
		$items = $item[$items_field];

		if (isset($item[$items_field]))
			unset($item[$items_field]);

		$sort_list[] = $item;

		if (is_array($items))
			$sort_list = array_merge($sort_list, _treeSort($items, $items_field));
	}
	return $sort_list;
}

function parentSort($list, $parent_id = "", $parent_field = "PARENT")
{
	$tree = parentTree($list, $parent_id, $parent_field);

	$sort_list = _treeSort($tree, "ITEMS");
	
	return $sort_list;
}

function getTableAlias($obj, $join_table = "")
{
	return strtolower($obj->getEntity()->getCode() . (empty($join_table) ? "" : "_".$join_table));
	;
}

function joinSqlExpression($obj, $string = "")
{
	$path = explode(".", $string);
	return new \Bitrix\Main\DB\SqlExpression('?#', getTableAlias($obj, $path[0]) . (empty($path[1]) ? "" : ".".$path[1]));
	;
}
