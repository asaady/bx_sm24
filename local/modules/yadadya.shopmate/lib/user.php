<?
namespace Yadadya\Shopmate;
use \Bitrix\Main\Localization\Loc;

use \Bitrix\Catalog,
	\Bitrix\Main\UserTable,
	\Bitrix\Main\GroupTable;

Loc::loadMessages(__FILE__);

class User
{
	function SimpleBuyerAdd($login = "")
	{
		return self::SimpleAdd("simple_buyer");
	}

	function SimpleAdd($login = "", $groups = array())
	{
		$oUser = new \CUser;

		if(empty($login))
			$login = "sample";

		if($user = UserTable::GetList(array("select" => array("ID"), "filter" => array("LOGIN" => $login)))->fetch())
		{
			if(!empty($groups))
				$oUser->SetUserGroup($user["ID"], $groups);

			return $user["ID"];
		}

		$arFields = array(
			"LOGIN" => $login,
			"EMAIL" => $login."@email.tmp",
			"ACTIVE" => "Y",
			"SITE_ID" => SITE_ID
		);

		if(empty($groups))
			$groups = explode(",", \COption::GetOptionString("main", "new_user_registration_def_group", ""));

		$arFields["GROUP_ID"] = explode(",", $def_group);

		$password_chars = array(
			"abcdefghijklnmopqrstuvwxyz",
			"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			"0123456789",
		);
		if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
			$password_chars[] = ",.<>/?;:'\"[]{}\\|`~!@#\$%^&*()-_+=";
		$arFields["PASSWORD"] = $arFields["CONFIRM_PASSWORD"] = randString(6, $password_chars);

		$arFields["LID"] = $arFields["SITE_ID"];
		return $oUser->Add($arFields);
	}

	function SimpleGroupAdd($name = "")
	{
		if(empty($name))
			$name = "sample";

		if($group = GroupTable::GetList(array("select" => array("ID"), "filter" => array("NAME" => $name)))->fetch())
			return $group["ID"];

		$result = GroupTable::Add(array("NAME" => $name));

		if($result->IsSuccess())
			return $result->getId();
	}
}