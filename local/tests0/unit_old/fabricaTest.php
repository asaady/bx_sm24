<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class FabricaTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:fabrica";
	private $rightChapter = "fabrica";
}