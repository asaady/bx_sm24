<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

	CModule::IncludeModule("iblock");
	CModule::IncludeModule("yadadya.shopmate");

	global $APPLICATION;

	$APPLICATION->RestartBuffer();

	if($_REQUEST['cmd'] != '')
	{
		switch($_REQUEST['cmd'])
		{
			case "reload_db":
				header("Location: http://shopmate.yadadya.net/dnc/goods_".$_REQUEST['shop']. "_" . $_REQUEST['store_cash'] . ".txt");
			break;
			case "rm_goods":
				$file_name = $_SERVER['DOCUMENT_ROOT'].'dnc/'.$_FILES['upload']['name'].'_'.$_REQUEST['shop'].'_'.$_REQUEST['store_cash'].'.php';
				move_uploaded_file($_FILES['upload']['tmp_name'], $file_name);

				$log = "Response from ".$_REQUEST['shop']." shop and ".$_REQUEST['store_cash']." store cash\r\n";
				$log .= iconv('windows-1251', 'UTF-8', file_get_contents($file_name));

				file_put_contents($_SERVER['DOCUMENT_ROOT'].'dnc/log.php', file_get_contents($_SERVER['DOCUMENT_ROOT'].'dnc/log.php')."\r\n".$log);

				SMDncDB::RemoveTasksForCurrentCashbox($_REQUEST['store_cash'], $_REQUEST['shop']);
				SMDncDB::deleteStoreProducts($_REQUEST['shop']);
				unlink($_SERVER['DOCUMENT_ROOT']."dnc/goods_".$_REQUEST['shop']. "_" . $_REQUEST['store_cash'] . ".txt");
				unlink($file_name);
			break;
		}
	}
	else
	{
		// есть ли такая касса в таблице касс
		if(!SMDncDB::getCashboxByNumberAndStore($_REQUEST['store_cash'], $_REQUEST['shop']))
		{
			// если нет, то добавляем, говорим, что ей нужно обновиться и генерируем полную выгрузку
			SMDncDB::addCashbox(array("CASHBOX" => $_REQUEST['store_cash'], "SHOP" => $_REQUEST['shop'], "STATUS" => "ON"));
			SMDncDB::addToCashboxMonitor(array("CASHBOX" => $_REQUEST['store_cash'], "SHOP" => $_REQUEST['shop']));
			// генерация полной выгрузки
			$store_id = $_REQUEST['shop'];
			$total = array();

			$res = SMDnc::GetSections('', $store_id, $total);

			$text = SMDnc::GetStoreGoodsExport($store_id, true);

			// добавили файл выгрузки
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . 'dnc/goods_' . $store_id . "_" . $_REQUEST['store_cash'] . '.txt', $text);
		}

		if($tsk = SMDncDB::GetTasksForCurrentCashbox($_REQUEST['store_cash'], $_REQUEST['shop']))
		{
			// закачать файл выгрузки и выгрузить в кассу его
			echo "RELOAD_DB";
		}
	}
	die();
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>