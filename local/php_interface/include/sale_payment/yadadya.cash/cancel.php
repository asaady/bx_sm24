<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<script src="<?=SITE_TEMPLATE_PATH?>/static/js/jquery.js"></script>
<div id="cash_device">
<?$ORDER_ID = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$DEVICE = new SMCashDevice(array("ajax" => $APPLICATION->GetCurPageParam(), "post" => array("submit" => "Y")));
function getCashback($ORDER_ID)
{
	$arCB = array(
		"Price" => 0,
		"AuthorizationCode" => "",
		"RRN" => "",
	);
	$oTodo = SMTodoLog::GetList(array(), array("SECTION_ID" => "cash_back", "ITEM_ID" => $ORDER_ID, "ACTIVE" => "N", "STORE_ID" => SMShops::getUserShop()), false, false, array("DESCRIPTION"));
	while($arTodo = $oTodo->Fetch())
	{
		$arCashback = unserialize($arTodo["DESCRIPTION"]);
		if($arCashback["Price"] > 0)
		{
			$arCB["Price"] += $arCashback["Price"];
			if(empty($arCB["AuthorizationCode"]) && !empty($arCashback["AuthorizationCode"]))
				$arCB["AuthorizationCode"] = $arCashback["AuthorizationCode"];
			if(empty($arCB["RRN"]) && !empty($arCashback["RRN"]))
				$arCB["RRN"] = $arCashback["RRN"];
		}
	}
	return $arCB;
}
function activateCashback($ORDER_ID)
{
	$oTodo = SMTodoLog::GetList(array(), array("SECTION_ID" => "cash_back", "ITEM_ID" => $ORDER_ID, "ACTIVE" => "N", "STORE_ID" => SMShops::getUserShop()), false, false, array("ID"));
	while($arTodo = $oTodo->Fetch())
		SMTodoLog::Activate($arTodo["ID"], true);
	CSaleOrder::CancelOrder($ORDER_ID, "Y");
}
if(!empty($_POST["submit"])):?>
	<?if(!empty($_REQUEST["device_data"]))
	{
		$answer_xml = simplexml_load_string("<answer>".$_REQUEST["device_data"]."</answer>");
		switch ($_REQUEST["device_func"]) 
		{
			case "enumDevices":
				$LDNumber = (string) $answer_xml->Devices->Device['LDNumber'];
				if($LDNumber > 0)
				{
					$_SESSION["LDNumber"] = $LDNumber;
					$arCashback = getCashback($ORDER_ID);
					$DEVICE->returnSale($LDNumber, "", "", $arCashback["Price"]);
					//$DEVICE->returnSale($LDNumber, $arCashback["Price"]);
				}
				break;
			case "returnSale":
				$error = (string) $answer_xml->ErrorText;
				if(!empty($error))
					echo $error."<br>";
				if(stripos($message, "ok") !== false || stripos($message, "ок") !== false || $DEVICE->isCashTest())
				{
					activateCashback($ORDER_ID);
					$DEVICE->openDrawer($_SESSION["LDNumber"]);
				}
				break;
			case "openDrawer":
				$message = (string) $answer_xml->OverallDeviceResult;
				if(!empty($message))
					echo $message."<br>";
				if(stripos($message, "ok") !== false || stripos($message, "ок") !== false || $DEVICE->isCashTest())
				{
					echo "Возврат завершен<br>";
					echo "<script>window.parent.location.reload();</script>";
				}
				break;
		}
	}?>
<?else:
	if($ORDER_ID > 0)
	{
		$arCashback = getCashback($ORDER_ID);
		if($arCashback["Price"] > 0)
			$DEVICE->enumDevices();
	}
endif?>
</div>