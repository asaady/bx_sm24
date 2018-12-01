<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$ORDER_ID = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
function getBasketItems($ORDER_ID)
{
	//$arBasketItems = CSalePaySystemAction::GetParamValue("BASKET_ITEMS");
	//if(!is_array($arBasketItems))
	{
		$arBasketItems = array();
		$dbBasket = CSaleBasket::GetList(
			array("DATE_INSERT" => "ASC", "NAME" => "ASC"),
			array("ORDER_ID" => $ORDER_ID),
			false, false,
			array("ID", "PRICE", "CURRENCY", "QUANTITY", "NAME", "VAT_RATE", "MEASURE_NAME", "DISCOUNT_PRICE")
		);
		while ($arBasket = $dbBasket->Fetch())
		{
			$arBasket["PRICE"] = $arBasket["PRICE"] + $arBasket["DISCOUNT_PRICE"];
			// props in product basket
			$arProdProps = array();
			$dbBasketProps = CSaleBasket::GetPropsList(
				array("SORT" => "ASC", "ID" => "DESC"),
				array(
					"BASKET_ID" => $arBasket["ID"],
					"!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")
				),
				false,
				false,
				array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
			);
			while ($arBasketProps = $dbBasketProps->GetNext())
			{
				if (!empty($arBasketProps) && $arBasketProps["VALUE"] != "")
					$arProdProps[] = $arBasketProps;
			}
			$arBasket["PROPS"] = $arProdProps;
			$arBasketItems[] = $arBasket;
		}
	}
	return $arBasketItems;
}
if(!empty($_POST["submit"]) && $_REQUEST["CASH_PAY"] >= $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]):
	$DEVICE = new SMCashDevice(array("ajax" => $APPLICATION->GetCurPageParam(), "post" => array("submit" => "Y", "CASH_PAY" => $_REQUEST["CASH_PAY"])));?>
<script src="<?=SITE_TEMPLATE_PATH?>/static/js/jquery.js"></script>
<div id="cash_device">
	<?
	if(!empty($_REQUEST["device_data"]))
	{
		$answer_xml = simplexml_load_string("<answer>".$_REQUEST["device_data"]."</answer>");
		switch ($_REQUEST["device_func"]) 
		{
			case "enumDevices":
				$LDNumber = (string) $answer_xml->Devices->Device['LDNumber'];
				if($LDNumber > 0)
				{
					$_SESSION["LDNumber"] = $LDNumber;
					$DEVICE->deviceReady($LDNumber);
				}
				break;
			case "deviceReady":
				$LDNumber = (string) $answer_xml->Device['LDNumber'];
				$message = (string) $answer_xml->Device->ECRMode;
				$status = (string) $answer_xml->Device->ECRMode['status'];
				if(!empty($message))
					echo $message."<br>";

				if(empty($status) || $status == 2 || $DEVICE->isCashTest())
				{
					if($ORDER_ID > 0)
					{
						$arBasketItems = getBasketItems($ORDER_ID);
						$DEVICE->printReceipt($LDNumber, $text1 = "Чек №".$ORDER_ID, "Чек №".$ORDER_ID, $_REQUEST["CASH_PAY"], $arBasketItems, 0);
					}
				}
				break;
			case "printReceipt":
				$change = (string) $answer_xml->Change;
				$error = (string) $answer_xml->ErrorText;

				if(!empty($error))
					echo $error."<br>";
				echo "Сдача: ".PriceFormat(floatval($change))."<br>";
				$_SESSION["CHANGE"][$ORDER_ID] = $change;
				if(stripos($error, "ok") !== false || stripos($error, "ок") !== false || empty($error) || SMCashDevice::isCashTest())
				{
					if($ORDER_ID > 0)
					{
						CSaleOrder::PayOrder($ORDER_ID, "Y");
						$DEVICE->openDrawer($_SESSION["LDNumber"]);
					}
				}?>
					<br><br><button class="btn btn-primary btn-lg" onclick="window.parent.location.href = '<?=$APPLICATION->GetCurPageParam("", array("strIMessage", "pay_system_blank", "CODE"))?>';return false;">Закрыть</button>
				<?
					//CSaleOrder::PayOrder($ORDER_ID, "Y");
					//echo "<script>window.parent.location.href = '".$APPLICATION->GetCurPageParam("", array("strIMessage", "pay_system_blank", "CODE"))."';</script>";
					//echo "<script>window.parent.location.reload();</script>";
				break;
			case "openDrawer":
				$message = (string) $answer_xml->OverallDeviceResult;
				if(!empty($message))
					echo $message."<br>";
				echo "Сдача: ".PriceFormat(floatval($_SESSION["CHANGE"][$ORDER_ID]))."<br>";
				unset($_SESSION["CHANGE"][$ORDER_ID]);
				if(stripos($message, "ok") !== false || stripos($message, "ок") !== false || empty($error) || $DEVICE->isCashTest())
				{?>
					<br><br><button class="btn btn-primary btn-lg" onclick="window.parent.location.href = '<?=$APPLICATION->GetCurPageParam("", array("strIMessage", "pay_system_blank", "CODE"))?>';return false;">Закрыть</button>
				<?}
				break;

		}
	}
	else
	{
		$deviceReady = $APPLICATION->get_cookie("DEVCE_READY");
		$ECRModeStatus = $APPLICATION->get_cookie("ECR_MODE_STATUS");
		$LDNumber = $APPLICATION->get_cookie("LDNUMBER");

		
		if($LDNumber > 0)
		{
			if($deviceReady == "Y")
			{
				$arBasketItems = getBasketItems($ORDER_ID);
				$DEVICE->printReceipt($LDNumber, $text1 = "Чек №".$ORDER_ID, "Чек №".$ORDER_ID, $_REQUEST["CASH_PAY"], $arBasketItems, 0);
			}
			else
				$DEVICE->deviceReady($LDNumber);
		}
		else
			$DEVICE->enumDevices();
		/*if($ORDER_ID > 0)
		{
			//echo "Сдача: ".PriceFormat(floatval($change));
			$arBasketItems = getBasketItems($ORDER_ID);
			$arStoreQuantity = array();
			if (!empty($arBasketItems))
				foreach($arBasketItems as $arBasket)
					$arStoreQuantity[$arBasket["ID"]][0] = array(
						"STORE_ID" => $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["STORE_ID"],
						"QUANTITY" => $arBasket["QUANTITY"]
					);
			CSaleOrder::DeductOrder($ORDER_ID, "Y", "", false, $arStoreQuantity);
			CSaleOrder::PayOrder($ORDER_ID, "Y");
		}*/
	}
