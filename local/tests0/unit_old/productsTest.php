<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Products,
	Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class ProductsTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:products";
	private $rightChapter = "products";

	protected function getId()
	{
		if($this->testId <= 0)
		{
			$res = $this->testObj->getList(array(
				"select" => array("ID"),
				"filter" => array("XML_ID" => "test")
			));
			if($row = $res->fetch())
			{
				$this->testId = $row["ID"];
				while ($row = $res->fetch())
					CIBlockElement::Delete($row["ID"]);
			}
			else
			{
				$res = $this->testObj->save(0, array("NAME" => "test"));
				if($res->isSuccess())
				{
					$this->testId = $res->getId();

					$el = new \CIBlockElement;
					$el->Update($this->testId, array("XML_ID" => "test"));
				}
			}
		}
		
		return $this->testId;
	}

	public static function getTestItem()
	{
		$test = new self();
		$test->setUp();
		return $test->testObj->getById($test->getId());
	}

	public function testUpdate()
	{
		// echo " - Товары. Редактирование полей.<br />";
		Test::Authorize(1); //admin group
		Test::setShop();
		$id = $this->getId();
		$set = array(
			"IBLOCK_SECTION" => "",
			"NAME" => "test",
			"BARCODE" => array("testbc1", "testbc2"),
			"MEASURE" => "0",
			"AMOUNT" => "10",
			"PURCHASING_PRICE" => "11",
			"PRICE" => "12.00",
			"SHELF_LIFE" => "13",
			"DNC_TYPE_CODE" => "0",
			"ALCCODE" => array("testac1", "testac2"),
			"DETAIL_TEXT" => "test's text",
			"COMMENT" => "test update",
		);
		$res = $this->testObj->save($id, $set);
		if($row = $res->isSuccess())
		{
			$get = $this->testObj->getByID($id);
			$get["IBLOCK_SECTION"] = $get["IBLOCK_SECTION_ID"];
			foreach ($get as $key => $value) 
				if(!array_key_exists($key, $set))
					unset($get[$key]);
		}
		unset($set["COMMENT"]);

		$this->assertEquals($set, $get);
	}

	public function testUpdateBase()
	{
		// echo " - Товары. Редактирование полей общих товаров.<br />";
		$user = Test::setRights("products", "W");
		$id = $this->getId();
		$el = new \CIBlockElement;
		$el->Update($id, array("CREATED_BY" => 1));
		$set = array(
			"IBLOCK_SECTION" => "",
			"NAME" => "test2",
			"BARCODE" => array("testbc12", "testbc22"),
			"MEASURE" => "0",
			"AMOUNT" => "12",
			"PURCHASING_PRICE" => "13",
			"PRICE" => "14.00",
			"SHELF_LIFE" => "15",
			"DNC_TYPE_CODE" => "1",
			"ALCCODE" => array("testac12", "testac22"),
			"DETAIL_TEXT" => "test's text 2",
			"COMMENT" => "test update base",
		);
		$res = $this->testObj->save($id, $set);
		if($row = $res->isSuccess())
		{
			$get = $this->testObj->getByID($id);
			$get["IBLOCK_SECTION"] = $get["IBLOCK_SECTION_ID"];
			foreach ($get as $key => $value) 
				if(!array_key_exists($key, $set))
					unset($get[$key]);
		}
		unset($set["COMMENT"]);

		$this->assertNotEquals($set, $get);

		$fields = $this->testObj->getProps();
		foreach ($fields as $field => $param) 
			if($param["DISABLED"] == "Y")
				unset($set[$field], $get[$field]);

		$this->assertEquals($set, $get);
	}

	public function testUpdateStore()
	{
		// echo " - Товары. Редактирование полей товаров магазина.<br />";
		$user = Test::setRights("products", "W");
		$id = $this->getId();
		$el = new \CIBlockElement;
		$el->Update($id, array("CREATED_BY" => $user));
		$set = array(
			"IBLOCK_SECTION" => "",
			"NAME" => "test3",
			"BARCODE" => array("testbc13", "testbc23"),
			"MEASURE" => "0",
			"AMOUNT" => "13",
			"PURCHASING_PRICE" => "14",
			"PRICE" => "15.00",
			"SHELF_LIFE" => "16",
			"DNC_TYPE_CODE" => "0",
			"ALCCODE" => array("testac13", "testac23"),
			"DETAIL_TEXT" => "test's text 3",
			"COMMENT" => "test update Store",
		);
		$res = $this->testObj->save($id, $set);
		if($row = $res->isSuccess())
		{
			$get = $this->testObj->getByID($id);
			$get["IBLOCK_SECTION"] = $get["IBLOCK_SECTION_ID"];
			foreach ($get as $key => $value) 
				if(!array_key_exists($key, $set))
					unset($get[$key]);
		}
		unset($set["COMMENT"]);

		$this->assertEquals($set, $get);
	}
}
//http://apigen.juzna.cz/doc/sebastianbergmann/phpunit/source-class-PHPUnit_Framework_TestCase.html#319-336
//https://phpunit.de/manual/current/en/index.html