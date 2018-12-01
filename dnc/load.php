<?
if(!empty($_REQUEST["mask"]))
{
	$handle = __DIR__;
	if (file_exists($handle)) 
	{
		$files = scandir($handle);
		foreach($files as $file)
			if(stripos($file, $_REQUEST["mask"]) === 0)
				file_get_contents("http://".$_SERVER["SERVER_NAME"].dirname($_SERVER["SCRIPT_NAME"])."/index.php?removefile=Y&loadfile=".$file);
	}
}
?>