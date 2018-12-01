<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\UserOverhead,
	Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class UserOverheadTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:user.overhead";
	private $rightChapter = "overhead";

	public function testXml()
	{
		Test::setRights($this->rightChapter, "X");
		$cobj = new Component($this->componentName);
		$request = end(Test::getRequiredProvider($cobj))[0];
		$request["STORE_ID"] = Test::getShop();

		$_FILES["XML"] = \CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]. "/local/tests/unit/useroverhead/test.txt");
		$result = $this->testObj->save(0, $request);
		$this->assertEquals(false, $result->isSuccess());

		$_FILES["XML"] = \CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]. "/local/tests/unit/useroverhead/test.xml");
		$result = $this->testObj->save(0, $request);
		$this->assertEquals(true, $result->isSuccess());

		if ($result->isSuccess())
			$this->testObj->delete($result->getId());
	}

	public function testVisibleShop()
	{
		Test::setRights($this->rightChapter, "X");
		$cobj = new Component($this->componentName);
		$request = end(Test::getRequiredProvider($cobj))[0];
		$request["STORE_ID"] = Test::getShop();
		$new = $this->testObj->save(0, $request);

		Test::setShop();

		$result = $this->testObj->getByID($new->getId());
		$this->assertEquals($request["STORE_ID"], $result["STORE_ID"]);

		if ($new->isSuccess())
			$this->testObj->delete($new->getId());
	}

	public function testRejected()
	{
		Test::setRights($this->rightChapter, "X");
		$cobj = new Component($this->componentName);
		$request = end(Test::getRequiredProvider($cobj))[0];
		$request["STORE_ID"] = Test::getShop();
		$_FILES["XML"] = \CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]. "/local/tests/unit/useroverhead/test.xml");
		$new = $this->testObj->save(0, $request);

		Test::setShop();

		$new = $this->testObj->save($new->getId(), array("rejected" => "Y", "DESCRIPTION" => "test"));
		$result = $this->testObj->getByID($new->getId());

		$this->assertEmpty($result["OVERHEAD_ID"]);

		if ($new->isSuccess())
			$this->testObj->delete($new->getId());
	}

	public function testAccepted()
	{
		Test::setRights($this->rightChapter, "X");
		$cobj = new Component($this->componentName);
		$request = end(Test::getRequiredProvider($cobj))[0];
		$request["STORE_ID"] = Test::getShop();
		$_FILES["XML"] = \CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]. "/local/tests/unit/useroverhead/test.xml");
		$new = $this->testObj->save(0, $request);

		Test::setShop();

		$overhead = array("PROPERTY_LIST" => array());
		$overhead["ITEM"] = $this->testObj->getById($new->getId());
		$overhead = $this->testObj->resultModifier($overhead);
		$test_prod = ProductsTest::getTestItem();
		foreach ($overhead["ITEM"]["ELEMENT"] as &$element)
			$element["ELEMENT_ID"] = $test_prod["ID"];

		$data = $overhead["ITEM"];
		$data["apply"] = "act";
		$data["accepted"] = "Y";

		$new = $this->testObj->save($new->getId(), $data);
		$result = $this->testObj->getByID($new->getId());

		$this->assertEquals(true, $result["OVERHEAD_ID"] > 0);

		if ($new->isSuccess())
			$this->testObj->delete($new->getId());
	}
}