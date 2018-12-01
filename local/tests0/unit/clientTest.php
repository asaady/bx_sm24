<?
include_once("defaultTest.php");

class ClientTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:client";
	private $rightChapter = "client";

	public static function prepareCompare(&$testData, &$resData)
	{
		DefaultTest::prepareCompare($testData, $resData);

		$testData["GROUP_ID_DISCOUNT"] = empty($testData["GROUP_ID_DISCOUNT"]) ? [] : (array) $testData["GROUP_ID_DISCOUNT"];
		$testData["GROUP_ID_PRICELIST"] = empty($testData["GROUP_ID_PRICELIST"]) ? [] : (array) $testData["GROUP_ID_PRICELIST"];

		$resData["CONTRACT_DATE"] = substr($resData["CONTRACT_DATE"], 0, strlen("dd.mm.yyyy"));
	}
}