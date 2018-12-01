<?

use Yadadya\Shopmate\Components;

class CProductsFilterComponent extends Components\Component
{
	public function getInit()
	{
		return array(
			"object" => new Components\Products(),
			"type" => "filter"
		);
	}
}
?>