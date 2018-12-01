<?
class Altassian {

	public static function getApiUrl()
	{
		return "https://yadadya-dev.atlassian.net/wiki/rest/api";
	}

	public static function post($comand = "", $type = "", array $data = array())	
	{
		$ch = curl_init(static::getApiUrl().$comand);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
		curl_setopt($ch, CURLOPT_USERPWD, "enzeru@yadadya.com" . ":" . "da110286");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		if (!empty($data))
		{
			if ($type != "" && $type != "GET")
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
			$data_json = json_encode($data);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
		}
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}

	public static function parse($res)
	{
		return json_decode($res, true);
	}

	public static function send($comand = "", $type = "", array $data = array())
	{
		$res = static::post($comand, $type, $data);
		return static::parse($res);
	}

	public static function getContent($id = "", array $data = array())
	{
		return static::send("/content/".$id, "GET", $data);
	}

	public static function setContent($id = "", array $data = array())
	{
		return static::send("/content/".$id, "PUT", $data);
	}
}