<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);

if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";
$FILTER_NAME = $arParams["FILTER_NAME"];
global ${$FILTER_NAME};

if (CModule::IncludeModule("catalog") && CModule::IncludeModule("yadadya.shopmate"))
{
	$arGroups = $USER->GetUserGroupArray();

	// check whether current user has access to view list
	if ($USER->IsAdmin() || SM::GetUserPermission("overhead") >= "R")
	{
		$bAllowAccess = true;
	}
	else
	{
		$bAllowAccess = false;
	}

	if(SMShops::getUserShop() <= 0)
		$bAllowAccess = false;

	// if user has access
	if ($bAllowAccess)
	{
		$arResult["CAN_EDIT"] = $bAllowAccess ? "Y" : "N";
		/*$arResult["CAN_DELETE"] = $arParams["ALLOW_DELETE"] == "Y" ? "Y" : "N";
		if(!$USER->CanDoOperation('catalog_store'))
			$arResult["CAN_EDIT"] = $arResult["CAN_DELETE"] = "N";*/

		if ($USER->GetID())
		{
			$arResult["NO_USER"] = "N";

			// set starting filter value
			/*$arFilter = array("IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"], "SHOW_NEW" => "Y");
			// check type of user association to iblock elements and add user association to filter

			if ($arParams["ELEMENT_ASSOC"] == "PROPERTY_ID" && intval($arParams["ELEMENT_ASSOC_PROPERTY"]) > 0 && in_array($arParams["ELEMENT_ASSOC_PROPERTY"], $arPropertyIDs))
			{
				$arFilter["PROPERTY_".$arParams["ELEMENT_ASSOC_PROPERTY"]] = $USER->GetID();
			}
			else
			{
				$arFilter["CREATED_BY"] = $USER->GetID();
			}*/

			// deleteting element
			/*if (check_bitrix_sessid() && $_REQUEST["delete"] == "Y" && $arResult["CAN_DELETE"])
			{
				$arParams["ID"] = intval($_REQUEST["CODE"]);

				// try to get element with id, for user and for iblock
				$rsElement = CIBLockElement::GetList(array(), array_merge($arFilter, array("ID" => $arParams["ID"])));
				if ($arElement = $rsElement->GetNext())
				{
					// delete one
					$DB->StartTransaction();
					if(!CIBlockElement::Delete($arElement["ID"]))
					{
						$DB->Rollback();
					}
					else
					{
						$DB->Commit();
					}
				}
			}

			if ($bWorkflowIncluded)
			{
				$by = "c_sort";
				$order = "asc";
				$is_filtered = false;
				$rsWFStatus = CWorkflowStatus::GetList($by, $order, array("ACTIVE" => "Y"), $is_filtered);
				$arResult["WF_STATUS"] = array();
				while ($arStatus = $rsWFStatus->GetNext())
				{
					$arResult["WF_STATUS"][$arStatus["ID"]] = $arStatus["TITLE"];
				}
			}
			else
			{
				$arResult["ACTIVE_STATUS"] = array("Y" => GetMessage("IBLOCK_FORM_STATUS_ACTIVE"), "N" => GetMessage("IBLOCK_FORM_STATUS_INACTIVE"));
			}*/

			// get elements list using generated filter
			//CShopmateStore
			$arFilter = array();
			$arFilter["SITE_ID"] = SITE_ID;
			$arFilter["DOC_TYPE"] = "A";
			$dbDocElement = CCatalogStoreDocsElement::getList(array(), array("STORE_TO" => SMShops::getUserShop()), array("DOC_ID"), false, array("DOC_ID"));
			while($arDocElement = $dbDocElement->Fetch())
				$arFilter["ID"][] = $arDocElement["DOC_ID"];
			if(empty($arFilter["ID"]))
				$arFilter["ID"] = false;
			$arNavParams = $arParams["NAV_ON_PAGE"] > 0 ? array("nPageSize"=>$arParams["NAV_ON_PAGE"]) : false;
			if(!empty(${$FILTER_NAME})) $arFilter = array_merge(${$FILTER_NAME}, $arFilter);
			if(is_array($arFilter["ID"]) && is_array(${$FILTER_NAME}["ID"]))
			{
				$tmpFilterID = array();
				foreach (${$FILTER_NAME}["ID"] as $fid) 
					if(in_array($fid, $arFilter["ID"]))
						$tmpFilterID[] = $fid;
				$arFilter["ID"] = $tmpFilterID;
			}

			if($_REQUEST["print"] == "Y")
			{
				$rsPrint = CCatalogDocs::getList(array("DATE_DOCUMENT" => "DESC"), $arFilter, false, false, array("ID"));
				while($arPrint = $rsPrint->Fetch())
					$arResult["PRINT"][] = $arPrint["ID"];
				$APPLICATION->RestartBuffer();
				$this->IncludeComponentTemplate("print");
				die();
			}

			$rsElements = CCatalogDocs::getList(array("DATE_DOCUMENT" => "DESC"), $arFilter);

			$arResult["ELEMENTS_COUNT"] = $rsElements->SelectedRowsCount();
			//$page_split = intval(COption::GetOptionString("iblock", "RESULTS_PAGEN"));
			$arParams["NAV_ON_PAGE"] = intval($arParams["NAV_ON_PAGE"]);
			$arParams["NAV_ON_PAGE"] = $arParams["NAV_ON_PAGE"] > 0 ? $arParams["NAV_ON_PAGE"] : 10;

			$rsElements->NavStart($arParams["NAV_ON_PAGE"]);

			// get paging to component result
			if ($arParams["NAV_ON_PAGE"] < $arResult["ELEMENTS_COUNT"])
			{
				$arResult["NAV_STRING"] = $rsElements->GetPageNavString(GetMessage("IBLOCK_LIST_PAGES_TITLE"), "", true);
			}

			// get current page elements to component result
			$arElements = $arDocsID = $arResult["ELEMENTS"] = array();
			$bCanEdit = false;
			$bCanDelete = false;
			while ($arElement = $rsElements->NavNext(false))
			{
				$arElement = htmlspecialcharsex($arElement);
				$arElements[] = $arElement;
				$arDocsID[] = $arElement["ID"];
			}

			$arSMElements = array();
			$rsElements = SMDocs::getList(array(), array("DOC_ID" => $arDocsID));
			while ($arElement = $rsElements->Fetch())
			{
				$doc_id = $arElement["DOC_ID"];
				unset($arElement["ID"], $arElement["DOC_ID"]);
				$arSMElements[$doc_id] = $arElement;
			}
			foreach ($arElements as $keyElement => $arElement) 
				if(!empty($arSMElements[$arElement["ID"]]))
					$arElements[$keyElement] = array_merge($arElement, $arSMElements[$arElement["ID"]]);

			$arDocElements = array();

			$docID = array();
			$arDocsElementIDs = array();
			foreach($arElements as $arElement)
				$docID[] = $arElement["ID"];
			$dbDocElement = CCatalogStoreDocsElement::getList(array(), array("DOC_ID" => $docID/*, "STORE_TO" => SMShops::getUserShop()*/));
			while($arDocElement = $dbDocElement->Fetch())
			{
				$arDocElements[$arDocElement["DOC_ID"]][] = $arDocElement;
				$arDocsElementIDs[] = $arDocElement["ID"];
			}

			$tmpDocsElements = array();
			$dbElement = SMStoreDocsElement::getList(array("ID" => "ASC"), array("DOCS_ELEMENT_ID" => $arDocsElementIDs));
			while($tmpElement = $dbElement->Fetch())
			{
				$docElementId = $tmpElement["DOCS_ELEMENT_ID"];
				unset($tmpElement["ID"], $tmpElement["DOCS_ELEMENT_ID"]);
				$tmpDocsElements[$docElementId] = $tmpElement;
			}
			foreach($arDocElements as $doc_id => $arDoc)
				foreach ($arDoc as $doc_element_id => $arDocElement) 
					if(!empty($tmpDocsElements[$arDocElement["ID"]]))
						$arDocElements[$doc_id][$doc_element_id] = array_merge($arDocElement, $tmpDocsElements[$arDocElement["ID"]]);

			foreach($arElements as $arElement)
				//if(!empty($arDocElements[$arElement["ID"]]))
				{
					$arElement["TOTAL_QUANTITY"] = 0;
					foreach ($arDocElements[$arElement["ID"]] as $arDocElement)
					{
						$arElement["TOTAL_QUANTITY"] += $arDocElement["AMOUNT"];
						$arElement["FIRST_END_DATE"] = empty($arElement["FIRST_END_DATE"]) ? $arDocElement["END_DATE"] : (!empty($arDocElement["END_DATE"]) && $arDocElement["END_DATE"] < $arElement["FIRST_END_DATE"] ? $arDocElement["END_DATE"] : $arElement["FIRST_END_DATE"]);
					}
					$arResult["ELEMENTS"][] = $arElement;
				}
		}
		else
		{
			$arResult["NO_USER"] = "Y";
		}

		$arResult["MESSAGE"] = htmlspecialcharsex($_REQUEST["strIMessage"]);

		$this->IncludeComponentTemplate();
	}
	else
	{
		$APPLICATION->AuthForm("");
	}
}
?>