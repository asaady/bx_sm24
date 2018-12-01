<?php
namespace Yadadya\Shopmate\Events;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class User
{
	//OnAfterUserAuthorize
	public function saveLastAuth($arUser)
	{
		$_SESSION["SESS_AUTH"]["PREV_AUTH"] = $arUser["user_fields"]["LAST_LOGIN"];
	}
}