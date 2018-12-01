<?
include_once("defaultTest.php");

class RegisterTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:register";
	private $rightChapter = "";

	public static function prepareCompare(&$testData, &$resData)
	{
		DefaultTest::prepareCompare($testData, $resData);

		unset($testData["PASSWORD"]);
	}
}