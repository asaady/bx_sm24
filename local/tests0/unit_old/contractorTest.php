<?
include_once("defaultTest.php");

use Yadadya\Shopmate\Components\Component,
	Yadadya\Shopmate\Test;

class ContractorTest extends \Codeception\Test\Unit
{
	use DefaultTest;

	private $componentName = "shopmate:contractors";
	private $rightChapter = "contractor";
}