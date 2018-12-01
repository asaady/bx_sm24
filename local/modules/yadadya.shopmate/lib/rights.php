<?
namespace Yadadya\Shopmate;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Rights
{
	function GetModuleRightList()
	{
		$arr = array(
			'reference_id' => array('D', 'R', 'W', 'X'),
			'reference' => array(
					'[D] '.Loc::getMessage('SM_PERM_D'),
					'[R] '.Loc::getMessage('SM_PERM_R'),
					'[W] '.Loc::getMessage('SM_PERM_W'),
					'[X] '.Loc::getMessage('SM_PERM_X'),
				)
			);
		return $arr;
	}

	function GetModuleChapterList()
	{
		$arr = array(
			'reference_id' => array('settings', 'products', 'overhead', 'inventory', 'cash', 'fabrica', 'contractors', 'clients', 'personal', 'finance'),
			'reference' => array(
					Loc::getMessage('SM_CHAPTER_SETTINGS'),
					Loc::getMessage('SM_CHAPTER_PRODUCTS'),
					Loc::getMessage('SM_CHAPTER_OVERHEAD'),
					Loc::getMessage('SM_CHAPTER_INVENTORY'),
					Loc::getMessage('SM_CHAPTER_CASH'),
					Loc::getMessage('SM_CHAPTER_FABRICA'),
					Loc::getMessage('SM_CHAPTER_CONTRACTORS'),
					Loc::getMessage('SM_CHAPTER_CLIENTS'),
					Loc::getMessage('SM_CHAPTER_PERSONAL'),
					Loc::getMessage('SM_CHAPTER_FINANCE'),
				)
			);
		return $arr;
	}

