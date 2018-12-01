<?php
namespace Yadadya\Shopmate\Events;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Dnc
{
	//OnShopmateCProductsAdd, OnShopmateCProductsUpdate
	public function OnShopmateProductAction(\Bitrix\Main\Event $event)
	{
		$errors = array();
		$parameters = $event->getParameters();

		if($parameters["VALUE"]["ID"] > 0)
			\SMDncDB::addProduct(array("PRODUCT_ID" => $parameters["VALUE"]["ID"]));

		if(!empty($errors))
			return new \Bitrix\Main\EventResult(
				\Bitrix\Main\EventResult::ERROR,
				$errors,
				'yadadya.shopmate'
			);
		return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, null, 'yadadya.shopmate');
	}
}