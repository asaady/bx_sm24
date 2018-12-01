<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class FabricaPartTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:fabrica.part";
	private $rightChapter = "fabrica";
}