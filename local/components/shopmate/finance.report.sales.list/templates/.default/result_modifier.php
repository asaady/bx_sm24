<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Yadadya\Shopmate\Components\Template;
use \Yadadya\Shopmate\BitrixInternals;
use Bitrix\Main\Entity;

foreach ($arResult["ITEMS"] as $key => $item) 
{
	$item["SALE_QUANTITY"] = Template::QuantityFormat($item["SALE_QUANTITY"]);
	$item["SALE"] = Template::PriceFormat($item["SALE"]);
	$item["PROFIT"] = Template::PriceFormat($item["PROFIT"]);
	$item["PURCHASING_PRICE"] = Template::PriceFormat($item["PURCHASING_PRICE"]);
	$item["STORE_PRICE"] = Template::PriceFormat($item["STORE_PRICE"]);
	$item["SUMMARY_PRICE"] = Template::PriceFormat($item["SUMMARY_PRICE"]);
	$arResult["ITEMS"][$key] = $item;
}
unset($arResult["SORTS"]["ORDER_ID"]);

switch ($arResult["LIST_TYPE"]) 
{
	case "SECTIONS":
	case "ELEMENTS":
		
		if (!empty($_REQUEST["SECTION"]))
		{
			$arResult["BACK_URL"] = BitrixInternals\SectionTable::getList(array(
				"select" => array("ID", "NAME", "PARENT_ID" => "PARENT.ID", "PARENT_NAME" => "PARENT.NAME"),
				"filter" => array("ID" => $_REQUEST["SECTION"]),
				"runtime" => array("PARENT" => new Entity\ReferenceField(
					'PARENT',
					'Yadadya\Shopmate\BitrixInternals\Section',
					array('=ref.ID' => 'this.IBLOCK_SECTION_ID'),
					array('join_type' => 'LEFT')
				))
			))->fetch();
			$arResult["BACK_URL"]["PARENT_URL"] = $APPLICATION->GetCurPageParam("SECTION=".$arResult["BACK_URL"]["PARENT_ID"], array("SECTION"));
		}

		break;

	case "ORDER":
		
		if (!empty($_REQUEST["ELEMENT"]) && !empty($_REQUEST["ORDER"]))
		{
			$arResult["BACK_URL"] = BitrixInternals\OrderTable::getList(array(
				"select" => array("ID", "NAME" => "ACCOUNT_NUMBER", "PARENT_ID" => "PARENT.ID", "PARENT_NAME" => "PARENT.NAME"),
				"filter" => array("ID" => $_REQUEST["ORDER"]),
				"runtime" => array("PARENT" => new Entity\ReferenceField(
					'PARENT',
					'Yadadya\Shopmate\BitrixInternals\Element',
					array('=ref.ID' => new \Bitrix\Main\DB\SqlExpression(intval($_REQUEST["ELEMENT"]))),
					array('join_type' => 'LEFT')
				))
			))->fetch();
			$arResult["BACK_URL"]["PARENT_URL"] = $APPLICATION->GetCurPageParam("ELEMENT=".$arResult["BACK_URL"]["PARENT_ID"], array("SECTION", "ELEMENT", "ORDER"));
		}

		break;

	case "BASKET":
		
		if (!empty($_REQUEST["ELEMENT"]))
		{
			$arResult["BACK_URL"] = BitrixInternals\ElementTable::getList(array(
				"select" => array("ID", "NAME", "PARENT_ID" => "PARENT.ID", "PARENT_NAME" => "PARENT.NAME"),
				"filter" => array("ID" => $_REQUEST["ELEMENT"]),
				"runtime" => array("PARENT" => new Entity\ReferenceField(
					'PARENT',
					'Yadadya\Shopmate\BitrixInternals\Section',
					array('=ref.ID' => 'this.IBLOCK_SECTION_ID'),
					array('join_type' => 'LEFT')
				))
			))->fetch();
			$arResult["BACK_URL"]["PARENT_URL"] = $APPLICATION->GetCurPageParam("SECTION=".$arResult["BACK_URL"]["PARENT_ID"], array("SECTION", "ELEMENT"));
		}

		break;
}

if (empty($arResult["BACK_URL"]["PARENT_NAME"]))
	$arResult["BACK_URL"]["PARENT_NAME"] = "Список групп";
if (empty($arResult["BACK_URL"]["NAME"]))
	$arResult["BACK_URL"]["NAME"] = "Список групп";