<?php
namespace Yadadya\Shopmate\Events;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Notify
{
	//OnShopmateCUserOverheadAdd, OnShopmateCUserOverheadUpdate
	public function OnShopmateUserOverheadAction(\Bitrix\Main\Event $event)
	{
		$params = $event->getParameters();
		$data = $params["VALUE"];

		if ($data["submit"] == "act")
		{
			global $USER;
			$arNotify = array(
				"DATE" => new \Bitrix\Main\Type\DateTime(),
				"USER_ID" => $USER->GetId(),
			);

			$arNotify["EVENT_TYPE"] = "отправил " . (!empty($data["accepted"]) ? "акт соответствия" : (!empty($data["changed"]) ? "акт расхождения" : (!empty($data["rejected"]) ? "акт отказа" : "сообщение")));

			$uo = \Yadadya\Shopmate\Components\UserOverhead::getList(array("select" => array("ID", "USER_ID", "STORE_ID"), "filter" => array("ID" => $params["VALUE"]["ID"])))->fetch();
			if ($uo["USER_ID"] == $USER->GetId())
				$arNotify["STORE_TO"] = $uo["STORE_ID"];
			else
				$arNotify["USER_TO"] = $uo["USER_ID"];


			$arNotify["ITEM_OBJECT"] = "в накладной №" . $data["NUMBER_DOCUMENT"] . " от " . $data["DATE_DOCUMENT"];

			$arNotify["ITEM_ID"] = $params["VALUE"]["ID"];

			global $APPLICATION;
			$arNotify["URL"] = $APPLICATION->GetCurPageParam("edit=Y&CODE=".$arNotify["ITEM_ID"], array_keys($_REQUEST));

			\Yadadya\Shopmate\Internals\NotifyTable::add($arNotify);
		}
	}

	//OnBeforeShopmateComponentAdd, OnBeforeShopmateComponentUpdate
	public function OnBeforeShopmateComponentAction(\Bitrix\Main\Event $event)
	{
		global $notifyBeforeValue;
		$params = $event->getParameters();
		if (!empty($params["VALUE"]["ID"]))
		{
			$itemClass = "\\Yadadya\\Shopmate\\Components\\".$params["COMPONENT"];
			$item = new $itemClass;
			$beforeValue = $item->getById($params["VALUE"]["ID"]);
			$notifyBeforeValue[$params["COMPONENT"]] = $beforeValue;
		}
		else
			$notifyBeforeValue[$params["COMPONENT"]] = [];
	}

	//OnShopmateComponentAdd, OnShopmateComponentUpdate
	public function OnShopmateComponentAction(\Bitrix\Main\Event $event)
	{
		global $notifyBeforeValue;
		$params = $event->getParameters();
		$object_name = strtolower($params["COMPONENT"]);
		if ($object_name != "notify")
		{
			global $USER;
			$arNotify = array(
				"DATE" => new \Bitrix\Main\Type\DateTime(),
				"USER_ID" => $USER->GetId(),
			);

			$eventType = $event->getEventType();
			$arNotify["EVENT_TYPE"] = stripos($eventType, "add") !== false ? "add" : (stripos($eventType, "update") !== false ? "update" : (stripos($eventType, "delete") !== false ? "delete" : $eventType));

			$arNotify["STORE_TO"] = \Yadadya\Shopmate\Shops::getUserShop();

			$params = $event->getParameters();
			$arNotify["ITEM_OBJECT"] = strtolower($params["COMPONENT"]);

			$arNotify["ITEM_ID"] = $params["VALUE"]["ID"];

			global $APPLICATION;
			$arNotify["URL"] = $APPLICATION->GetCurPageParam("edit=Y&CODE=".$arNotify["ITEM_ID"], array_keys($_REQUEST));

			global $notifyBeforeValue;
			$arNotify["BEFORE"] = serialize($notifyBeforeValue[$params["COMPONENT"]]);
			$itemClass = "\\Yadadya\\Shopmate\\Components\\".$params["COMPONENT"];
			$item = new $itemClass;
			$arNotify["AFTER"] = serialize($item->getById($params["VALUE"]["ID"]));

			\Yadadya\Shopmate\Internals\NotifyTable::add($arNotify);
		}
	}

	public function OnEGAISWaybillAdd($lastId, $arFields)
	{
		global $USER;
		$arNotify = array(
			"DATE" => new \Bitrix\Main\Type\DateTime(),
			"USER_ID" => $USER->GetId(),
		);

		if($arFields["EVENT_TYPE"] == "") $arFields["EVENT_TYPE"] = "add";
		$eventType = $arFields["EVENT_TYPE"];
		$arNotify["EVENT_TYPE"] = stripos($eventType, "add") !== false ? "add" : (stripos($eventType, "update") !== false ? "update" : (stripos($eventType, "delete") !== false ? "delete" : $eventType));

		$arNotify["STORE_TO"] = \Yadadya\Shopmate\Shops::getUserShop();

		$params = '';//$event->getParameters();
		if($arFields["COMPONENT"] == "") $arFields["COMPONENT"] = "egais";
		$arNotify["ITEM_OBJECT"] = strtolower($arFields["COMPONENT"]);

		$arNotify["ITEM_ID"] = $lastId;

		$arNotify["URL"] = "/products/egais/?edit=Y&CODE=". $lastId;

		$res = \Yadadya\Shopmate\Internals\NotifyTable::add($arNotify);
	}

	//OnShopmateCOverheadAdd
	public static function OnShopmateOverheadAdd(\Bitrix\Main\Event $event)
	{
		$prod = new \Yadadya\Shopmate\Components\Products;
		$errors = array();
		$parameters = $event->getParameters();
		//$value = $parameters["VALUE"];
		$value = \Yadadya\Shopmate\Components\Overhead::getById($parameters["VALUE"]["ID"]);

		global $USER;

		if (is_array($value["ELEMENT"]))
			foreach ($value["ELEMENT"] as $element)
				if ($element["ELEMENT_ID"] > 0)
				{
					$prodBefore = $prod->getByID($element["ELEMENT_ID"]);
					if ($prodBefore["SHELF_LIFE"] > 0)
					{
						$start_date = !empty($element["START_DATE"]) ? $element["START_DATE"] : new \Bitrix\Main\Type\DateTime();

						$start_date->setTime($prodBefore["SHELF_LIFE"]*24,0);
						$end_date = $start_date->toString();

						$start_date->setTime(-1*$prodBefore["SHELF_LIFE"]*0.3*24,0);
						$pre_end_date = $start_date->toString();

						$arNotify = array(
							"DATE" => new \Bitrix\Main\Type\DateTime($pre_end_date),
							"USER_ID" => $USER->GetId(),
							"EVENT_TYPE" => "напоминает, ".$end_date." заканчивается срок годности товара",
							"STORE_TO" => \Yadadya\Shopmate\Shops::getUserStore(),
							"ITEM_OBJECT" => "\"".$prodBefore["NAME"]."\"",
							"ITEM_ID" => $prodBefore["ID"],
							"URL" => "/products/?edit=Y&CODE=".$prodBefore["ID"]
						);

						$res = \Yadadya\Shopmate\Internals\NotifyTable::add($arNotify);

						$arNotify["DATE"] = new \Bitrix\Main\Type\DateTime($end_date);

						$res = \Yadadya\Shopmate\Internals\NotifyTable::add($arNotify);
					}
				}
	}
}