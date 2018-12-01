<?
namespace Yadadya\Shopmate;

class XMLElement extends \SimpleXMLElement
{
	public function attrArray()
	{
		$data = array();
		$attributes = $this->attributes();
		foreach ($attributes as $key => $attr) 
			$data[$key] = $attr->__toString();
		return $data;
	}
}