<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class PricelistTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:pricelist";
	private $rightChapter = "products";
}