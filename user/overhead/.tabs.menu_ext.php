<? 
$prod_dir = "/products/";
include("../.." . $prod_dir . ".tabs.menu_ext.php");
foreach ($aMenuLinks as &$aMenuLink) 
	if ($aMenuLink[1] == ".")
		$aMenuLink[1] = $prod_dir;
	elseif (strpos($aMenuLink[1], "/") !== 0)
		$aMenuLink[1] = $prod_dir . $aMenuLink[1];
?>