<?
use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

trait DefaultTest
{
	//private $componentName;
	//private $rightChapter;
	//private $componentClass;
	private $testObj;
	private $testShop;
	private $testUser;
	private $testId;

	public function setUp()
	{
		initBitrixCore();
		if (empty($this->componentName))
			$this->componentName = "shopmate:default";
		$this->testObj = new Component($this->componentName);
		if (!empty($this->componentClass))
			$this->testObj->onPrepareComponentParams(["COMPONENT_CLASS" => $this->componentClass]);
	}

	public static function prepareCompare(&$testData, &$resData)
	{
		Test::prepareCompare($testData, $resData);
	}

	/**
	* @param string    $right
	*
	* @testWith        [null]
	*                  ["R"]
	*                  ["W"]
	*                  ["X"]
	*/

	public function testUserCanAccess($right)
	{
		if (!empty($this->rightChapter))
		{
			Test::setRights($this->rightChapter, $right);
			
			$result = $this->testObj->userCanRead();
			$this->assertEquals(empty($right) ? false : $right >= "R", $result);

			$result = $this->testObj->userCanEdit();
			$this->assertEquals(empty($right) ? false : $right >= "W", $result);

			$result = $this->testObj->userCanAll();
			$this->assertEquals(empty($right) ? false : $right >= "X", $result);
		}
	}

	public function testActions()
	{
		if (!empty($this->rightChapter))
			Test::setRights($this->rightChapter, "X");

		//add item
		$testData = Test::getRandItem($this->testObj);
		$res = $this->testObj->save(0, $testData);
		$this->assertTrue($res->isSuccess());
		if ($res->isSuccess())
		{
			$this->testId = $res->getId();
			$result = $this->testObj->getByID($this->testId);
			static::prepareCompare($testData, $result);
		}
		$this->assertEquals($testData, $result);

		//update item
		$testData = Test::getRandItem($this->testObj);
		$res = $this->testObj->save($this->testId, $testData);
		$this->assertTrue($res->isSuccess());
		if ($res->isSuccess())
		{
			$result = $this->testObj->getByID($this->testId);
			static::prepareCompare($testData, $result);
		}
		$this->assertEquals($testData, $result);

		//delete item
		//$res = $this->testObj->delete($this->testId);
		//$this->assertTrue($res->isSuccess());
	}
}