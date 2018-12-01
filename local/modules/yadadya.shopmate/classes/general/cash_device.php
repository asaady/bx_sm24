<?php

IncludeModuleLangFile(__FILE__);

class SMCashDevice
{
	private $device = array (
		"ip" => "127.0.0.1",
		"port" => "54321",
		"ajax" => "",
		"info_selector" => "#cash_device",
		"log_file" => "/test.txt"
	);

	public static $request = array (
		"checkDevice" => array("id" => "{69d7397f-a977-4dca-b4ef-75949f33e71f}", "function" => "checkDevice"),
		"deviceReady" => array("id" => "{40370679-959c-4f49-834a-65e741556213}", "function" => "deviceReady"),
		"isDrawerOpened" => array("id" => "{afa422d8-ae40-4611-a592-9a5942441aba}", "function" => "isDrawerOpened"),
		"openDrawer" => array("id" => "{4180f328-f471-4658-820c-8698777e916c}", "function" => "openDrawer"),
		"report" => array("id" => "{ea9023d4-2b7f-4417-9d82-1833a7993972}", "function" => "sessionControl"),
		"Xreport" => array("id" => "{ea9023d4-2b7f-4417-9d82-1833a7993972}", "function" => "sessionControl"),
		"Zreport" => array("id" => "{ea9023d4-2b7f-4417-9d82-1833a7993972}", "function" => "sessionControl"),
		"Openshift" => array("id" => "{ea9023d4-2b7f-4417-9d82-1833a7993972}", "function" => "sessionControl"),
		"printReceipt" => array("id" => "{2ee8cce1-ea57-41f9-b8c4-0e5b8239d5fe}", "function" => "printReceipt"),
		"repeatDocument" => array("id" => "{ae27365b-9b8e-4224-9a74-af9e378b1401}", "function" => "repeatDocument"),
		"returnSale" => array("id" => "{e6594580-59fe-417a-8ec9-0c41fc1e35ea}", "function" => "returnSale"),
		"tableWrite" => array("id" => "{41d94a27-a10f-4687-9768-5454d79a8805}", "function" => "returnSale"),

		"enumDevices" => array("id" => "{73ece452-122d-4d82-af24-5e78e2f2c68b}", "function" => "enumDevices"),
		"posPay" => array("id" => "{7623fb4d-9144-4ef8-99d5-68c7f70014e7}", "function" => "posPay"),
		"posCancel" => array("id" => "{7623fb4d-9144-4ef8-99d5-68c7f70014e7}", "function" => "posCancel"),
		"posRefund" => array("id" => "{7623fb4d-9144-4ef8-99d5-68c7f70014e7}", "function" => "posRefund"),
		"posSettlement" => array("id" => "{7623fb4d-9144-4ef8-99d5-68c7f70014e7}", "function" => "posSettlement"),
		"posAbort" => array("id" => "{9c54b1e9-2e69-4d56-b9f2-52e5a1930980}", "function" => "posAbort"),
		"resetPrinter" => array("id" => "{c172dff7-d527-477c-8277-6286cebb5f43}", "function" => "resetPrinter"),
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

	function enumDevices()
	{
		$string="";
		return self::show($string, __FUNCTION__);
	}

	function checkDevice($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>";
		return self::show($string, __FUNCTION__);
	}

	function deviceReady($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>";
		return self::show($string, __FUNCTION__);
	}

	function isDrawerOpened($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>";
		return self::show($string, __FUNCTION__);
	}

	function openDrawer($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<DrawerNumber LDNumber=\\\"".$LDNumber."\\\"\>0\<\/DrawerNumber>";
		return self::show($string, __FUNCTION__);
	}

	function report($LDNumber, $Action, $func = "")
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>\<Action\>".$Action."\<\/Action\>";
		return self::show($string, !empty($func) ? $func : __FUNCTION__);
	}

	function Xreport($LDNumber)
	{
		return self::report($LDNumber, "X", __FUNCTION__);
	}

