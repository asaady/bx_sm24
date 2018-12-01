<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<script src="<?=SITE_TEMPLATE_PATH?>/static/js/jquery.js"></script>
<div id="cash_device">
<?$ORDER_ID = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$DEVICE = new SMCashDevice(array("ajax" => $APPLICATION->GetCurPageParam(), "post" => array("submit" => "Y")));
function getBasketItems($ORDER_ID)
{
	$arBasketItems = CSalePaySystemAction::GetParamValue("BASKET_ITEMS");
	if(!is_array($arBasketItems))
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

if(!empty($_POST["submit"])):?>
	<?if(!empty($_REQUEST["device_data"]))
	{
		$answer_xml = simplexml_load_string("<answer>".$_REQUEST["device_data"]."</answer>");
		$deviceReady = $APPLICATION->get_cookie("DEVCE_READY");
		$ECRModeStatus = $APPLICATION->get_cookie("ECR_MODE_STATUS");
		$LDNumber = $APPLICATION->get_cookie("LDNUMBER");

		switch ($_REQUEST["device_func"]) 
		{
			case "posPay":
				$payed = (string) $answer_xml->Amount;
				$Response = (string) $answer_xml->Response;
				$error = (string) $answer_xml->Error;
				$AuthorizationCode = (string) $answer_xml->AuthorizationCode;
				$RRN = (string) $answer_xml->RRN;
				$TerminalId = (string) $answer_xml->TerminalId;

				if(!empty($error))
					echo $error."<br>";
				if(!empty($Response))
					echo $Response."<br>";
				if($ORDER_ID > 0)
					$arOrder = CSaleOrder::GetByID($ORDER_ID);
				if(stripos($error, "ok") !== false || stripos($error, "ок") !== false || empty($error) || $DEVICE->isCashTest())
				{
					if($LDNumber > 0)
					{
						if($deviceReady == "Y")
						{
							$arBasketItems = getBasketItems($ORDER_ID);
							if($arOrder = CSaleOrder::GetByID($ORDER_ID))
								$DEVICE->printReceipt($LDNumber, $text1 = "Чек №".$ORDER_ID, "Чек №".$ORDER_ID, $arOrder["PRICE"], $arBasketItems, 4);
						}
						else
							$DEVICE->deviceReady($LDNumber);
					}
					else
						$DEVICE->enumDevices();

					CSaleOrder::Update($ORDER_ID, array("PAY_VOUCHER_NUM" => $AuthorizationCode, "REASON_CANCELED" => $RRN));
				}
				else
				{?>
					<button onclick="window.parent.location.reload();">Повтор платежа</button>
					<button onclick="window.parent.location.href = '<?=$APPLICATION->GetCurPageParam("", array("strIMessage", "pay_system_blank"))?>';">Отмена платежа</button>
				<?}
				break;
			case "enumDevices":
				$LDNumber = (string) $answer_xml->Devices->Device['LDNumber'];
				if($LDNumber > 0)
					$DEVICE->deviceReady($LDNumber);
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
						$basket_price = 0;
						foreach ($arBasketItems as $arBasketItem) 
							$basket_price += round($arBasketItem["PRICE"] * $arBasketItem["QUANTITY"], 2);
						if($arOrder = CSaleOrder::GetByID($ORDER_ID))
						{
							/*if($arOrder["PRICE"] != $basket_price)
								echo "Ошибка рассчета итоговой суммы.";
							else*/
								$DEVICE->printReceipt($LDNumber, $text1 = "Чек №".$ORDER_ID, "Чек №".$ORDER_ID, $basket_price, $arBasketItems, 4);
						}
					}
				}
				break;
			case "printReceipt":
				$change = (string) $answer_xml->Change;
				$error = (string) $answer_xml->ErrorText;
				if(!empty($error))
					echo $error."<br>";
				if(stripos($error, "ok") !== false || stripos($error, "ок") !== false || empty($error))
				{
					CSaleOrder::PayOrder($ORDER_ID, "Y");
					echo "<script>window.parent.location.href = '".$APPLICATION->GetCurPageParam("", array("strIMessage", "pay_system_blank", "CODE"))."';</script>";
					//echo "<script>window.parent.location.reload();</script>";
				}
				else
					$DEVICE->posAbort();
				break;

		}
	}?>
<?else:
	if($ORDER_ID > 0)
	{
		$arBasketItems = getBasketItems($ORDER_ID);
		$basket_price = 0;
		foreach ($arBasketItems as $arBasketItem) 
			$basket_price += round($arBasketItem["PRICE"] * $arBasketItem["QUANTITY"], 2);
		if($arOrder = CSaleOrder::GetByID($ORDER_ID))
		{
			/*if($arOrder["PRICE"] != $basket_price)
				echo "Ошибка рассчета итоговой суммы.";
			else*/
				$DEVICE->posPay($arOrder["PRICE"], "15891411");
		}
	}
endif?>
</div>