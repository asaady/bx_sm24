<?

use Yadadya\Shopmate\Components;

class CProductsComponent extends Components\Component
{
	public function getInit()
	{
		return new Components\Products();
	}
}
?>