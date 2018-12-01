<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class ClientTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:client";
	private $rightChapter = "client";

	public function userCanAllProvider()
	{
		return array(
			array(null, false),
			array("R", false),
			array("W", false),
			array("X", false),
		);
	}
}