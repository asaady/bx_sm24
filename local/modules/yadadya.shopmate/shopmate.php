<?php
IncludeModuleLangFile(__FILE__);

$arClasses = array(
	/*GENERAL*/
	//old
	"SM" => "classes/general/shopmate.php",
	"SMUser" => "classes/general/user.php",
	"SMShops" => "classes/general/shops.php",
	//objects
	"Yadadya\\Shopmate\\Loader" => "lib/loader.php",
	"Yadadya\\Shopmate\\Shops" => "lib/shops.php",
	"Yadadya\\Shopmate\\Rights" => "lib/rights.php",
	"Yadadya\\Shopmate\\User" => "lib/user.php",
	"Yadadya\\Shopmate\\User" => "lib/user.php",
	"Yadadya\\Shopmate\\XMLElement" => "lib/xmlelement.php",
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\StoreTable" => "lib/bitrix_internals/store.php",
	"Yadadya\\Shopmate\\BitrixInternals\\PriceTable" => "lib/bitrix_internals/price.php",
	//internals
	"Yadadya\\Shopmate\\BitrixInternals\\NotifyTable" => "lib/internals/notify.php",
	"Yadadya\\Shopmate\\BitrixInternals\\PermissionTable" => "lib/internals/permission.php",
	//components
	"Yadadya\\Shopmate\\Components\\Base" => "lib/components/base.php",
	"Yadadya\\Shopmate\\Components\\Component" => "lib/components/component.php",
	"Yadadya\\Shopmate\\Components\\Dflt" => "lib/components/dflt.php",
	"Yadadya\\Shopmate\\Components\\Template" => "lib/components/template.php",
	"Yadadya\\Shopmate\\Components\\Store" => "lib/components/store.php",

	/*SETTINGS*/
	//internals
	"Yadadya\\Shopmate\\Internals\\SettingsChapterTable" => "lib/internals/settingschapter.php",
	"Yadadya\\Shopmate\\Internals\\SettingsGroupTable" => "lib/internals/settingsgroup.php",
	"Yadadya\\Shopmate\\Internals\\SettingsRightTable" => "lib/internals/settingsright.php",
	"Yadadya\\Shopmate\\Internals\\SettingsGroupItemTable" => "lib/internals/settingsgroupitem.php",
	//components
	"Yadadya\\Shopmate\\Components\\SettingsChapter" => "lib/components/settingschapter.php",
	"Yadadya\\Shopmate\\Components\\SettingsGroup" => "lib/components/settingsgroup.php",
	"Yadadya\\Shopmate\\Components\\SettingsRight" => "lib/components/settingsright.php",

	/*PRODUCTS*/
	//old
	"SMProductions" => "classes/general/productions.php",
	"SMStoreProduct" => "classes/general/store_product.php",
	"SMProduct" => "classes/general/product.php",
	"SMStoreBarcode" => "classes/general/store_barcode.php",
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\ElementTable" => "lib/bitrix_internals/element.php",
	"Yadadya\\Shopmate\\BitrixInternals\\SectionTable" => "lib/bitrix_internals/section.php",
	"Yadadya\\Shopmate\\BitrixInternals\\ProductTable" => "lib/bitrix_internals/product.php",
	"Yadadya\\Shopmate\\BitrixInternals\\StoreProductTable" => "lib/bitrix_internals/storeproduct.php",
	"Yadadya\\Shopmate\\BitrixInternals\\StoreBarcodeTable" => "lib/bitrix_internals/storebarcode.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\ProductTable" => "lib/internals/product.php",
	"Yadadya\\Shopmate\\Internals\\StoreProductTable" => "lib/internals/storeproduct.php",
	//objects
	"Yadadya\\Shopmate\\Products" => "lib/products.php",
	//components
	"Yadadya\\Shopmate\\Components\\Products" => "lib/components/products.php",
	//events
	"Yadadya\\Shopmate\\Events\\Products" => "lib/events/products.php",

	/*PRICELIST*/
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\CatGroupTable" => "lib/bitrix_internals/catgroup.php",
	"Yadadya\\Shopmate\\BitrixInternals\\CatGroupLangTable" => "lib/bitrix_internals/catgrouplang.php",
	"Yadadya\\Shopmate\\BitrixInternals\\CatGroup2GroupTable" => "lib/bitrix_internals/catgroup2group.php",
	//components
	"Yadadya\\Shopmate\\Components\\Pricelist" => "lib/components/pricelist.php",
	"Yadadya\\Shopmate\\Components\\PricelistProduct" => "lib/components/pricelistproduct.php",

	/*OVERHEAD*/
	"SMDocs" => "classes/general/store_docs.php",
	"SMStoreDocsElement" => "classes/general/store_docs_element.php",
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\StoreDocsTable" => "lib/bitrix_internals/storedocs.php",
	"Yadadya\\Shopmate\\BitrixInternals\\StoreDocsElementTable" => "lib/bitrix_internals/storedocselement.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\StoreDocsTable" => "lib/internals/storedocs.php",
	"Yadadya\\Shopmate\\Internals\\StoreDocsElementTable" => "lib/internals/storedocselement.php",
	//events
	"Yadadya\\Shopmate\\Events\\Overhead" => "lib/events/overhead.php",

	/*EGAIS*/
	//old
	"SMEGAIS" => "classes/general/egais.php",
	"SMEGAISWaybill" => "classes/general/egais_waybill.php",
	"SMEGAISWaybillAct" => "classes/general/egais_waybill_act.php",
	"SMEGAISAlcCode" => "classes/general/egais_alccode.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\EgaisAlccodeTable" => "lib/internals/egaisalccode.php",
	//objects
	"Yadadya\\Shopmate\\Egais\\EgaisTable" => "lib/egais.php",
	"Yadadya\\Shopmate\\Egais\\EgaisCustom" => "lib/egaiscustom.php",

	/*INVENTORY*/
	//old
	"SMInventory" => "classes/general/inventory.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\InventoryTable" => "lib/internals/inventory.php",
	"Yadadya\\Shopmate\\Internals\\InventoryProductTable" => "lib/internals/inventoryproduct.php",
	"Yadadya\\Shopmate\\Internals\\InventoryListTable" => "lib/internals/inventorylist.php",
	"Yadadya\\Shopmate\\Internals\\InventoryListProductTable" => "lib/internals/inventorylistproduct.php",
	//objects
	"Yadadya\\Shopmate\\Inventory" => "lib/inventory.php",

	/*CASH*/
	//old
	"SMCashDevice" => "classes/general/cash_device.php",
	//objects
	"Yadadya\\Shopmate\\Cash" => "lib/cash.php",
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\OrderTable" => "lib/bitrix_internals/order.php",
	"Yadadya\\Shopmate\\BitrixInternals\\BasketTable" => "lib/bitrix_internals/basket.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\BasketTable" => "lib/internals/basket.php",
	//components
	"Yadadya\\Shopmate\\Components\\Cash" => "lib/components/cash.php",


	/*FINANCE*/
	//old
	"SMFinance" => "classes/general/finance.php",
	"SMFinanceLog" => "classes/general/finance_log.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\FinanceLogTable" => "lib/internals/financelog.php",
	"Yadadya\\Shopmate\\Internals\\ProdAmountTable" => "lib/internals/financeprodamount.php",
	"Yadadya\\Shopmate\\Internals\\ProdPriceTable" => "lib/internals/financeprodprice.php",
	"Yadadya\\Shopmate\\Internals\\ReportTasksTable" => "lib/internals/financereporttasks.php",
	"Yadadya\\Shopmate\\Internals\\ReportTable" => "lib/internals/financereport.php",
	"Yadadya\\Shopmate\\Internals\\FinanceMoneyTable" => "lib/internals/financemoney.php",
	"Yadadya\\Shopmate\\Internals\\FinanceMoneyCatTable" => "lib/internals/financemoneycat.php",
	//objects
	"Yadadya\\Shopmate\\FinanceLog" => "lib/financelog.php",
	"Yadadya\\Shopmate\\FinanceProd" => "lib/financeprod.php",
	"Yadadya\\Shopmate\\FinanceReport" => "lib/financereport.php",
	"Yadadya\\Shopmate\\FinanceMoney" => "lib/financemoney.php",
	//events
	"Yadadya\\Shopmate\\Events\\Finance" => "lib/events/finance.php",
	"Yadadya\\Shopmate\\Events\\FinanceReport" => "lib/events/financereport.php",
	//components
	"Yadadya\\Shopmate\\Components\\FinanceMoney" => "lib/components/financemoney.php",
	"Yadadya\\Shopmate\\Components\\FinanceMoneyCat" => "lib/components/financemoneycat.php",
	"Yadadya\\Shopmate\\Components\\FinanceReportSales" => "lib/components/financereportsales.php",

	/*FABRICA*/
	//old
	"SMFabricaProduct" => "classes/general/fabrica_product.php",
	"SMFabricaProductConnect" => "classes/general/fabrica_product_connect.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\FabricaProductTable" => "lib/internals/fabricaproduct.php",
	"Yadadya\\Shopmate\\Internals\\FabricaProductConnectTable" => "lib/internals/fabricaproductconnect.php",
	"Yadadya\\Shopmate\\Internals\\FabricaPartTable" => "lib/internals/fabricapart.php",
	"Yadadya\\Shopmate\\Internals\\FabricaPartProductTable" => "lib/internals/fabricapartproduct.php",
	//components
	"Yadadya\\Shopmate\\Components\\Fabrica" => "lib/components/fabrica.php",
	"Yadadya\\Shopmate\\Components\\FabricaPart" => "lib/components/fabricapart.php",

	/*CONTRACTORS*/
	//objects
	"Yadadya\\Shopmate\\Contractor" => "lib/contractor.php",
	//old
	"SMContractor" => "classes/general/contractor.php",
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\ContractorTable" => "lib/bitrix_internals/contractor.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\ContractorTable" => "lib/internals/contractor.php",
	//events
	"Yadadya\\Shopmate\\Events\\Contractor" => "lib/events/contractor.php",

	/*CLIENTS*/
	//objects
	"Yadadya\\Shopmate\\Client" => "lib/client.php",
	//old
	"SMDiscount" => "classes/general/discount.php",
	//components
	"Yadadya\\Shopmate\\Components\\Client" => "lib/components/client.php",

	/*TODO*/
	"SMTodo" => "classes/general/todo.php",
	"SMTodoLog" => "classes/general/todo_log.php",

	/*DNC*/
	//old
	"SMDnc" => "classes/general/dnc.php",
	"SMDncDB" => "classes/general/dnc_db.php",
	//events
	"Yadadya\\Shopmate\\Events\\Dnc" => "lib/events/dnc.php",

	/*USER*/
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\UserTable" => "lib/bitrix_internals/user.php",
	"Yadadya\\Shopmate\\BitrixInternals\\UserGroupTable" => "lib/bitrix_internals/usergroup.php",
	"Yadadya\\Shopmate\\BitrixInternals\\GroupTable" => "lib/bitrix_internals/group.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\UserTable" => "lib/internals/user.php",
	"Yadadya\\Shopmate\\Internals\\PersonalDepartmentTable" => "lib/internals/personaldepartment.php",
	"Yadadya\\Shopmate\\Internals\\PersonalPositionTable" => "lib/internals/personalposition.php",

	//objects
	"Yadadya\\Shopmate\\UserOverhead" => "lib/useroverhead.php",
	//components
	"Yadadya\\Shopmate\\Components\\UserOverhead" => "lib/components/useroverhead.php",
	"Yadadya\\Shopmate\\Components\\PersonalDepartment" => "lib/components/personaldepartment.php",
	//internals
	"Yadadya\\Shopmate\\Internals\\UserOverheadTable" => "lib/internals/useroverhead.php",
	"Yadadya\\Shopmate\\Internals\\UserOverheadChatTable" => "lib/internals/useroverheadchat.php",
	"Yadadya\\Shopmate\\Internals\\UserOverheadProductTable" => "lib/internals/useroverheadproduct.php",
	//bitrix_internals
	"Yadadya\\Shopmate\\BitrixInternals\\VatTable" => "lib/bitrix_internals/vat.php",
);

CModule::AddAutoloadClasses("yadadya.shopmate", $arClasses);

?>