	function Zreport($LDNumber)
	{
		return self::report($LDNumber, "Z", __FUNCTION__);
	}

	function Openshift($LDNumber)
	{
		return self::report($LDNumber, "Open", __FUNCTION__);
	}

	function printReceipt($LDNumber, $text1 = "", $text2 = "", $summ = 0, $arItems = array(), $tpay = 0)
	{
		if($tpay == 0) $tpay = "cash";
		$items = "";
		foreach($arItems as $arItem)
		{
			$arItem["NAME"] = substr(preg_replace('/%[^A-Za-zА-Яа-я0-9 ]%/u', '', $arItem["NAME"]), 0, 40);
			$items .= "\<Item department=\\\"1\\\" tax1=\\\"1\\\" quantity=\\\"".$arItem["QUANTITY"]."\\\" price=\\\"".$arItem["PRICE"]."\\\"\>".$arItem["NAME"]."\<\/Item\>";
			if($arItem["DISCOUNT_PRICE"] > 0) $items .= "\<Discount tax1=\\\"1\\\" summ=\\\"".$arItem["DISCOUNT_PRICE"]."\\\"\>Скидка\<\/Discount\>";
		}
		$string = "\<OperatorPass\>30\<\/OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/>\<ReceiptData openTray=\\\"yes\\\"\>\<Text align=\\\"center\\\"\>".$text1."\<\/Text>\<Text align=\\\"center\\\"\>".$text2."\<\/Text>".$items."\<\/ReceiptData\>\<Summ type=\\\"".$tpay."\\\"\>".$summ."\<\/Summ\>";
		return self::show($string, __FUNCTION__);
	}

	function printTest($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>\<ReceiptData openTray=\\\"yes\\\"\/\>";
		return self::show($string, __FUNCTION__);
	}

	function resetPrinter($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>";
		return self::show($string, __FUNCTION__);
	}

