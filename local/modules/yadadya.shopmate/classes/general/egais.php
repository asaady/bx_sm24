<?php

IncludeModuleLangFile(__FILE__);

use Yadadya\Shopmate\Egais\EgaisCustom;

class SMEGAIS
{
	private $device = array (
		"host" => "localhost",
		"port" => "8080",
		"ajax" => "",
		"info_selector" => "#egais",
		"log_file" => "/egais.txt",
		"FSRAR_ID" => "030000196489"
	);

	function __construct($device) 
	{
		if(is_array($device))
		{
			foreach(array_keys($device) as $key) 
				$this->device[$key] = $device[$key];
		}
		elseif(is_string($device))
		{
			$this->device["info_selector"] = $device;	
		}
	}

	function getParams() 
	{
		$device = array();
		if(is_object($this) && (get_class($this) == get_class()))
			$device = $this->device;
		else
		{
			$class = get_class();
			$_this = new $class;
			$device = $_this->device;
		}
		$device["log_file"] = $_SERVER["DOCUMENT_ROOT"].$device["log_file"];
		return $device;
	}

	function sendWayBillAct($id, $Document)
	{
		if(empty($Document["ACTNUMBER"])) $Document["ACTNUMBER"] = "1";
		if(empty($Document["IsAccept"])) $Document["IsAccept"] = "Accepted";
		if(empty($Document["Note"])) $Document["Note"] = "Принимаем продукцию";
		if(empty($Document["ActDate"])) $Document["ActDate"] = date("Y-m-d");
		else date("Y-m-d", strtotime($Document["ActDate"]));
		$Document["Content"] = "";
		if(is_array($Document["Positions"]))
			$Positions = json_encode($Document["Positions"]);

		$device = self::getParams();
		$onSuccess = "function(sdata) {
			var _data = $.parseXML(sdata),
				\$data = \$(_data),
				BRegId = \$data.find('WBRegId').text(),
				content = '';
			".(!empty($Positions) ? "var positions = jQuery.parseJSON('".$Positions."'),
				\$positions = \$data.find('Position');
			\$positions.each(function() {
				var \$position = $(this),
					Identity = \$position.find('Identity').text(),
					InformBRegId = \$position.find('InformBRegId').text();
				if(positions[Identity] != undefined)
					content += '<wa:Position>'+
							'<wa:Identity>'+Identity+'</wa:Identity>'+
							'<wa:RealQuantity>'+positions[Identity]+'</wa:RealQuantity>'+
							'<wa:InformBRegId>'+InformBRegId+'</wa:InformBRegId>'+
						'</wa:Position>';
			});" : "")."
			var xml = '<ns:Documents Version=\"1.0\" '+
'xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" '+
'xmlns:ns=\"http://fsrar.ru/WEGAIS/WB_DOC_SINGLE_01\" '+
'xmlns:oref=\"http://fsrar.ru/WEGAIS/ClientRef\" '+
'xmlns:pref=\"http://fsrar.ru/WEGAIS/ProductRef\" '+
'xmlns:wa=\"http://fsrar.ru/WEGAIS/ActTTNSingle\" '+
'> '+
'	<ns:Owner> '+
'		<ns:FSRAR_ID>".$device["FSRAR_ID"]."</ns:FSRAR_ID> '+
'	</ns:Owner> '+
'	<ns:Document> '+
'		<ns:WayBillAct> '+
'			<wa:Header> '+
'				<wa:IsAccept>".$Document["IsAccept"]."</wa:IsAccept> '+
'				<wa:ACTNUMBER>".$Document["ACTNUMBER"]."</wa:ACTNUMBER> '+
'				<wa:ActDate>".$Document["ActDate"]."</wa:ActDate> '+
'				<wa:WBRegId>'+BRegId+'</wa:WBRegId> '+
'				<wa:Note>".$Document["Note"]."</wa:Note> '+
'			</wa:Header> '+
'			<wa:Content>'+content+'</wa:Content> '+
'		</ns:WayBillAct> '+
'	</ns:Document> '+
'</ns:Documents>';

			var data = new FormData();
			data.append('xml_file', xml);

			$.ajax({
				url: 'http://localhost:8080/opt/in/WayBillAct',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				type: 'POST',
				success: function(data) {
					$('".$device["info_selector"]."').prepend('<div class=\"".$Document["SuccessClass"]."\">ОК</div>').run();
				}
			});
		}";
		return self::getFormBRegInfo($id, $onSuccess);	
	}

	function showWayBills()
	{
		//old example
		/*if (CModule::IncludeModule("yadadya.shopmate"))
		{
			if($_REQUEST["egais_data"])
			{
				$APPLICATION->RestartBuffer();
				$_REQUEST["egais_data"] = preg_replace("/(<[\/]{0,1})([a-z]+:)/", "$1", $_REQUEST["egais_data"]);
				$answer_xml = simplexml_load_string($_REQUEST["egais_data"]);
				switch ($_REQUEST["egais_func"]) 
				{
					case "getOptOut":
						if(!empty($answer_xml->url))
							foreach ($answer_xml->url as $url) 
							{
								$pi = pathinfo($url);
								if(stripos($pi["dirname"], "/opt/out/WAYBILL") !== false):
									$waybill_id = $pi["basename"];
									$waybill = new SMEGAIS("#egais_waybill_".$waybill_id);?>
		<div id="egais_waybill_<?=$waybill_id?>">
			<?$waybill->getWayBill($waybill_id);?>
		</div>
								<?endif;
							}
						break;
					case "getWayBill":
						print_p($answer_xml);
						break;
				}
				die();
			}
		}
		?><div id="egais">
			<?SMEGAIS::getOptOut();?>
		</div><?*/

		$device = self::getParams();
		$onSuccess = "function(sdata) {
			var _data = $.parseXML(sdata),
				\$waybill = \$(_data).find('url:contains(\"/opt/out/WAYBILL\")');
			\$waybill.each(function() {
				var wb_id = $(this).text().split('/').pop();
				$.ajax({
					url: $(this).text(),
					crossDomain : true,
					dataType: 'text',
					success: function(sdata) {
						$.ajax({
							url: '".$device["ajax"]."',
							type: 'POST',
							data: {
								egais_func: '".__FUNCTION__."',
								egais_wbid: wb_id,
								egais_data: sdata
							},
							success: function(data){
								$('".$device["info_selector"]."').append(data).run();
							}
						});
					}
				});
			});
		}";
		return self::getOptOut($onSuccess);	
	}

	function getWayBill($id, $onSuccess = "")
	{
		return self::get("/opt/out/WAYBILL/".$id, __FUNCTION__, $onSuccess);
	}

	function getFormBRegInfo($id, $onSuccess = "")
	{
		return self::get("/opt/out/FORMBREGINFO/".$id, __FUNCTION__, $onSuccess);
	}

	function getOptOut($onSuccess = "")
	{
		return self::get("/opt/out", __FUNCTION__, $onSuccess);
	}

	function get($url, $func, $onSuccess = "")
	{
		$device = self::getParams();
		echo "<script>
if (window.jQuery) {
	$.ajax({
		url: 'http://".$device["host"].":".$device["port"].$url."',
		crossDomain : true,
		dataType: 'text',
		success: ".(!empty($onSuccess) ? $onSuccess : "function(sdata) {
			$.ajax({
				url: '".$device["ajax"]."',
				type: 'POST',
				data: {
					egais_func: '".$func."',
					egais_data: sdata
				},
				success: function(data){
					//data = '".GetMessage("SM_DEVICE_".$func)."...<br>' + data;
					$('".$device["info_selector"]."').append(data).run();
				}
			});
		}").",
		error: function() {
			alert('Невозможно подключиться к УТМ, возможно он выключен.');
		}
	});
}
</script>";
	}

	function set($url, $func, $onSuccess = "", $data = "")
	{
		$device = self::getParams();
		echo "<script>
if (window.jQuery) {
	var xml = '".$data."';
	data = new FormData();
	data.append('xml_file', xml);

	$.ajax({
		url: 'http://".$device["host"].":".$device["port"].$url."',
		crossDomain : true,
		dataType: 'text',
		data: data,
		cache: false,
		contentType: false,
		processData: false,
		type: 'POST',
		success: ".(!empty($onSuccess) ? $onSuccess : "function(sdata) {
			$.ajax({
				url: '".$device["ajax"]."',
				type: 'POST',
				data: {
					egais_func: '".$func."',
					egais_data: sdata
				},
				success: function(data){
					//data = '".GetMessage("SM_DEVICE_".$func)."...<br>' + data;
					$('".$device["info_selector"]."').append(data).run();
				}
			});
		}").",
		error: function() {
			alert('Ошибка! Ваш запрос не был отправлен.');
		}
	});
}
</script>";
	}

	function del($url, $onSuccess = "")
	{
		$device = self::getParams();
		echo "<script>
if (window.jQuery) {
	".self::del_func("'http://".$device["host"].":".$device["port"]."'+'".$url."'", $onSuccess)."
}
</script>";
	}

	function del_func($url, $onSuccess = "")
	{
		$device = self::getParams();
		$url = "'http://".$device["host"].":".$device["port"]."'+".str_replace("http://".$device["host"].":".$device["port"], "", $url);
		return "	$.ajax({
		url: ".$url.",
		crossDomain : true,
		type: 'DELETE',
		processData: false,
		success: ".(!empty($onSuccess) ? $onSuccess : "function(sdata) {
			$(document).trigger('egais-delete', []);
		}")."
	});";
	}

	function updateOptOut()
	{
		$device = self::getParams();
		$onSuccess = "function(sdata) {
			var _data = $.parseXML(sdata),
				\$documents = \$(_data).find('url'),
				doc_success = 0,
				reboot = 0;
			if(\$documents.length > 0)
				$(document).trigger('egais-updateOptOutStart', [true]);
			\$documents.each(function(i) {
				var \$document = $(this),
					url = \$document.text(),
					url_split = url.split('/'),
					docId = url_split.pop(),
					doc = url_split.pop(),
					replyId = \$document.attr('replyId');
				$.ajax({
					url: url,
					crossDomain : true,
					dataType: 'text',
					success: function(sdata) {
						window.setTimeout(
							function() {
								//alert(i);
								if (replyId == undefined || replyId == '') {
									if(doc.toLowerCase().indexOf('waybill') == 0 || doc.toLowerCase().indexOf('formbreginfo') == 0)
										replyId = $(sdata).find('Identity').text();
								}
								$.ajax({
									url: '".$device["ajax"]."',
									type: 'POST',
									data: {
										egais_func: '".__FUNCTION__."',
										egais_replyId: replyId,
										egais_url: url,
										egais_doc: doc,
										egais_docId: docId,
										egais_data: sdata
									},
									success: function(data){
										doc_success++;
										if(data.indexOf('#reboot#') >= 0) {
											reboot++;
											".self::del_func("url")."
										}
										if((doc_success >= \$documents.length)/* && (reboot > 0)*/)
											$(document).trigger('egais-updateOptOut', [reboot]);
									}
								});
							}
						, i*2000);
					}
				});
			});
			
		}";
		return self::get("/opt/out", __FUNCTION__, $onSuccess);
	}

	function onPrologOptOut()
	{
		if($_REQUEST["egais_func"] == "updateOptOut")
		{
			global $APPLICATION;
			$APPLICATION->RestartBuffer();

			$arFields = array(
				"STORE_ID" => SMShops::getUserShop(),
				"OPT" => "OUT",
				"URL" => $_REQUEST["egais_url"],
			);
			$opt = new SMEGAIS();
			/*$update = array();
			$opt = new SMEGAIS();
			$rsOpt = $opt->getList(array(), $arFields, false, false, array("ID", "XML"));
			while($arOpt = $rsOpt->Fetch())
				$update[] = $arOpt;*/

			$url_split = split("/",$arFields["URL"]);
			$docId = array_pop($url_split);
			$doc = array_pop($url_split);
			$arFields["DOCUMENT"] = (!empty($_REQUEST["egais_doc"]) ? $_REQUEST["egais_doc"] : $doc);
			$arFields["DOCUMENT_NUMBER"] = (!empty($_REQUEST["egais_docId"]) ? $_REQUEST["egais_docId"] : $docId);
			$arFields["REPLY_ID"] = (!empty($_REQUEST["egais_replyId"]) ? $_REQUEST["egais_replyId"] : "");
			$arFields["XML"] = $_REQUEST["egais_data"];

			$urls = EgaisCustom::getDelList(SMShops::getUserShop());

			if(!in_array($arFields["URL"], $urls))
			{
				$opt->add($arFields);
				echo "#reboot#";
			}
			/*if(!empty($update))
			{
				foreach ($update as $arOpt) 
					if(empty($arOpt["XML"]))
					{
						$opt->update($arOpt["ID"], $arFields);
						echo "#reboot#";
					}
			}
			elseif(!empty($arFields["REPLY_ID"]))
			{
				$opt->add($arFields);
				echo "#reboot#";
			}*/
			die();
		}
	}

	function updateOptIn($url, $data)
	{
		$device = self::getParams();
		if(empty($url))
		{
			$onSuccess = "function(sdata) {
				var _data = $.parseXML(sdata),
					\$documents = \$(_data).find('url'),
					doc_success = 0,
					reboot = 0;
				\$documents.each(function(i) {
					var \$document = $(this),
						url = \$document.text(),
						url_split = url.split('/'),
						docId = url_split.pop(),
						doc = url_split.pop(),
						replyId = \$document.attr('replyId');
					$.ajax({
						url: url,
						crossDomain : true,
						dataType: 'text',
						success: function(sdata) {
							window.setTimeout(
								function() {
									//alert(i);
									$.ajax({
										url: '".$device["ajax"]."',
										type: 'POST',
										data: {
											egais_func: '".__FUNCTION__."',
											egais_replyId: replyId,
											egais_url: url,
											egais_doc: doc,
											egais_docId: docId/*,
											egais_data: sdata*/
										},
										success: function(data){
											doc_success++;
											if(data.indexOf('#reboot#') >= 0) {
												reboot++;
												".self::del_func("url")."
											}
											if((doc_success >= \$documents.length) && (reboot > 0))
												$(document).trigger('egais-updateOptIn', [reboot]);
										}
									});
								}
							, i*2000);
						}
					});
				});
				
			}";
			return self::get("/opt/in", __FUNCTION__, $onSuccess, $data);
		}
		else
		{
			$onSuccess = "function(sdata) {
				//alert(i);
				var _data = $.parseXML(sdata),
					url = '".$url."',
					url_split = url.split('/'),
					docId = url_split.pop(),
					doc = url_split.pop(),
					replyId = \$(_data).find('url').text();
				$.ajax({
					url: '".$device["ajax"]."',
					type: 'POST',
					data: {
						egais_func: '".__FUNCTION__."',
						egais_replyId: replyId,
						egais_url: url,
						egais_data: '".$data."'
					},
					success: function(data){
						if(data.indexOf('#reboot#') >= 0) {
							$(document).trigger('egais-updateOptIn', [1]);
							".self::del_func("url")."
						}
					}
				});
			}";
			return self::set(empty($url) ? "/opt/in" : $url, __FUNCTION__, $onSuccess, $data);
		}
	}

	function onPrologOptIn()
	{
		if($_REQUEST["egais_func"] == "updateOptIn")
		{
			global $APPLICATION;
			$APPLICATION->RestartBuffer();

			$url_split = split("/", parse_url($_REQUEST["egais_url"], PHP_URL_PATH));
			$docId = $url_split[4];
			$doc = $url_split[3];

			$arFields = array(
				"STORE_ID" => SMShops::getUserShop(),
				"OPT" => "IN",
				"REPLY_ID" => (!empty($_REQUEST["egais_replyId"]) ? $_REQUEST["egais_replyId"] : ""),
				"DOCUMENT" => $doc
			);

			$update = array();
			$opt = new SMEGAIS();
			$rsOpt = $opt->getList(array(), $arFields, false, false, array("ID", "XML", "DOCUMENT_NUMBER"));
			while($arOpt = $rsOpt->Fetch())
				$update[] = $arOpt;

			$arFields["DOCUMENT_NUMBER"] = (!empty($_REQUEST["egais_docId"]) ? $_REQUEST["egais_docId"] : $docId);
			$arFields["URL"] = $_REQUEST["egais_url"];
			$arFields["XML"] = $_REQUEST["egais_data"];

			foreach ($arFields as $key => $value) 
				if(empty($value))
					unset($arFields[$key]);

			if(!empty($update))
			{
				foreach ($update as $arOpt) 
					if(empty($arOpt["XML"]) || empty($arOpt["DOCUMENT_NUMBER"]))
					{
						$opt->update($arOpt["ID"], $arFields);
						echo "#reboot#";
					}
			}
			else
			{
				$opt->add($arFields);
				echo "#reboot#";
			}
			die();
		}
	}

	function getFsrarRsa()
	{
		return "020000103154";
	}

	static function getList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
	{
		global $DB;
		CModule::IncludeModule("catalog");
		if (empty($arSelectFields))
			$arSelectFields = array("ID", "STORE_ID", "OPT", "REPLY_ID", "URL", "DOCUMENT", "DOCUMENT_NUMBER", "XML");

		$arFields = array(
			"ID" => array("FIELD" => "SME.ID", "TYPE" => "int"),
			"STORE_ID" => array("FIELD" => "SME.STORE_ID", "TYPE" => "int"),
			"OPT" => array("FIELD" => "SME.OPT", "TYPE" => "string"),
			"REPLY_ID" => array("FIELD" => "SME.REPLY_ID", "TYPE" => "string"),
			"URL" => array("FIELD" => "SME.URL", "TYPE" => "string"),
			"DOCUMENT" => array("FIELD" => "SME.DOCUMENT", "TYPE" => "string"),
			"DOCUMENT_NUMBER" => array("FIELD" => "SME.DOCUMENT_NUMBER", "TYPE" => "string"),
			"XML" => array("FIELD" => "SME.XML", "TYPE" => "string")
		);

		$arSqls = CCatalog::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);
		$arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "", $arSqls["SELECT"]);
		
		if (empty($arGroupBy) && is_array($arGroupBy))
		{
			$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais SME ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			if ($arRes = $dbRes->Fetch())
				return $arRes["CNT"];
			else
				return false;
		}

		$strSql = "SELECT ".$arSqls["SELECT"]." FROM b_sm_egais SME ".$arSqls["FROM"];
		if (!empty($arSqls["WHERE"]))
			$strSql .= " WHERE ".$arSqls["WHERE"];
		if (!empty($arSqls["GROUPBY"]))
			$strSql .= " GROUP BY ".$arSqls["GROUPBY"];
		if (!empty($arSqls["ORDERBY"]))
			$strSql .= " ORDER BY ".$arSqls["ORDERBY"];

		$intTopCount = 0;
		$boolNavStartParams = (!empty($arNavStartParams) && is_array($arNavStartParams));
		if ($boolNavStartParams && array_key_exists('nTopCount', $arNavStartParams))
		{
			$intTopCount = intval($arNavStartParams["nTopCount"]);
		}
		if ($boolNavStartParams && 0 >= $intTopCount)
		{
			$strSql_tmp = "SELECT COUNT('x') as CNT FROM b_sm_egais SME ".$arSqls["FROM"];
			if (!empty($arSqls["WHERE"]))
				$strSql_tmp .= " WHERE ".$arSqls["WHERE"];
			if (!empty($arSqls["GROUPBY"]))
				$strSql_tmp .= " GROUP BY ".$arSqls["GROUPBY"];

			$dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$cnt = 0;
			if (empty($arSqls["GROUPBY"]))
			{
				if ($arRes = $dbRes->Fetch())
					$cnt = $arRes["CNT"];
			}
			else
			{
				$cnt = $dbRes->SelectedRowsCount();
			}

			$dbRes = new CDBResult();

			$dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
		}
		else
		{
			if ($boolNavStartParams && 0 < $intTopCount)
			{
				$strSql .= " LIMIT ".$intTopCount;
			}
			$dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		return $dbRes;
	}

	/**
	* @static
	* @param $arFields
	* @return bool|int
	*/
	static function add($arFields)
	{
		global $DB;

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISAdd", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$arFields)) === false)
				return false;

		$arInsert = $DB->PrepareInsert("b_sm_egais", $arFields);

		$strSql = "INSERT INTO b_sm_egais (".$arInsert[0].") VALUES(".$arInsert[1].")";

		$res = $DB->Query($strSql, False, "File: ".__FILE__."<br>Line: ".__LINE__);
		if(!$res)
			return false;
		$lastId = intval($DB->LastID());

		foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISAdd", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($lastId, $arFields));

		return $lastId;
	}

	/**
	 * @param $id
	 * @param $arFields
	 * @return bool
	 */
	public static function update($id, $arFields)
	{
		/** @global CDataBase $DB */
		global $DB;
		$id = (int)$id;

		foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISUpdate", true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array($id, &$arFields)) === false)
				return false;

		$strUpdate = $DB->PrepareUpdate("b_sm_egais", $arFields);

		$strSql = "update b_sm_egais set ".$strUpdate." where ID = ".$id;
		$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__);

		foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISUpdate", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array($id, $arFields));

		return true;
	}

	public static function delete($id)
	{
		global $DB;
		$id = (int)$id;
		if($id > 0)
		{
			foreach(GetModuleEvents("yadadya.shopmate", "OnBeforeEGAISDelete", true) as $arEvent)
				if(ExecuteModuleEventEx($arEvent, array($id)) === false)
					return false;

			$DB->Query("DELETE FROM b_sm_egais WHERE ID = ".$id, true);

			foreach(GetModuleEvents("yadadya.shopmate", "OnEGAISDelete", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($id));

			return $id;
		}
		return false;
	}
}