<?

use Yadadya\Shopmate\Components;

class CProductsListComponent extends Components\Component
{
	public function getInit()
	{
		return array(
			"object" => new Components\Products(),
			"type" => "list"
		);
	}
}
?>