	function repeatDocument($LDNumber)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>";
		return self::show($string, __FUNCTION__);
	}

	function returnSale($LDNumber, $text1 = "", $text2 = "", $summ = 0, $arItems = array(), $tpay = 0)
	{
		if($tpay == 0) $tpay = "cash";
		$items = "";
		if(empty($arItems))
		{
			$arItems = array(
				array(
					"NAME" => "Возврат",
					"QUANTITY" => 1,
					"PRICE" => $summ
				)
			);
		}
		foreach($arItems as $arItem)
		{
			$arItem["NAME"] = substr(preg_replace('/%[^A-Za-zА-Яа-я0-9 ]%/u', '', $arItem["NAME"]), 0, 40);
			$items .= "\<Item department=\\\"1\\\" tax1=\\\"1\\\" quantity=\\\"".$arItem["QUANTITY"]."\\\" price=\\\"".$arItem["PRICE"]."\\\"\>".$arItem["NAME"]."\<\/Item\>";
			//if($arItem["DISCOUNT_PRICE"] > 0) $items .= "\<Discount tax1=\\\"1\\\" summ=\\\"".$arItem["DISCOUNT_PRICE"]."\\\"\>Скидка\<\/Discount\>";
		}
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>\<Return\>".$items."\</Return\>\<Summ type=\\\"".$tpay."\\\"\>".$summ."\<\/Summ\>";
		//$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>\<ItemText\>".$sale."\</ItemText\>\<Quantity\>".$quantity."\</Quantity\>\<Price\>".$price."\</Price\>\<Department\>".$department."\</Department\>\<Tax1\>1\</Tax1\>";
		return self::show($string, __FUNCTION__);
	}

	function tableWrite($LDNumber, $table, $row, $col, $value)
	{
		$string = "\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"".$LDNumber."\\\"\/\>\<Modify table=\\\"".$table."\\\" row=\\\"".$row."\\\" col=\\\"".$col."\\\" value=\\\"".$value."\\\" \/\>";
		return self::show($string, __FUNCTION__);
	}

	function posPay($Amount, $TerminalId)
	{
		$TerminalId = 15891411;
		$string = "\<TerminalTrxId\>1\<\/TerminalTrxId\>\<Amount\>".$Amount."\<\/Amount\>\<CurrencyCode\>643\<\/CurrencyCode\>\<CardEntryMode\>3\<\/CardEntryMode\>\<TerminalId\>".$TerminalId."\<\/TerminalId\>\<TimeOut\>180000\<\/TimeOut\>";
		return self::show($string, __FUNCTION__, "<br>".GetMessage("SM_DEVICE_AMOUNT").": ".$Amount);
	}

	function posCancel($Amount, $TerminalId, $AuthorizationCode, $RRN)
	{
		$TerminalId = 15891411;
		$string = "\<TerminalTrxId\>1\<\/TerminalTrxId\>\<Amount\>".$Amount."\<\/Amount\>\<CurrencyCode\>643\<\/CurrencyCode\>\<CardEntryMode\>3\<\/CardEntryMode\>\<AuthorizationCode\>".$AuthorizationCode."\<\/AuthorizationCode\>\<RRN\>".$RRN."\<\/RRN\>\<TerminalId\>".$TerminalId."\<\/TerminalId\>\<OrigOperation\>1\<\/OrigOperation\>\<TimeOut\>180000\<\/TimeOut\>\<AdditionalAmount\>0\<\/AdditionalAmount\>";
		return self::show($string, __FUNCTION__, 
			"<br>".GetMessage("SM_DEVICE_AMOUNT").": ".$Amount
			/*."<br>AuthorizationCode: ".$AuthorizationCode
			."<br>RRN: ".$RRN*/
		);
	}

	function posRefund($Amount, $TerminalId, $AuthorizationCode, $RRN)
	{
		$TerminalId = 15891411;
		$string = "\<TerminalTrxId\>1\<\/TerminalTrxId\>\<Amount\>".$Amount."\<\/Amount\>\<CurrencyCode\>643\<\/CurrencyCode\>\<CardEntryMode\>3\<\/CardEntryMode\>\<AuthorizationCode\>".$AuthorizationCode."\<\/AuthorizationCode\>\<RRN\>".$RRN."\<\/RRN\>\<TerminalId\>".$TerminalId."\<\/TerminalId\>\<OrigOperation\>1\<\/OrigOperation\>\<TimeOut\>180000\<\/TimeOut\>";
		return self::show($string, __FUNCTION__, "<br>".GetMessage("SM_DEVICE_AMOUNT").": ".$Amount);
	}

	function posSettlement($TerminalId)
	{
		$TerminalId = 15891411;
		$string = "\<TerminalTrxId\>1\<\/TerminalTrxId\>\<TerminalId\>".$TerminalId."\<\/TerminalId\>\<TimeOut\>180000\<\/TimeOut\>";
		return self::show($string, __FUNCTION__);
	}

	function posAbort()
	{
		$string = "\<TimeOut\>180000\<\/TimeOut\>";
		return self::show($string, __FUNCTION__);
	}

	function xml($xml, $func)
	{
		$xml = "\<?xml version=\\\"1.0\\\" encoding=\\\"windows-1251\\\"?\>\<!--SHTRICH-M PROXY EXCHANGE FORMAT--\>\<!DOCTYPE pef\>\<pef version=\\\"1.0\\\"\>\<Request id=\\\"".self::$request[$func]["id"]."\\\" function=\\\"".self::$request[$func]["function"]."\\\"\>".$xml."\</Request\>\</pef\>";
		return $xml;
	}

	function send($xml, $func, $onSuccess = "")
	{
		$device = self::getParams();
		$xml = self::xml($xml, $func);
		$post = "";
		file_put_contents($device["log_file"], PHP_EOL, FILE_APPEND);
		file_put_contents($device["log_file"], PHP_EOL, FILE_APPEND);
		file_put_contents($device["log_file"], PHP_EOL . "[".date("Y-m-d H:i:s")."] ".$func.": Request", FILE_APPEND);
		file_put_contents($device["log_file"], PHP_EOL . $xml, FILE_APPEND);
		if(is_array($device["post"]))
			foreach ($device["post"] as $key => $value) 
			{
				$post .= ", $key: \"$value\"";
			}
		$send = "var xml='".$xml."';  
if(!window.WebSocket) {
	document.body.innerHTML = 'WebSocket в этом браузере не поддерживается.';
}
var socket=new WebSocket(\"ws:\/\/".$device["ip"].":".$device["port"]."\");
var LDNumber = 0;
socket.onopen=function() {
	socket.send(xml);
};
socket.onmessage = ".(!empty($onSuccess) ? $onSuccess : "function(event) {
	// alert(\"Получены данные: \\n\" + event.data);
	if(window.jQuery)
	{
		var _data = $.parseXML(event.data),
			\$data = $(_data);
		$.ajax({
			url: '".$device["ajax"]."',
			type: 'POST',
			data: {
				device_func: '".$func."',
				device_data: \$data.find('pef').html()".$post."
			},
			success: function(data) {
				data = '".GetMessage("SM_DEVICE_".$func)."...<br>' + data;
				$('".$device["info_selector"]."').html(data);
			}
		});
	}
}").";
socket.onerror=function(error) {
	alert(\"По всей видимости сервер не запущен\");
};"; 
		return $send;
	}

	function show($xml, $func, $text = "")
	{
		//sleep(5);
		echo GetMessage("SM_DEVICE_".$func)."...
<script>
".self::send($xml, $func)."
</script>".$text;
	}

	function onGetResult()
	{
		if(!empty($_REQUEST["device_func"]) && !empty($_REQUEST["device_data"]))
		{
			$answer_xml = simplexml_load_string("<answer>".$_REQUEST["device_data"]."</answer>");
			$device = self::getParams();
			file_put_contents($device["log_file"], PHP_EOL, FILE_APPEND);
			file_put_contents($device["log_file"], PHP_EOL, FILE_APPEND);
			file_put_contents($device["log_file"], PHP_EOL . "[".date("Y-m-d H:i:s")."] ".$_REQUEST["device_func"].": Answer", FILE_APPEND);
			file_put_contents($device["log_file"], PHP_EOL . $_REQUEST["device_data"], FILE_APPEND);
			switch ($_REQUEST["device_func"]) 
			{
				case "posSettlement":
					$error = (string) $answer_xml->Error;
					$TerminalId = (string) $answer_xml->TerminalId;
					//$TerminalId = "40000030";
					if(stripos($error, "ok") !== false || stripos($error, "ок") !== false)
					{
						if(CModule::IncludeModule("sale") && CModule::IncludeModule("yadadya.shopmate"))
						{
							$ORDER_ID = 0;
							$STORE_ID = SMShops::getUserShop();
							$rsOrder = CSaleOrder::getList(array("ID" => "DESC"), array("SITE_ID" => SITE_ID, "STORE_ID" => $STORE_ID), false, false, array("ID"));
							if($arOrder = $rsOrder->Fetch())
								$ORDER_ID = $arOrder["ID"];
							SMTodoLog::Log($STORE_ID, "cd_pos_settlement", $TerminalId, $ORDER_ID);
						}
					}
					break;
				case "checkDeviceReady":
					global $APPLICATION;
					$APPLICATION->RestartBuffer();

					$LDNumber = (string) $answer_xml->Device['LDNumber'];
					$OperatorNumber = (string) $answer_xml->OperatorNumber;
					$ECRModeStatus = (int) $answer_xml->Device->ECRMode['status'];
					$ECRModeStatusDesc = (string) $answer_xml->Device->ECRMode;
					$error = (string) $answer_xml->Device->OverallDeviceResult;
					$deviceReady = "Y";
					$mess = "";

					if(stripos($error, "ok") !== false || stripos($error, "ок") !== false)
					{
						if($ECRModeStatus == 2)
							$mess = "ok";
						else
						{
							$mess = $ECRModeStatusDesc;
							$deviceReady = "N";
						}
					}
					else
					{
						$mess = $error;
						$deviceReady = "N";
					}

					$_deviceReady = $APPLICATION->get_cookie("DEVCE_READY");
					$_ECRModeStatus = $APPLICATION->get_cookie("ECR_MODE_STATUS");
					$APPLICATION->set_cookie("DEVCE_READY", $deviceReady, false, "/", $_SERVER["SERVER_NAME"]);
					$APPLICATION->set_cookie("ECR_MODE_STATUS", $ECRModeStatus, false, "/", $_SERVER["SERVER_NAME"]);
					$APPLICATION->set_cookie("LDNUMBER", $LDNumber, false, "/", $_SERVER["SERVER_NAME"]);

					if($deviceReady != $_deviceReady || $ECRModeStatus != $_ECRModeStatus)
						$mess = "#reboot#";

					echo $mess;

					die();
					break;
			}
		}
	}

	function getLastOrder($TerminalId)
	{
		$ORDER_ID = 0;
		if (CModule::IncludeModule("yadadya.shopmate"))
		{
			$rsTodo = SMTodoLog::GetList(array("ID" => "DESC"), array("SECTION_ID" => "cd_pos_settlement", "ITEM_ID" => $TerminalId), false, array("nTopCount" => 1), array("DESCRIPTION"));
			if($arTodo = $rsTodo->Fetch())
				$ORDER_ID = $arTodo["DESCRIPTION"];
		}
		return $ORDER_ID;
	}

	function isCashTest()
	{
		global $USER;
		return $USER->IsAdmin() && $_SESSION["cash_test"] == "Y" ? true : false;
	}

	function checkDeviceReady()
	{
		$device = self::getParams();
		echo "<script>
	".self::send("", "enumDevices", "function(event) {
		var xml = $($.parseXML(event.data));		
		var data = xml.find('pef');
		var error = '';
		//console.log(event.data);
		if(data){
			var finc_id = data.find('Request').attr('id');
			switch (finc_id) {
				case '".self::$request["enumDevices"]["id"]."':
					var devices = data.find('Devices');
					if(devices.length == 0){
						alert('Не найдено ни одного устройства.<br/>');
					}
					else{
						var lastError = devices.find('LastError');
						if(lastError.text() != 'No errors') 
							alert(lastError.text());
						else{
							var device = devices.find('Device');
							LDNumber = device.attr('LDNumber');
							if(!isNaN(parseInt(LDNumber)) && LDNumber > 0){
								xml = '".self::xml("\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"'+LDNumber+'\\\"\/\>", "resetPrinter")."';
								socket.send(xml);
							}
							else{
								alert('Не получилось извлечь номер устройства.<br/>');
							}
						}
					}

					break;

				case '".self::$request["resetPrinter"]["id"]."':
					if(!isNaN(parseInt(LDNumber)) && LDNumber > 0){
						xml = '".self::xml("\<OperatorPass\>30\</OperatorPass\>\<Device LDNumber=\\\"'+LDNumber+'\\\"\/\>", "deviceReady")."';
						socket.send(xml);
					}
					else{
						alert('Не получилось извлечь номер устройства.<br/>');
					}
					break;

				case '".self::$request["deviceReady"]["id"]."':
							var device = data.find('Device');
							res = {};
							device.children().each(function(){
								res[$(this).prop('nodeName')] = $(this).text();
							});
							res['ECRModeStatus'] = data.find('Device').find('ECRMode').attr('status');
							$.ajax({
								url: '".$device["ajax"]."',
								type: 'POST',
								data: {
									device_func: 'checkDeviceReady',
									device_data: xml.find('pef').html()
								},
								success: function(data) {
									/*data = '".GetMessage("SM_DEVICE_".$func)."...<br>' + data;
									$('".$device["info_selector"]."').html(data);*/
									if(data == 'ok'){

									}
									else if(data == '#reboot#'){
										window.location.reload();
									}
									else
										alert(data);

								}
							});
					break;
			}
		}
		else{
			alert('Нет ответа от сервера.<br/>');
		}
		return false;
	};")."
</script>";
	}
}