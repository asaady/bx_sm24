<?
define("STOP_STATISTICS", true);
$SITE_ID = '';
if(
	isset($_REQUEST["SITE_ID"])
	&& is_string($_REQUEST["SITE_ID"])
	&& strlen($_REQUEST["SITE_ID"]) > 0
)
{
	$SITE_ID = substr(preg_replace("/[^a-z0-9_]/i", "", $_REQUEST["SITE_ID"]), 0, 2);
	define("SITE_ID", $SITE_ID);
}

if(
	isset($_REQUEST["ADMIN_SECTION"])
	&& is_string($_REQUEST["ADMIN_SECTION"])
	&& trim($_REQUEST["ADMIN_SECTION"]) == "Y"
)
{
	define("ADMIN_SECTION", true);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use \Yadadya\Shopmate\Cash;

	function readReport($path, $store)
	{
		$text = file_get_contents($path);
		$text = explode("
", $text);
		if(count($text) == 1)
			$text = explode("\n", $text[0]);
		$number_of_lines = $text[12];
		$order = array();
		$last_trans = 0;

		for($i = 13; $i < 13 + $number_of_lines; $i++)
		{	
			$row = explode(';', $text[$i]);

			if($row[8] == 11 && $last_trans != 11)
				$order = array();
			$last_trans = $row[8];

			$ACCOUNT_NUMBER = $row[4]."_".$row[7]."_".$row[6]."_".substr($row[1], 0, 10);
			if($order["ACCOUNT_NUMBER"] != $ACCOUNT_NUMBER)
				$order = array(
					"DATE" => $row[1],
					"ACCOUNT_NUMBER" => $ACCOUNT_NUMBER,
					"LID" => SITE_ID,
					"CURRENCY" => "RUB",
					"FUSER_ID" => 1,
					"BASKET_ITEMS" => array()
				);

			switch($row[8])
			{
				case 11:
					// save prod and quantity
					$price = round($row[12] / $row[11], 2);
					$discount = $price - $row[10];
					$order["BASKET_ITEMS"][] = array(
						"CODE" => $row[9],
						"QUANTITY" => $row[11],
						"BARCODE" => $row[13],
						"PRICE" => $price,
						"DISCOUNT_PRICE" => $discount,
					);
				break;
				case 12:
					//strono - add and remove
				break;

				case 40:
					// handler update quantity
					$order["PAY_SYSTEM_ID"] = 1;
					$order["PAYED"] = "Y";
					$order["PAYMENT"] = $row[10];
					$order["CHANGE"] = $row[11];
				break;
				case 41:
					// handler update quantity
					$order["PAY_SYSTEM_ID"] = 4;
					$order["PAYED"] = "Y";
					$order["PAYMENT"] = $row[10];
				break;

				case 55:
					// close bill
					$order["STORE_ID"] = $store;
					$order["PRICE"] = $row[11];
					if(!empty($order["BASKET_ITEMS"]))
					{
						$bcFilter = array();
						foreach($order["BASKET_ITEMS"] as $item)
							$bcFilter[] = $item["BARCODE"];
						if(!empty($bcFilter))
						{
							$bcprods = \SMStoreBarcode::getProductsByBarcode($bcFilter, array(0, $order["STORE_ID"]), true);
							$save = true;
							foreach($order["BASKET_ITEMS"] as $ki => $item)
								if(!empty($bcprods[$item["BARCODE"]]))
									$order["BASKET_ITEMS"][$ki]["PRODUCT_ID"] = $bcprods[$item["BARCODE"]];
								else
									$save = false;
							if($save)
								Cash::DoSaveOrder($order);
							else
								file_put_contents($_SERVER['DOCUMENT_ROOT'].'/dnc/log.php', "\r\n".$file_name . ' error order: ' . $order["ACCOUNT_NUMBER"], FILE_APPEND);
						}
					}
				break;
				case 56:
					// cancellation bill
					/*if($row[5] == 1)
					{
						$order["STORE_ID"] = $store;
						$order["PRICE"] = $row[12];
						if(!empty($order["BASKET_ITEMS"]))
						{
							$bcFilter = array();
							foreach($order["BASKET_ITEMS"] as $item)
								$bcFilter[] = $item["BARCODE"];
							if(!empty($bcFilter))
							{
								$bcprods = \SMStoreBarcode::getProductsByBarcode($bcFilter, array(0, $order["STORE_ID"]), true);
								foreach($order["BASKET_ITEMS"] as $ki => $item)
									$order["BASKET_ITEMS"][$ki]["PRODUCT_ID"] = $bcprods[$item["BARCODE"]];
								if(count($bcprods) == count($bcFilter))
									print_p($order);
									//Cash\Order::DoSaveOrder($order);
							}
						}
						
						die();
					}*/
				break;
			}
		}
	}


	$loadfile = "";

	if(!empty($_FILES['upload']))
	{
		$path_parts = pathinfo($_FILES['upload']['name']);
		$loadfile = $path_parts["basename"];
		$file_name = $_SERVER['DOCUMENT_ROOT'].'/dnc/'.$path_parts["filename"].'_'.time().'.'.$path_parts["extension"];		
		move_uploaded_file($_FILES['upload']['tmp_name'], $file_name);
	}
	elseif(!empty($_REQUEST["loadfile"]))
	{
		$loadfile = $_REQUEST["loadfile"];
		$file_name = $_SERVER['DOCUMENT_ROOT'].'/dnc/'.$loadfile;
	}
	$file_name = str_replace(array("//", "\\\\"), array("/", "\\"), $file_name);

	preg_match_all('!\d+!', $loadfile, $matches);
	$store_id = $matches[0][0];

	\readReport($file_name, $store_id);
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/dnc/log.php', "\r\n".$file_name . ' added ' . date("Y-m-d H:i:s"), FILE_APPEND);

	if(!empty($_FILES['upload']) || $_REQUEST["removefile"] == "Y")
		unlink($file_name);
	
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
?>