?>
</div>
<?else:?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?$APPLICATION->ShowHead()?>
	<title><?$APPLICATION->ShowTitle()?></title>
	<?//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/bootstrap.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/jquery.select2.css");?>

	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/style.default.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/morris.css");?>
	<?//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/select2.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/js/css3clock/css/style.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/style.calendar.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/js/keyboard/css/keyboard.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/style.css");?>
</head>

<body>
	<form action="" method="post">
		<label>К оплате: <input type="text" disableb id="price" value="<?=$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]?>"  style="border:none;"> руб.</label><br>
		<label>Принято: <input class="keyboard keyboard_np keyboard_open" type="text" autofocus id="payed" name="CASH_PAY" onkeyup="calcChange(this);" onchange="calcChange(this);" onfocus="calcChange(this);"> руб.</label><br>
		<label>Сдача: <input type="text" disableb id="change" value="<?=($_REQUEST["CASH_PAY"] - $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"])?>"  style="border:none;"> руб.</label><br>
		<div class="btn-list keyboard_block">
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
		<input class="btn btn-primary btn-lg" type="submit" name="submit" value="Оплатить">
	</form>
	<script>
		var calcChange = function(_payed)
		{
			var _price = document.getElementById("price"),
				_change = document.getElementById("change"),
				_change = document.getElementById("change"),
				price = parseFloat(_price.value),
				payed = parseFloat(_payed.value),
				change = payed - price > 0 ? Math.ceil((payed - price)*100)/100 : 0;
			_change.value = change;
		}
		calcChange(document.getElementById("payed"));

	</script>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery-1.11.1.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery-migrate-1.2.1.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/bootstrap.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/modernizr.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/pace.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/retina.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.cookies.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scrollTo.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.slimscroll.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.resize.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.spline.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.sparkline.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/morris.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/raphael-2.1.0.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/bootstrap-wizard.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/select2.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/css3clock/js/css3clock.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery-ui-1.10.3.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/moment.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fullcalendar.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/custom.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/dashboard.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jqueru.select2.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jqueru.select2.ru.js");?>
	
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scannerdetection.compatibility.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scannerdetection.js");?>
	
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.hotkeys.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/keyboard/js/jquery.keyboard.js");?>
	
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/script.js");?>
</body>
</html>
<?endif?>