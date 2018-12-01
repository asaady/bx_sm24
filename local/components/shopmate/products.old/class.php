<?

use Yadadya\Shopmate\Components;

class CProductsComponent extends Components\Component
{
	public function executeComponent()
	{
		parent::executeComponent(new Components\Products());
	}
}
?>