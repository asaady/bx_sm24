<?

use Yadadya\Shopmate\Components;

class CInventoryListComponent extends Components\Component
{
	public function getInit()
	{
		return array(
			"object" => new Components\Inventory(),
			"type" => "list"
		);
	}
}
?>