<?
include_once("defaultTest.php");

class PersonalTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	//private $componentName = "shopmate:personal";
	private $componentClass = "Personal";
	private $rightChapter = "personal";

	public static function prepareCompare(&$testData, &$resData)
	{
		DefaultTest::prepareCompare($testData, $resData);

		$resData["START_DATE"] = substr($resData["START_DATE"], 0, strlen("dd.mm.yyyy"));
		$resData["SALARY"] = round($resData["SALARY"], 2);
		$resData["RATE"] = round($resData["RATE"], 2);
		unset($testData["STORE_ID"], $resData["STORE_ID"]);
	}
}