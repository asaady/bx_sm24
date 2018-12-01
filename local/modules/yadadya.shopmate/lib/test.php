<?
namespace Yadadya\Shopmate;
use Bitrix\Main\Localization\Loc,
	Yadadya\Shopmate\User,
	Yadadya\Shopmate\Shops,
	Yadadya\Shopmate\Rights;

use Bitrix\Catalog;

Loc::loadMessages(__FILE__);

class Test
{
	public function getUser($groups = array())
	{
		$groups = (array) $groups;
		return User::SimpleAdd("test", $groups);
	}

	public function Authorize($groups = array())
	{
		global $USER;
		$user = Test::getUser($groups);
		//$USER->Logout();
		$USER->Authorize($user);
		return $user;
	}

	public function getGroup()
	{
		return User::SimpleGroupAdd("test");
	}

	public function getShop()
	{
		return Shops::SimpleAdd("test");
	}

	public function getShopGroup($shop = 0)
	{
		return Shops::getGroupByStore($shop);
	}

	public function setShop()
	{
		$shop = Test::getShop();
		Shops::setUserShopParams($shop);
		return $shop;
	}

	public function setGroupsRights($chapter = "", $groups = array(), $rights = array())
	{
		if(!empty($chapter) && !empty($groups))
		{
			$groups = (array) $groups;
			$rights = empty($rights) ? array() : (array) $rights;

			$permissions = Rights::GetPermission($chapter);
			foreach ($permissions as $permission => $pgroups) 
				foreach ($pgroups as $key => $pgroup) 
					if(!in_array($permission, $rights) && in_array($pgroup, $groups))
						unset($permissions[$permission][$key]);

			foreach ($rights as $right) 
				if(is_array($permissions[$right]))
				{
					foreach ($groups as $group) 
						if(!in_array($group, $permissions[$right]))
							$permissions[$right][] = $group;
				}
				else
					$permissions[$right] = $groups;

			foreach ($permissions as $permission => $pgroups) 
				Rights::SetPermission($chapter, $permission, $pgroups);
		}
	}

	public function setRightsOld($chapter, $right)
	{
		$group = Test::getGroup();
		$shop = Test::getShop();
		$shopGroup = Test::getShopGroup($shop);
		Test::setGroupsRights($chapter, $group, $right);
		$user = Test::Authorize(array($group, $shopGroup));
		Shops::setUserShopParams($shop);
		return $user;
	}

	public function getRequiredProvider($cobj)
	{
		$test_requests = array();

		$propList = $cobj->getPropList();
		$request = array();
		foreach ($cobj->getPropList() as $prop => $params) 
			if ($params["REQUIRED"] == "Y")
			{
				switch ($params["USER_TYPE"]) {
					case "Date":
						$test_val = date("d.m.Y H:i:s");
						break;
					
					default:
						$test_val = "test";
						break;
				}

				$request[$prop] = $test_val;
			}

		foreach ($request as $prop => $test_val) 
		{
			$test_request = $request;
			unset($test_request[$prop]);
			$test_request["PROP"] = $prop;
			$test_requests[] = array($test_request, false);
		}
		$test_requests[] = array($request, true);

		return $test_requests;
	}

	public function setRights($ch, $r)
	{
		$group = Test::getGroup();
		$shop = Test::getShop();
		$shopGroup = Test::getShopGroup($shop);
		//$user = Test::getUser(array($group, $shopGroup));
		$user = Test::Authorize(array($group, $shopGroup));
		Shops::setUserShopParams($shop);

		/*global $USER;
		$USER->SetParam("SM_PERM_".strtoupper($ch), $r);*/

		if (
			$chapter = Components\SettingsChapter::getList([
				"select" => ["ID"],
				"filter" => ["STRING_ID" => $ch]
			])->fetch())
		{
			$rights = [
				"NAME" => "test",
				"USER_ID" => [$user],
				"CHAPTERS" => [array_merge(["ID" => $chapter["ID"]], empty($r) ? [] : Components\SettingsGroup::charToRights($r))]
			];

			$res = Components\SettingsGroup::getList([
				"select" => ["ID"],
				"filter" => ["NAME" => "test"]
			]);
			if ($row = $res->fetch())
				Components\SettingsGroup::update($row["ID"], $rights);
			else
				Components\SettingsGroup::add($rights);

			global $USER;
			$USER->SetParam("SM_PERM_".strtoupper($ch), "");

		}

		return $user;
	}

