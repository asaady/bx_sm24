<?
IncludeModuleLangFile(__FILE__);

class SMStoreProduct
{
	function OnStoreProductAction($id, $arFields)
	{
		/*if($arFields["PRODUCT_ID"] > 0)
		{
			$QUANTITY = 0;
			$rsProps = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arFields["PRODUCT_ID"]), false, false, array("ID", "AMOUNT"));
			while($arProp = $rsProps->GetNext())
				$QUANTITY += $arProp["AMOUNT"];
			CCatalogProduct::Update($arFields["PRODUCT_ID"], array("QUANTITY" => $QUANTITY));
		}*/
	}
}