	function SetPermission($chapter_id, $permission_id, $arGroups)
	{
		global $DB, $CACHE_MANAGER;

		$grp = array();
		foreach($arGroups as $group_id)
		{
			$group_id = intval($group_id);
			if($group_id)
				$grp[$group_id] = $group_id;
		}

		$DB->Query("
			delete from b_sm_permission
			where CHAPTER_ID = '".$DB->ForSQL($chapter_id)."'
			and PERMISSION_ID = '".$DB->ForSQL($permission_id)."'
		", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if(!empty($grp))
		{
			$DB->Query("
				delete from b_sm_permission
				where CHAPTER_ID = '".$DB->ForSQL($chapter_id)."'
				and GROUP_ID in (".implode(", ", $grp).")
			", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			
			$DB->Query("
				insert into b_sm_permission
				select '".$chapter_id."', '".$permission_id."', ug.ID
				from
					b_group ug
				where
					ug.ID in (".implode(", ", $grp).")
			", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		if(CACHED_b_sm_permission !== false)
			$CACHE_MANAGER->Clean("b_sm_permission");
	}

	public static function GetPermission($chapter_id, $permission_id = false)
	{
		global $DB, $CACHE_MANAGER;

		$arResult = false;
		if(CACHED_b_sm_permission !== false)
		{
			if($CACHE_MANAGER->Read(CACHED_b_sm_permission, "b_sm_permission"))
				$arResult = $CACHE_MANAGER->Get("b_sm_permission");
		}

		if($arResult === false)
		{
			$arResult = array();
			$res = $DB->Query("select CHAPTER_ID, PERMISSION_ID, GROUP_ID from b_sm_permission", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while($ar = $res->Fetch())
				$arResult[$ar["CHAPTER_ID"]][$ar["PERMISSION_ID"]][] = $ar["GROUP_ID"];

			if(CACHED_b_sm_permission !== false)
				$CACHE_MANAGER->Set("b_sm_permission", $arResult);
		}

		if($permission_id === false)
			return $arResult[$chapter_id];
		else
			return $arResult[$chapter_id][$permission_id];
	}

	public static function GetUserPermission($chapter_id = false, $user_id = false, $shop_ignore = false)
	{
		global $USER;
		$permission = false;

		if (empty($chapter_id))
			return $permission;

		if($user_id <= 0)
		{
			if($USER->IsAdmin())
				return "X";
			$user_id = $USER->GetId();
		}
		if($user_id == $USER->GetId() && $USER->IsAuthorized() && Shops::getUserShop() <= 0 && !$shop_ignore)
			return "D";

		$permission = $USER->GetParam("SM_PERM_".strtoupper($chapter_id));
		if (!empty($permission))
			return $permission;


		$obj = new \Yadadya\Shopmate\BitrixInternals\UserTable;
		$res = $obj->getList(array(
			'select' => array('ID', 'RIGHT_USER' => 'SR_USER.RIGHT', 'RIGHT_POSITION' => 'SR_POSITION.RIGHT', 'RIGHT_DEPARTMENT' => 'SR_DEPARTMENT.RIGHT'),
			'filter' => array('ID' => $user_id),
			'runtime' => array(
				'SMUSER' => new \Bitrix\Main\Entity\ReferenceField(
					'SMUSER',
					'Yadadya\Shopmate\Internals\User',
					array('=this.ID' => 'ref.USER_ID'),
					array('join_type' => 'LEFT')
				),
				'SCHAPTER' => new \Bitrix\Main\Entity\ReferenceField(
					'SCHAPTER',
					'Yadadya\Shopmate\Internals\SettingsChapter',
					array('ref.STRING_ID' => new \Bitrix\Main\DB\SqlExpression('?s', $chapter_id)),
					array('join_type' => 'LEFT')
				),
				'SGI_USER' => new \Bitrix\Main\Entity\ReferenceField(
					'SGI_USER',
					'Yadadya\Shopmate\Internals\SettingsGroupItem',
					array(
						'ref.ITEM_ID' => 'this.ID', 
						'ref.ITEM_TYPE' => new \Bitrix\Main\DB\SqlExpression('?s', 'USER')
					),
					array('join_type' => 'LEFT')
				),
				'SR_USER' => new \Bitrix\Main\Entity\ReferenceField(
					'SR_USER',
					'Yadadya\Shopmate\Internals\SettingsRight',
					array(
						'ref.GROUP_ID' => joinSqlExpression($obj, 'SGI_USER.GROUP_ID'),
						'ref.CHAPTER_ID' => joinSqlExpression($obj, 'SCHAPTER.ID')
					),
					array('join_type' => 'LEFT')
				),
				'SGI_POSITION' => new \Bitrix\Main\Entity\ReferenceField(
					'SGI_POSITION',
					'Yadadya\Shopmate\Internals\SettingsGroupItem',
					array(
						'ref.ITEM_ID' => joinSqlExpression($obj, 'SMUSER.POSITION_ID'),
						'ref.ITEM_TYPE' => new \Bitrix\Main\DB\SqlExpression('?s', 'POSITION')
					),
					array('join_type' => 'LEFT')
				),
				'SR_POSITION' => new \Bitrix\Main\Entity\ReferenceField(
					'SR_POSITION',
					'Yadadya\Shopmate\Internals\SettingsRight',
					array(
						'ref.GROUP_ID' => joinSqlExpression($obj, 'SGI_POSITION.GROUP_ID'),
						'ref.CHAPTER_ID' => joinSqlExpression($obj, 'SCHAPTER.ID')
					),
					array('join_type' => 'LEFT')
				),
				'SGI_DEPARTMENT' => new \Bitrix\Main\Entity\ReferenceField(
					'SGI_DEPARTMENT',
					'Yadadya\Shopmate\Internals\SettingsGroupItem',
					array(
						'ref.ITEM_ID' => joinSqlExpression($obj, 'SMUSER.DEPARTMENT_ID'),
						'ref.ITEM_TYPE' => new \Bitrix\Main\DB\SqlExpression('?s', 'DEPARTMENT')
					),
					array('join_type' => 'LEFT')
				),
				'SR_DEPARTMENT' => new \Bitrix\Main\Entity\ReferenceField(
					'SR_DEPARTMENT',
					'Yadadya\Shopmate\Internals\SettingsRight',
					array(
						'ref.GROUP_ID' => joinSqlExpression($obj, 'SGI_DEPARTMENT.GROUP_ID'),
						'ref.CHAPTER_ID' => joinSqlExpression($obj, 'SCHAPTER.ID')
					),
					array('join_type' => 'LEFT')
				),
			)
		));
		if ($row = $res->fetch())
		{
			if (!empty($row["RIGHT_USER"]))
				$permission = $row["RIGHT_USER"];
			elseif (!empty($row["RIGHT_POSITION"]))
				$permission = $row["RIGHT_POSITION"];
			elseif (!empty($row["RIGHT_DEPARTMENT"]))
				$permission = $row["RIGHT_DEPARTMENT"];
		}

		/*if (empty($permission)) //old logic
		{
			$arGroups = array();
			$res = \CUser::GetUserGroupList($user_id);
			while ($arGroup = $res->Fetch())
				$arGroups[] = $arGroup["GROUP_ID"];

			$arPerms = self::GetPermission($chapter_id);
			ksort($arPerms);
			foreach($arPerms as $perm_id => $groups)
			{
				foreach ($groups as $group) 
				{
					if(in_array($group, $arGroups))
					{
						$permission = $perm_id;
						break;
					}
				}
				if($permission != false) break;
			}
		}*/

		$USER->SetParam("SM_PERM_".strtoupper($chapter_id), $permission);
		
		return $permission;
	}

	function GetModuleGlobalGroupsList()
	{
		$arr = array(
			'reference_id' => array('clients', 'personal', 'contractors'),
			'reference' => array(
					Loc::getMessage('SM_GGROUP_CLIENTS'),
					Loc::getMessage('SM_GGROUP_PERSONAL'),
					Loc::getMessage('SM_GGROUP_CONTRACTORS'),
				)
			);
		return $arr;
	}

	function GetModulePersonalGroupsList()
	{
		$arr = array(
			'reference_id' => array('shop', 'department', 'position'),
			'reference' => array(
					Loc::getMessage('SM_PERSONAL_SHOP'),
					Loc::getMessage('SM_PERSONAL_DEPARTMENT'),
					Loc::getMessage('SM_PERSONAL_POSITION'),
				)
			);
		return $arr;
	}

	public static function SetGlobalGroups($group_id = "", $ggroups = array(), $group_title = "")
	{
		return \COption::SetOptionString("yadadya.shopmate", "global_group_".$group_id, implode(",", (array) $ggroups), $group_title);
	}

	public static function GetGlobalGroups($group_id = "")
	{
		return explode(",", \COption::GetOptionString("yadadya.shopmate", "global_group_".$group_id));
	}

	public static function SetPersonalGroups($group_id = "", $ggroups = array(), $group_title = "")
	{
		return \COption::SetOptionString("yadadya.shopmate", "personal_group_".$group_id, implode(",", (array) $ggroups), $group_title);
	}

	public static function GetPersonalGroups($group_id = "")
	{
		$arPersonalGroups = array();
		if(empty($group_id))
		{
			$tmpGroups = self::GetModulePersonalGroupsList();
			foreach($tmpGroups["reference_id"] as $group_id)
				$arPersonalGroups = array_merge($arPersonalGroups, explode(",", \COption::GetOptionString("yadadya.shopmate", "personal_group_".$group_id)));
		}
		else
			$arPersonalGroups = explode(",", \COption::GetOptionString("yadadya.shopmate", "personal_group_".$group_id));
		if(empty($group_id) || $group_id == "shop")
		{
			$arPersonalGroups = array_merge($arPersonalGroups, \SMShops::getGroups());
		}
		if(empty($group_id) || $group_id == "discount")
		{
			$arPersonalGroups = array_merge($arPersonalGroups, \SMDiscount::getGroups());
		}
		return $arPersonalGroups;
	}

	public static function setDefaults($store_id = array())
	{
		$store_id = empty($store_id) ? \Yadadya\Shopmate\Shops::getStores(true) : (array) $store_id;

		$isset_store_id = array();
		$res = \Yadadya\Shopmate\Internals\SettingsGroupTable::getList(array(
			"select" => array("STORE_ID"), 
			"filter" => array("STORE_ID" => $store_id),
			"group" => array("STORE_ID")
		));
		while ($row = $res->fetch()) 
			$isset_store_id[] = $row["STORE_ID"];

		$tmp_store_id = $store_id;
		$store_id = array();
		foreach ($tmp_store_id as $s_id) 
			if (!in_array($s_id, $isset_store_id))
				$store_id[] = $s_id;

		if (empty($store_id))
			return ;

		$chapters = array();
		$res = \Yadadya\Shopmate\Internals\SettingsChapterTable::getList(array("select" => array("ID", "NAME", "STRING_ID", "PARENT_STRING_ID" => "PARENT.STRING_ID")));
		while ($row = $res->fetch())
			$chapters[] = $row;

		$rGroups = array(
			"master" => array(
				"NAME" => "Владелец"
			),
			"accountant" => array(
				"NAME" => "Бухгалтер"
			),
			"cashier" => array(
				"NAME" => "Кассир"
			),
		);

		$rGroupChapters = array(
			"accountant" => array("products", "finance", "fabrica", "contractor", "client", "personal"),
			"cashier" => array("cash"),
		);

		foreach ($chapters as $chapter) 
		{
			$rGroups["master"]["CHAPTERS"][] = array(
				"CHAPTER_ID" => $chapter["ID"],
				"RIGHT" => "X",
			);

			foreach ($rGroupChapters as $ch_s_id => $gr_chs) 
				if (in_array($chapter["STRING_ID"], $gr_chs) || in_array($chapter["PARENT_STRING_ID"], $gr_chs))
					$rGroups[$ch_s_id]["CHAPTERS"][] = array(
						"CHAPTER_ID" => $chapter["ID"],
						"RIGHT" => "X",
					);
		}

		$sgroup = new \Yadadya\Shopmate\Internals\SettingsGroupTable;
		$sright = new \Yadadya\Shopmate\Internals\SettingsRightTable;
		foreach ($store_id as $s_id)
			foreach ($rGroups as $rGroup) 
			{
				$gres = $sgroup->add(array(
					"PARENT_ID" => false,
					"STORE_ID" => $s_id,
					"NAME" => $rGroup["NAME"],
				));
				if ($gres->isSuccess())
				{
					$group_id = $gres->getId();
					foreach ($rGroup["CHAPTERS"] as $rchapter) 
					{
						$rchapter["GROUP_ID"] = $group_id;
						$rres = $sright->add($rchapter);
					}
				}
			}
	}
}