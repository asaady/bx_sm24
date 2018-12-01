<?
$module_dir = strstr(__DIR__, "install", true);
require_once($module_dir."lib/loader.php");
$module_name = "yadadya.shopmate";

$arEvents = array(

	/*PRODUCTS*/

	//overhead
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCOverheadAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnOverheadAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCOverheadUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnOverheadAction"),
	//inventory
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCInventoryAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnInventoryAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCInventoryUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnInventoryAction"),
	//fabrica part
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCFabricaPartAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnFabricaPartAdd"),
	//fabrica
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCFabricaAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnFabricaAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCFabricaUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnFabricaAction"),
	//product
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCProductsAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnProductAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCProductsUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Products", "TO_METHOD" => "OnProductAction"),

	/*OVERHEAD*/

	//user overhead
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCUserOverheadAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Overhead", "TO_METHOD" => "OnShopmateUserOverheadAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCUserOverheadUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Overhead", "TO_METHOD" => "OnShopmateUserOverheadAction"),

	/*NOTIFY*/

	//overhead
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCUserOverheadAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnShopmateUserOverheadAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCUserOverheadUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnShopmateUserOverheadAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCOverheadAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnShopmateOverheadAdd"),
	//all
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnBeforeShopmateComponentAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnBeforeShopmateComponentAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnBeforeShopmateComponentUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnBeforeShopmateComponentAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateComponentAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnShopmateComponentAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateComponentUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnShopmateComponentAction"),
	//user auth
	array("FROM_MODULE_ID" => "main", "MESSAGE_ID" => "OnAfterUserAuthorize", "TO_CLASS" => "\Yadadya\Shopmate\Events\User", "TO_METHOD" => "saveLastAuth"),
	//egais waybill
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnEGAISWaybillAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Notify", "TO_METHOD" => "OnEGAISWaybillAdd"),

	/*CONTRACTOR*/

	//register
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCRegisterAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\Contractor", "TO_METHOD" => "OnShopmateRegisterAdd"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCRegisterUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\Contractor", "TO_METHOD" => "OnShopmateRegisterAdd"),

	/*FINANCE MONEY*/

	//cash
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnBeforeShopmateCCashAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnBeforeShopmateCashAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnBeforeShopmateCCashUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnBeforeShopmateCashAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCCashAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnShopmateCashAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCCashUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnShopmateCashAction"),
	//cash self
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnBeforeShopmateCCashSelfAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnBeforeShopmateCashAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnBeforeShopmateCCashSelfUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnBeforeShopmateCashAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCCashSelfAdd", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnShopmateCashAction"),
	array("FROM_MODULE_ID" => $module_name, "MESSAGE_ID" => "OnShopmateCCashSelfUpdate", "TO_CLASS" => "\Yadadya\Shopmate\Events\FinanceMoney", "TO_METHOD" => "OnShopmateCashAction"),
);

