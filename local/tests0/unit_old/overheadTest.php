<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Overhead,
	Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class OverheadTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:overhead";
	private $rightChapter = "overhead";

	/** 
		* @dataProvider requiredProvider
	*/
	public function testRequired($request, $res)
	{
		//echo " - Накладные. Обязательные поля.".($res ? " Все обязательные заполнены" : " Не заполнено поле " . $request["PROP"]).".<br />";
		Test::setRights("overhead", "X");
		Test::setShop();
		unset($request["PROP"]);
		$result = $this->testObj->save(0, $request);
		$this->assertEquals($res, $result->isSuccess());
		if($result->isSuccess())
			$this->testObj->delete($result->getId());
	}

	public function requiredProvider()
	{
		$cobj = new Component("shopmate:overhead");
		return Test::getRequiredProvider($cobj);
	}

	public function testCalc()
	{
		//echo " - Накладные. Рассчеты.<br />";
		Test::setRights("overhead", "X");
		Test::setShop();
		$testRequest = array();
		$requests = Test::getRequiredProvider($this->testObj);
		foreach ($requests as $request) 
			if ($request[1])
			{
				$testRequest = $request[0];
				break;
			}

		$prodBefore = ProductsTest::getTestItem();

		$testRequest["ELEMENT"] = array(
			array(
				"ELEMENT_ID" => $prodBefore["ID"],
				"PURCHASING_PRICE" => 0,
				"AMOUNT" => 10,
				"SHOP_PRICE" => $prodBefore["PRICE"],
				"DOC_AMOUNT" => 10,
				"PURCHASING_NDS" => 18,
				"PURCHASING_SUMM" => 1000,
			)
		);
		$testRequest["TOTAL_SUMM"] = 0;

		$testItem = array();
		$result = $this->testObj->save(0, $testRequest);
		if($result->isSuccess())
			$testItem = $this->testObj->getByID($result->getId());

		$testRequest["DATE_DOCUMENT"] = new \Bitrix\Main\Type\DateTime($testRequest["DATE_DOCUMENT"]);
		foreach ($testRequest["ELEMENT"] as $key => $element) 
		{
			$testRequest["ELEMENT"][$key]["PURCHASING_PRICE"] = round($element["PURCHASING_SUMM"] * 100 / (100 + $element["PURCHASING_NDS"]) / $element["DOC_AMOUNT"], 2);
			$testRequest["TOTAL_SUMM"] += $element["PURCHASING_SUMM"];
		}

		foreach ($testItem as $key => $item) 
			if(!array_key_exists($key, $testRequest))
				unset($testItem[$key]);
			elseif($key == "ELEMENT")
			{
				foreach ($item as $ikey => $element) 
					foreach ($element as $ekey => $value) 
						if(!array_key_exists($ekey, $testRequest[$key][$ikey]))
							unset($testItem[$key][$ikey][$ekey]);
			}

		if($testItem["ID"] > 0)
			$this->testObj->delete($testItem["ID"]);

		$this->assertEquals($testRequest, $testItem);
	}

	public function testProduct()
	{
		//echo " - Накладные. Изменения в товаре.<br />";
		$testRequest = array();
		$requests = Test::getRequiredProvider($this->testObj);
		foreach ($requests as $request) 
			if ($request[1])
			{
				$testRequest = $request[0];
				break;
			}

		Test::setRights("overhead", "X");

		$prodBefore = ProductsTest::getTestItem();

		$amount_dif = 10;
		$nds = 18;
		$shop_price_dif = 10;
		$purchase_price_dif = 5;

		$testRequest["ELEMENT"] = array(
			array(
				"ELEMENT_ID" => $prodBefore["ID"],
				"PURCHASING_PRICE" => 0,
				"AMOUNT" => $amount_dif,
				"SHOP_PRICE" => $prodBefore["PRICE"] + $shop_price_dif,
				"DOC_AMOUNT" => 10,
				"PURCHASING_NDS" => $nds,
				"PURCHASING_SUMM" => round(($prodBefore["PURCHASING_PRICE"] + $purchase_price_dif) * $amount_dif * ($nds + 100) / 100, 2),
			)
		);

		$result = $this->testObj->save(0, $testRequest);

		$prodAfter = ProductsTest::getTestItem();

		$prodBefore["PRICE"] = (float) $prodBefore["PRICE"] + $shop_price_dif;
		$prodBefore["PURCHASING_PRICE"] = (float) $prodBefore["PURCHASING_PRICE"] + $purchase_price_dif;
		$prodBefore["AMOUNT"] = (float) $prodBefore["AMOUNT"] + $amount_dif;

		if($testItem["ID"] > 0)
			$this->testObj->delete($testItem["ID"]);

		$this->assertEquals($prodBefore, $prodAfter);
	}

	public function testFinanceLog()
	{
		//echo " - Накладные. Сохранение в финансах.<br />";
		$testRequest = array();
		$requests = Test::getRequiredProvider($this->testObj);
		foreach ($requests as $request) 
			if ($request[1])
			{
				$testRequest = $request[0];
				break;
			}

		Test::setRights("overhead", "X");

		$prodBefore = ProductsTest::getTestItem();

		$testRequest["ELEMENT"] = array(
			array(
				"ELEMENT_ID" => $prodBefore["ID"],
				"PURCHASING_PRICE" => 0,
				"AMOUNT" => 10,
				"SHOP_PRICE" => $prodBefore["PRICE"],
				"DOC_AMOUNT" => 10,
				"PURCHASING_NDS" => 18,
				"PURCHASING_SUMM" => 1000,
			)
		);
		$testRequest["TOTAL_SUMM"] = 0;
		$testRequest["TOTAL_FACT"] = rand(0, 1000);

		$testItem = array();
		$result = $this->testObj->save(0, $testRequest);
		if($result->isSuccess())
			$testItem = $this->testObj->getByID($result->getId());

		$testRequest["DATE_DOCUMENT"] = new \Bitrix\Main\Type\DateTime($testRequest["DATE_DOCUMENT"]);
		foreach ($testRequest["ELEMENT"] as $key => $element) 
		{
			$testRequest["ELEMENT"][$key]["PURCHASING_PRICE"] = round($element["PURCHASING_SUMM"] * 100 / (100 + $element["PURCHASING_NDS"]) / $element["DOC_AMOUNT"], 2);
			$testRequest["TOTAL_SUMM"] += $element["PURCHASING_SUMM"];
		}


		if(!empty($testItem))
		{
			$res = \Yadadya\Shopmate\FinanceLog::getLog(array(
				"filter" => array(
					"STORE_ID" => Test::getShop(),
					"TRANSACTION" => "overhead",
					"ITEM_ID" => $testItem["ID"],
				)
			));
			while ($log = $res->fetch())
				if($log["TYPE"] == "OUT")
				{
					if($log["CREDIT"] == "N")
						$this->assertEquals($log["PRICE"], $testItem["TOTAL_FACT"]);
					else
						$this->assertEquals($log["PRICE"], $testItem["TOTAL_SUMM"] - $testItem["TOTAL_FACT"]);
				}
		}

		if($testItem["ID"] > 0)
			$this->testObj->delete($testItem["ID"]);
	}

	public function testFinanceReport()
	{
		//echo " - Накладные. Изменения в финансовых отчетах.<br />";
		$testRequest = array();
		$requests = Test::getRequiredProvider($this->testObj);
		foreach ($requests as $request) 
			if ($request[1])
			{
				$testRequest = $request[0];
				break;
			}

		Test::setRights("overhead", "X");

		$testProd = ProductsTest::getTestItem();

		$testShop = Test::getShop();

		$amount_dif = 10;
		$nds = 18;
		$shop_price_dif = 10;
		$purchase_price_dif = 5;

		$testRequest["ELEMENT"] = array(
			array(
				"ELEMENT_ID" => $testProd["ID"],
				"PURCHASING_PRICE" => 0,
				"AMOUNT" => $amount_dif,
				"SHOP_PRICE" => $testProd["PRICE"] + $shop_price_dif,
				"DOC_AMOUNT" => 10,
				"PURCHASING_NDS" => $nds,
				"PURCHASING_SUMM" => round(($testProd["PURCHASING_PRICE"] + $purchase_price_dif) * $amount_dif * ($nds + 100) / 100, 2),
			)
		);

		\Yadadya\Shopmate\FinanceReport::cron($testShop, array(), array("PRODUCT_ID" => $testProd["ID"]));
		$reportBefore = \Yadadya\Shopmate\FinanceReport::getElements(array("select" => array("ID", "NAME", "PURCHASE", "PURCHASE_AMOUNT"), "filter" => array("ID" => $testProd["ID"])))->fetch();

		$avgPriceBefore = array();
		$res = \Yadadya\Shopmate\FinanceProd::getAvgPriceList(array("PRODUCT_ID" => $testProd["ID"]));
		while ($avgPriceBefore[] = $res->fetch());

		$result = $this->testObj->save(0, $testRequest);

		\Yadadya\Shopmate\FinanceReport::cron($testShop, array(), array("PRODUCT_ID" => $testProd["ID"]));
		$reportAfter = \Yadadya\Shopmate\FinanceReport::getElements(array("select" => array("ID", "NAME", "PURCHASE", "PURCHASE_AMOUNT"), "filter" => array("ID" => $testProd["ID"])))->fetch();

		$avgPriceAfter = array();
		$res = \Yadadya\Shopmate\FinanceProd::getAvgPriceList(array("PRODUCT_ID" => $testProd["ID"]));
		while ($avgPriceAfter[] = $res->fetch());

		$reportBefore["PURCHASE_AMOUNT"] += $amount_dif;
		$reportBefore["PURCHASE"] += ($testProd["PURCHASING_PRICE"] + $purchase_price_dif) * $amount_dif;

		if($testItem["ID"] > 0)
			$this->testObj->delete($testItem["ID"]);

		$this->assertEquals($reportBefore, $reportAfter);
		$this->assertNotEquals($avgPriceBefore, $avgPriceAfter);
	}
}