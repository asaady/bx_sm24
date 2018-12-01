<?
include_once("defaultTest.php");

class ContractorTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:contractors";
	private $rightChapter = "contractor";

	public static function prepareCompare(&$testData, &$resData)
	{
		DefaultTest::prepareCompare($testData, $resData);

		$resData["CONTRACT_DATE"] = substr($resData["CONTRACT_DATE"], 0, strlen("dd.mm.yyyy"));
	}
}