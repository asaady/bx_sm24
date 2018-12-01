<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$DEVICE = new SMCashDevice(array("ajax" => $APPLICATION->GetCurPageParam()));
if (!$DEVICE->isCashTest())
	SMCashDevice::checkDeviceReady();

global $USER;
if(!empty($_REQUEST["device_action"]))
{
	$APPLICATION->RestartBuffer();

	if(!empty($_REQUEST["device_data"])):
		$answer_xml = simplexml_load_string("<answer>".$_REQUEST["device_data"]."</answer>");
		switch ($_REQUEST["device_func"]) 
		{
			case "enumDevices":
				$LDNumber = (string) $answer_xml->Devices->Device['LDNumber'];
				if($LDNumber > 0)
				{
					$DEVICE->deviceReady($LDNumber);
					$APPLICATION->set_cookie("LDNUMBER", $LDNumber, false, "/", $_SERVER["SERVER_NAME"]);
				}
				break;
			case "deviceReady":
				$LDNumber = (string) $answer_xml->Device['LDNumber'];
				$message = (string) $answer_xml->Device->OverallDeviceResult;
				$APPLICATION->set_cookie("LDNUMBER", $LDNumber, false, "/", $_SERVER["SERVER_NAME"]);
				if(!empty($message))
					echo $message."<br>";
				if(stripos($message, "ok") !== false || stripos($message, "ок") !== false || $DEVICE->isCashTest())
				{
					if(stripos($_REQUEST["device_action"], "Openshift") !== false)
						$DEVICE->Openshift($LDNumber);
					if(stripos($_REQUEST["device_action"], "Zreport") !== false)
						$DEVICE->Zreport($LDNumber);
					if(stripos($_REQUEST["device_action"], "repeatDocument") !== false)
						$DEVICE->repeatDocument($LDNumber);
					if(stripos($_REQUEST["device_action"], "Xreport") !== false)
						$DEVICE->Xreport($LDNumber);
					if(stripos($_REQUEST["device_action"], "cashcollection") !== false)
					{
						if($_REQUEST['takethemoney'] == 'Y')
							$DEVICE->tableWrite($LDNumber, 1, 2, 1, 1);
						else
							$DEVICE->tableWrite($LDNumber, 1, 2, 1, 0);
					}
				}
				break;
			case "repeatDocument":
			case "Openshift":
			case "Zreport":
			case "Xreport":
			case "report":
				$OperatorNumber = (string) $answer_xml->OperatorNumber;
				$OverallResult = (string) $answer_xml->OverallResult;
				echo $OverallResult.'<br/>';

				if($OperatorNumber > 0 && (stripos($OverallResult, "ok") !== false || stripos($OverallResult, "ок") !== false || $DEVICE->isCashTest()))
				{	
					if(stripos($_REQUEST["device_action"], "Openshift") !== false) 
					{						
						$APPLICATION->set_cookie("DEVCE_READY", "Y", false, "/", $_SERVER["SERVER_NAME"]);
						$APPLICATION->set_cookie("ECR_MODE_STATUS", 2, false, "/", $_SERVER["SERVER_NAME"]);
						echo "<script>window.parent.location.reload();</script>";
					}
					if(stripos($_REQUEST["device_action"], "Zreport") !== false) 
					{
						$APPLICATION->set_cookie("DEVCE_READY", "N", false, "/", $_SERVER["SERVER_NAME"]);
						$APPLICATION->set_cookie("ECR_MODE_STATUS", 4, false, "/", $_SERVER["SERVER_NAME"]);
						$LDNumber = $APPLICATION->get_cookie("LDNUMBER");
						$DEVICE->openDrawer($LDNumber);
					}
					if(stripos($_REQUEST['device_action'], "repeatDocument") !== false)
					{
						echo "<script>window.parent.location.reload();</script>";
					}

				}
				break;
			case "openDrawer":
				$message = (string) $answer_xml->OverallDeviceResult;
				if(!empty($message))
					echo $message."<br>";
				if(stripos($message, "ok") !== false || stripos($message, "ок") !== false || $DEVICE->isCashTest())
				{
					echo "<script>window.parent.location.reload();</script>";
				}
				break;
			case "posSettlement":
				$error = (string) $answer_xml->Error;
				if(!empty($error))
					echo $error."<br>";
				if($_REQUEST['device_action'] != "posSettlement")
					$DEVICE->enumDevices();
				break;
			case "tableWrite":
				$LDNumber = $APPLICATION->get_cookie("LDNUMBER");
				$DEVICE->Zreport($LDNumber);
				break;
		}
	else:?>
	<script src="<?=SITE_TEMPLATE_PATH?>/static/js/jquery.js"></script>
	<div id="cash_device">
		<?if($_REQUEST["device_action"] == "Zreport" || $_REQUEST["device_action"] == "posSettlement")
			$DEVICE->posSettlement("15891411");
		else
		{
			$deviceReady = $APPLICATION->get_cookie("DEVCE_READY");
			$ECRModeStatus = $APPLICATION->get_cookie("ECR_MODE_STATUS");
			$LDNumber = $APPLICATION->get_cookie("LDNUMBER");

			
			if($LDNumber > 0)
			{
				if($deviceReady == "Y")
				{
					if(stripos($_REQUEST["device_action"], "Openshift") !== false)
						$DEVICE->Openshift($LDNumber);
					if(stripos($_REQUEST["device_action"], "Zreport") !== false)
						$DEVICE->Zreport($LDNumber);
					if(stripos($_REQUEST["device_action"], "repeatDocument") !== false)
						$DEVICE->repeatDocument($LDNumber);
					if(stripos($_REQUEST["device_action"], "Xreport") !== false)
						$DEVICE->Xreport($LDNumber);
					if(stripos($_REQUEST["device_action"], "cashcollection") !== false)
					{
						if($_REQUEST['takethemoney'] == 'Y')
							$DEVICE->tableWrite($LDNumber, 1, 2, 1, 1);
						else
							$DEVICE->tableWrite($LDNumber, 1, 2, 1, 0);
					}
				}
				else
					$DEVICE->deviceReady($LDNumber);
			}
			else
				$DEVICE->enumDevices();
		}?>
	</div>
	<?endif;
	die();
}
?>
<div id="cash_device">
</div>

<script type="text/javascript">
	function takethemoney()
	{
		var takethemoney = confirm("Изъять наличные из кассы?") ? 'Y' : 'N';

		$("#cashcollectionclose").data('src', $("#cashcollectionclose").data('src') + '&takethemoney=' + takethemoney);
		$("#cashcollectionclose").prev().click();

		return false;
	}
</script>