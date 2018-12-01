<?
include_once("defaultTest.php");

class OverheadTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:overhead";
	private $rightChapter = "overhead";

	public static function prepareCompare(&$testData, &$resData)
	{
		DefaultTest::prepareCompare($testData, $resData);

		$testData["TOTAL_FACT"] = 0;
		$testData["TOTAL_SUMM"] = 0;
		foreach ($testData["ELEMENT"] as &$element) 
		{
			$element["PURCHASING_PRICE"] = round($element["PURCHASING_SUMM"] * 100 / (100 + $element["PURCHASING_NDS"]) / $element["DOC_AMOUNT"], 2);
			$element["NDS_VALUE"] = round($element["PURCHASING_SUMM"] * $element["PURCHASING_NDS"] / 100, 2);
			$testData["TOTAL_SUMM"] += $element["PURCHASING_SUMM"];

			$element["AMOUNT"] = round($element["AMOUNT"], 2);
			$element["SHOP_PRICE"] = round($element["SHOP_PRICE"], 2);
			$element["DOC_AMOUNT"] = round($element["DOC_AMOUNT"], 2);
			$element["PURCHASING_SUMM"] = round($element["PURCHASING_SUMM"], 2);
			$element["NDS_VALUE"] = round($element["NDS_VALUE"], 2);

			unset($element["ID"]);
			unset($element["START_DATE"]);
			unset($element["MEASURE"]);
		}
		$testData["TOTAL_SUMM"] = round($testData["TOTAL_SUMM"], 2);

		$resData["DATE_DOCUMENT"] = substr($resData["DATE_DOCUMENT"], 0, strlen("dd.mm.yyyy"));
		foreach ($resData["ELEMENT"] as &$element) 
		{
			$element["NDS_VALUE"] = round($element["NDS_VALUE"], 2);
			unset($element["ID"]);
			unset($element["START_DATE"]);
			unset($element["MEASURE"]);
		}
		$resData["TOTAL_SUMM"] = round($resData["TOTAL_SUMM"], 2);
	}
}