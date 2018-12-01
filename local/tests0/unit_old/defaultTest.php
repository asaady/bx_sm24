<?
use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

trait DefaultTest
{
	//private $componentName;
	//private $rightChapter;
	private $testObj;
	private $testShop;
	private $testUser;
	private $testId;

	public function setUp()
	{
		initBitrixCore();
		$this->testObj = new Component($this->componentName);
	}

	/** 
		* @dataProvider userCanReadProvider
	*/
	public function testUserCanRead($right, $expectedResult)
	{
		global $USER;
		Test::setRights($this->rightChapter, $right);
		$result = $this->testObj->userCanRead();

		$this->assertEquals($expectedResult, $result);
	}

	public function userCanReadProvider()
	{
		return array(
			array(null, false),
			array("R", true),
			array("W", true),
			array("X", true),
		);
	}

	/** 
		* @dataProvider userCanEditProvider
	*/
	public function testUserCanEdit($right, $expectedResult)
	{
		global $USER;
		Test::setRights($this->rightChapter, $right);
		$result = $this->testObj->userCanEdit();

		$this->assertEquals($expectedResult, $result);
	}

	public function userCanEditProvider()
	{
		return array(
			array(null, false),
			array("R", false),
			array("W", true),
			array("X", true),
		);
	}

	/** 
		* @dataProvider userCanAllProvider
	*/
	public function testUserCanAll($right, $expectedResult)
	{
		global $USER;
		Test::setRights($this->rightChapter, $right);
		$result = $this->testObj->userCanAll();

		$this->assertEquals($expectedResult, $result);
	}

	public function userCanAllProvider()
	{
		return array(
			array(null, false),
			array("R", false),
			array("W", false),
			array("X", true),
		);
	}
}