\Yadadya\Shopmate\Loader::addEvents($module_name, $arEvents);

		RegisterModuleDependences("iblock", "OnBeforeIBlockElementAdd", $module_name, "SMProductions", "CustomSave");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $module_name, "SMProductions", "CustomSave");
		RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate", $module_name, "SMProductions", "CustomSave");
		RegisterModuleDependences("main", "OnProlog", $module_name, "SMProductions", "CustomSaveName");

		RegisterModuleDependences("catalog", "OnCatalogStoreAdd", $module_name, "SMShops", "CustomShop");
		RegisterModuleDependences("catalog", "OnCatalogStoreUpdate", $module_name, "SMShops", "CustomShop");
		RegisterModuleDependences("catalog", "OnCatalogStoreDelete", $module_name, "SMShops", "CustomShop");

		RegisterModuleDependences("main", "OnBeforeGroupAdd", $module_name, "SMShops", "CustomGroupClose");
		RegisterModuleDependences("main", "OnBeforeGroupUpdate", $module_name, "SMShops", "CustomGroupClose");
		RegisterModuleDependences("main", "OnBeforeGroupDelete", $module_name, "SMShops", "CustomGroupClose");

		RegisterModuleDependences("catalog", "OnBeforeGroupAdd", $module_name, "SMShops", "CustomPriceTypeClose");
		RegisterModuleDependences("catalog", "OnBeforeGroupUpdate", $module_name, "SMShops", "CustomPriceTypeClose");
		RegisterModuleDependences("catalog", "OnBeforeGroupDelete", $module_name, "SMShops", "CustomPriceTypeClose");

		RegisterModuleDependences("main", "OnProlog", $module_name, "SMShops", "setUserShopParams");

		RegisterModuleDependences("catalog", "OnDocumentAdd", $module_name, "SMDocs", "OnCatalogDocumentAdd");
		RegisterModuleDependences("catalog", "OnDocumentUpdate", $module_name, "SMDocs", "OnCatalogDocumentUpdate");
		RegisterModuleDependences("catalog", "OnDocumentDelete", $module_name, "SMDocs", "OnCatalogDocumentDelete");

		RegisterModuleDependences("catalog", "OnCatalogStoreDocsElementAdd", $module_name, "SMStoreDocsElement", "OnCatalogStoreDocsElementAdd");
		RegisterModuleDependences("catalog", "OnCatalogStoreDocsElementUpdate", $module_name, "SMStoreDocsElement", "OnCatalogStoreDocsElementUpdate");
		RegisterModuleDependences("catalog", "OnCatalogStoreDocsElementDelete", $module_name, "SMStoreDocsElement", "OnCatalogStoreDocsElementDelete");

		RegisterModuleDependences("main", "OnBeforeUserUpdate", $module_name, "SMUser", "CustomBuyerClose");
		RegisterModuleDependences("main", "OnBeforeUserDelete", $module_name, "SMUser", "CustomBuyerClose");

		RegisterModuleDependences("main", "OnBeforeGroupAdd", $module_name, "SMDiscount", "CustomGroupClose");
		RegisterModuleDependences("main", "OnBeforeGroupUpdate", $module_name, "SMDiscount", "CustomGroupClose");
		RegisterModuleDependences("main", "OnBeforeGroupDelete", $module_name, "SMDiscount", "CustomGroupClose");

		RegisterModuleDependences("catalog", "OnDocumentAdd", $module_name, "SMFinance", "OnOverheadAction");
		RegisterModuleDependences("catalog", "OnDocumentUpdate", $module_name, "SMFinance", "OnOverheadAction");
		RegisterModuleDependences("catalog", "OnBeforeDocumentDelete", $module_name, "SMFinance", "OnOverheadAction");
		RegisterModuleDependences("catalog", "OnBeforeStoreProductAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "onAddAmount");
		RegisterModuleDependences("catalog", "OnBeforeStoreProductUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "onUpdateAmount");
		RegisterModuleDependences("catalog", "OnBeforeStoreProductDelete", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "onDeleteAmount");
		RegisterModuleDependences("catalog", "OnPriceAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdatePrice");
		RegisterModuleDependences("catalog", "OnPriceUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdatePrice");
		RegisterModuleDependences("catalog", "OnProductAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdatePurchasingPrice");
		RegisterModuleDependences("catalog", "OnProductUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdatePurchasingPrice");

		RegisterModuleDependences("catalog", "OnStoreProductAdd", $module_name, "SMStoreProduct", "OnStoreProductAction");
		RegisterModuleDependences("catalog", "OnStoreProductUpdate", $module_name, "SMStoreProduct", "OnStoreProductAction");
		RegisterModuleDependences("catalog", "OnStoreProductDelete", $module_name, "SMStoreProduct", "OnStoreProductAction");

		RegisterModuleDependences($module_name, "OnFabricaProductAdd", $module_name, "SMFabricaProduct", "OnFPUpdateToCalcQuantity");
		RegisterModuleDependences($module_name, "OnFabricaProductUpdate", $module_name, "SMFabricaProduct", "OnFPUpdateToCalcQuantity");
		RegisterModuleDependences($module_name, "OnBeforeFabricaProductDelete", $module_name, "SMFabricaProduct", "OnFPBeforeDelToCalcQuantity");
		RegisterModuleDependences($module_name, "OnFabricaProductDelete", $module_name, "SMFabricaProduct", "OnFPDeleteToCalcQuantity");
		RegisterModuleDependences("catalog", "OnFabricaProductAdd", $module_name, "SMFabricaProduct", "OnSPUpdateToCalcQuantity");
		RegisterModuleDependences("catalog", "OnFabricaProductUpdate", $module_name, "SMFabricaProduct", "OnSPUpdateToCalcQuantity");
		RegisterModuleDependences("catalog", "OnBeforeFabricaProductDelete", $module_name, "SMFabricaProduct", "OnSPBeforeDelToCalcQuantity");
		RegisterModuleDependences("catalog", "OnFabricaProductDelete", $module_name, "SMFabricaProduct", "OnSPDeleteToCalcQuantity");

		RegisterModuleDependences("main", "OnProlog", $module_name, "SMCashDevice", "onGetResult");

		RegisterModuleDependences("main", "OnProlog", $module_name, "SMEGAIS", "onPrologOptOut");
		RegisterModuleDependences("main", "OnProlog", $module_name, "SMEGAIS", "onPrologOptIn");
		RegisterModuleDependences($module_name, "OnEGAISAdd", $module_name, "SMEGAISWaybill", "onUpdateOptOut");
		RegisterModuleDependences($module_name, "OnEGAISUpdate", $module_name, "SMEGAISWaybill", "onUpdateOptOut");
		RegisterModuleDependences($module_name, "OnEGAISAdd", $module_name, "SMEGAISWaybill", "onUpdateOptIn");
		RegisterModuleDependences($module_name, "OnEGAISUpdate", $module_name, "SMEGAISWaybill", "onUpdateOptIn");
		RegisterModuleDependences($module_name, "OnEGAISWaybillActAdd", $module_name, "SMEGAISWaybillAct", "onUpdateWaybillAct");
		RegisterModuleDependences($module_name, "OnEGAISWaybillActUpdate", $module_name, "SMEGAISWaybillAct", "onUpdateWaybillAct");
		RegisterModuleDependences($module_name, "OnEGAISAdd", $module_name, "\\Yadadya\\Shopmate\\Egais\\EgaisCustom", "onUpdateOpt");
		RegisterModuleDependences($module_name, "OnEGAISUpdate", $module_name, "\\Yadadya\\Shopmate\\Egais\\EgaisCustom", "onUpdateOpt");

		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $module_name, "SMDnc", "onElementUpdate");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $module_name, "SMDnc", "onElementUpdate");
		RegisterModuleDependences("iblock", "OnProductAdd", $module_name, "SMDnc", "onProductUpdate");
		RegisterModuleDependences("iblock", "OnProductUpdate", $module_name, "SMDnc", "onProductUpdate");
		RegisterModuleDependences("catalog", "OnPriceAdd", $module_name, "SMDnc", "onPriceUpdate");
		RegisterModuleDependences("catalog", "OnPriceUpdate", $module_name, "SMDnc", "onPriceUpdate");
		RegisterModuleDependences("catalog", "OnStoreProductAdd", $module_name, "SMDnc", "onAmountUpdate");
		RegisterModuleDependences("catalog", "OnStoreProductUpdate", $module_name, "SMDnc", "onAmountUpdate");
		RegisterModuleDependences("catalog", "OnCatalogStoreBarCodeAdd", $module_name, "SMDnc", "onBarcodeUpdate");
		RegisterModuleDependences("catalog", "OnCatalogStoreBarCodeUpdate", $module_name, "SMDnc", "onBarcodeUpdate");

		RegisterModuleDependences("catalog", "OnStoreProductAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnStoreProductAction");
		RegisterModuleDependences("catalog", "OnStoreProductUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnStoreProductAction");
		RegisterModuleDependences("catalog", "OnCatalogStoreAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "updateCronAgent");
		RegisterModuleDependences("catalog", "OnCatalogStoreUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "updateCronAgent");
		RegisterModuleDependences("catalog", "OnCatalogStoreDelete", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "updateCronAgent");
		
		RegisterModuleDependences("sale", "BasketOnAfterAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdateBasket");
		RegisterModuleDependences("sale", "BasketOnAfterUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdateBasket");
		RegisterModuleDependences("sale", "BasketOnAfterDelete", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdateBasket");
		RegisterModuleDependences("sale", "OnBasketAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdateBasket");
		RegisterModuleDependences("sale", "OnBasketUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdateBasket");
		RegisterModuleDependences("sale", "OnBasketDelete", $module_name, "\\Yadadya\\Shopmate\\Events\\FinanceReport", "OnUpdateBasket");

		RegisterModuleDependences("catalog", "OnProductAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\Products", "OnUpdateProduct");
		RegisterModuleDependences("catalog", "OnProductUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\Products", "OnUpdateProduct");

		RegisterModuleDependences("catalog", "OnCatalogStoreDocsElementAdd", $module_name, "\\Yadadya\\Shopmate\\Events\\Products", "OnUpdateStoreDocsElement");
		RegisterModuleDependences("catalog", "OnCatalogStoreDocsElementUpdate", $module_name, "\\Yadadya\\Shopmate\\Events\\Products", "OnUpdateStoreDocsElement");

		$eventManager = \Bitrix\Main\EventManager::getInstance();

		$eventManager->registerEventHandler($module_name, 'OnShopmateCProductsAdd', $module_name, '\Yadadya\Shopmate\Events\Dnc', 'OnShopmateProductAction');
		$eventManager->registerEventHandler($module_name, 'OnShopmateCProductsUpdate', $module_name, '\Yadadya\Shopmate\Events\Dnc', 'OnShopmateProductAction');

		$eventManager->registerEventHandler($module_name, 'OnShopmateCOverheadAdd', $module_name, '\Yadadya\Shopmate\Events\Products', 'OnShopmateOverheadAdd');
		$eventManager->registerEventHandler($module_name, 'OnShopmateCOverheadAdd', $module_name, '\Yadadya\Shopmate\Events\Finance', 'OnShopmateOverheadAdd');
		$eventManager->registerEventHandler($module_name, 'OnShopmateCOverheadAdd', $module_name, '\Yadadya\Shopmate\Events\FinanceReport', 'OnShopmateOverheadAdd');

		$eventManager->registerEventHandler($module_name, 'OnBeforeShopmateCOverheadAdd', $module_name, '\Yadadya\Shopmate\Events\FinanceMoney', 'OnBeforeShopmateOverheadAction');
		$eventManager->registerEventHandler($module_name, 'OnBeforeShopmateCOverheadUpdate', $module_name, '\Yadadya\Shopmate\Events\FinanceMoney', 'OnBeforeShopmateOverheadAction');
		$eventManager->registerEventHandler($module_name, 'OnShopmateCOverheadAdd', $module_name, '\Yadadya\Shopmate\Events\FinanceMoney', 'OnShopmateOverheadAction');
		$eventManager->registerEventHandler($module_name, 'OnShopmateCOverheadUpdate', $module_name, '\Yadadya\Shopmate\Events\FinanceMoney', 'OnShopmateOverheadAction');

		

		$eventManager->registerEventHandler($module_name, 'OnShopmateCFinanceMoneyAdd', $module_name, '\Yadadya\Shopmate\Events\Overhead', 'OnShopmateFinanceMoneyAdd');
?>
