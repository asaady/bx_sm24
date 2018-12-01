<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class InventoryTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:inventory";
	private $rightChapter = "inventory";
}