<?
IncludeModuleLangFile(__FILE__);

$MODULE_ID = "yadadya.shopmate";

$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", Array("EVENT_NAME" => "PRODUCT_END_DATE"));
if(!($dbEvent->Fetch()))
{
	$langs = CLanguage::GetList(($b=""), ($o=""), array("LID" => "ru"));
	while($lang = $langs->Fetch())
	{
		$lid = $lang["LID"];
		IncludeModuleLangFile(__FILE__, $lid);

		$et = new CEventType;
		$et->Add(array(
			"LID" => $lid,
			"EVENT_NAME" => "PRODUCT_END_DATE",
			"NAME" => GetMessage("PRODUCT_END_DATE_NAME"),
			"DESCRIPTION" => GetMessage("PRODUCT_END_DATE_DESC"),
		));

		$arSites = array();
		$sites = CSite::GetList(($b=""), ($o=""), Array("LANGUAGE_ID"=>$lid));
		while ($site = $sites->Fetch())
			$arSites[] = $site["LID"];

		if(count($arSites) > 0)
		{
			$emess = new CEventMessage;
			$emess->Add(array(
				"ACTIVE" => "Y",
				"EVENT_NAME" => "PRODUCT_END_DATE",
				"LID" => $arSites,
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
				"EMAIL_TO" => "#STORE_EMAIL#",
				"SUBJECT" => GetMessage("PRODUCT_END_DATE_SUBJECT"),
				"MESSAGE" => GetMessage("PRODUCT_END_DATE_MESSAGE"),
				"BODY_TYPE" => "html",
			));
		}
	}
}