	public function getRandProp($propParams, $len = 0)
	{
		$prop = false;
		switch ($propParams["VERIFICATION"]) 
		{
			case 'int':

				$prop = rand(1, empty($len) ? 10000 : $len);

				break;
			
			
			case 'float':

				$prop = rand(1, empty($len) ? 10000 : $len)/100;

				break;
			
			default:

				$prop = randString(empty($len) ? 10 : $len);

				break;
		}
		return $prop;
	}

	public function _getRandItem($propList, $only_required = false)
	{
		$data = [];

		foreach ($propList as $prop => $propParams) 
		{
			if (!empty($propParams["REF_ENTITY"]))
			{
				$propParams["PROPERTY_TYPE"] = "L";
				$propParams["LIST_TYPE"] = "AJAX";

			}
			switch($propParams["PROPERTY_TYPE"]) 
			{
				case "SUBLIST":
					
					$data[$prop] = self::_getRandItem($propParams["PROPERTY_LIST"], $only_required);

					break;

				case "L":

					if ($propParams["LIST_TYPE"] == "AJAX")
					{
						if (!empty($propParams["REF_ENTITY"]))
						{
							$refEntity = new $propParams["REF_ENTITY"];
							$res = $refEntity->getList([
								"runtime" => [new \Bitrix\Main\Entity\ExpressionField("RAND", "RAND()")],
								"order" => ["RAND"]
							]);
							if ($row = $res->fetch())
								$data[$prop] = $row["ID"];
						}
						else
							$data[$prop] = rand(1,10);
					}
					else
					{
						$listKeys = array_keys($propParams["ENUM"]);
						$data[$prop] = $listKeys[rand(0, count($listKeys) - 1)];
					}

					//

					break;
				

				case "T":

					$data[$prop] = Test::getRandProp($propParams);

					break;


				case "H":

					$data[$prop] = Test::getRandProp($propParams);

					break;

				case "F":

					//

					break;
				

				case "S":

				case "N":

				default:

					if ($propParams["USER_TYPE"] == "DateTime" || $propParams["USER_TYPE"] == "Date") 
						$data[$prop] = date($propParams["USER_TYPE"] == "DateTime" ? "d.m.Y H:i:s" : "d.m.Y", time());
					elseif ($propParams["VERIFICATION"] == "email") 
						$data[$prop] = randString(10)."@".randString(5).".".randString(3);
					elseif ($propParams["VERIFICATION"] == "phone") 
						$data[$prop] = randString(11, "0123456789");
					elseif ($propParams["PROPERTY_TYPE"] == "N") 
						$data[$prop] = rand(1, 100);
					else
						$data[$prop] = Test::getRandProp($propParams);

					break;
			}

			if (!empty($propParams["DEFAULT_VALUE"]))
				$data[$prop] = $propParams["DEFAULT_VALUE"];

			if ($propParams["MULTIPLE"] == "Y")
				$data[$prop] = [$data[$prop]];
		}

		return $data;
	}

	public function getRandItem($cobj, $only_required = false)
	{
		$data = [];

		$propList = $cobj->getPropList();

		$data = self::_getRandItem($propList, $only_required);

		return $data;
	}

	public function prepareCompare(&$testData, &$resData)
	{
		unset($testData["COMMENT"], $resData["COMMENT"]);
		foreach ($resData as $key => $value) 
			if(!array_key_exists($key, $testData))
				unset($resData[$key]);
			else
			{
				if ($resData[$key] instanceof \Bitrix\Main\Type\DateTime)
					$resData[$key] = $resData[$key]->toString();
				elseif (is_array($testData[$key]) && is_array($resData[$key]))
					foreach ($resData[$key] as $subkey => $subvalue) 
						if (is_array($testData[$key][$subkey]) && is_array($resData[$key][$subkey]))
							self::prepareCompare($testData[$key][$subkey], $resData[$key][$subkey]);
			}
	}
}