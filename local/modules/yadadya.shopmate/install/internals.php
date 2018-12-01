<?
$module_dir = strstr(__DIR__, "install", true);
require_once($module_dir."lib/loader.php");

$arInternals = array(
	"\\Yadadya\\Shopmate\\Internals\\ProductTable" => "lib/internals/product.php",
	"\\Yadadya\\Shopmate\\Internals\\ProdAmountTable" => "lib/internals/financeprodamount.php",
	"\\Yadadya\\Shopmate\\Internals\\ProdPriceTable" => "lib/internals/financeprodprice.php",
	"\\Yadadya\\Shopmate\\Internals\\FinanceLogTable" => "lib/internals/financelog.php",
	"\\Yadadya\\Shopmate\\Internals\\ReportTasksTable" => "lib/internals/financereporttasks.php",
	"\\Yadadya\\Shopmate\\Internals\\ReportTable" => "lib/internals/financereport.php",
	"\\Yadadya\\Shopmate\\Internals\\BasketTable" => "lib/internals/basket.php",
	"\\Yadadya\\Shopmate\\Internals\\StoreDocsTable" => "lib/internals/storedocs.php",
	"\\Yadadya\\Shopmate\\Internals\\StoreDocsElementTable" => "lib/internals/storedocselement.php",
	"\\Yadadya\\Shopmate\\Internals\\StoreProductTable" => "lib/internals/storeproduct.php",
	"\\Yadadya\\Shopmate\\Internals\\InventoryTable" => "lib/internals/inventory.php",
	"\\Yadadya\\Shopmate\\Internals\\InventoryProductTable" => "lib/internals/inventoryproduct.php",
	"\\Yadadya\\Shopmate\\Internals\\FinanceMoneyTable" => "lib/internals/financemoney.php",
	"\\Yadadya\\Shopmate\\Internals\\FinanceMoneyCatTable" => "lib/internals/financemoneycat.php",
	"\\Yadadya\\Shopmate\\Internals\\UserTable" => "lib/internals/user.php",
	"\\Yadadya\\Shopmate\\Internals\\UserOverheadTable" => "lib/internals/useroverhead.php",
	"\\Yadadya\\Shopmate\\Internals\\UserOverheadChatTable" => "lib/internals/useroverheadchat.php",
	"\\Yadadya\\Shopmate\\Internals\\UserOverheadProductTable" => "lib/internals/useroverheadproduct.php",
	"\\Yadadya\\Shopmate\\Internals\\NotifyTable" => "lib/internals/notify.php",
	"\\Yadadya\\Shopmate\\Internals\\FabricaProductTable" => "lib/internals/fabricaproduct.php",
	"\\Yadadya\\Shopmate\\Internals\\FabricaProductConnectTable" => "lib/internals/fabricaproductconnect.php",
	"\\Yadadya\\Shopmate\\Internals\\FabricaPartTable" => "lib/internals/fabricapart.php",
	"\\Yadadya\\Shopmate\\Internals\\FabricaPartProductTable" => "lib/internals/fabricapartproduct.php",
	"\\Yadadya\\Shopmate\\Internals\\ShopProductHistoryTable" => "lib/internals/shopproducthistory.php",
	"\\Yadadya\\Shopmate\\Internals\\ContractorTable" => "lib/internals/contractor.php",
	"\\Yadadya\\Shopmate\\Internals\\SettingsChapterTable" => "lib/internals/settingschapter.php",
	"\\Yadadya\\Shopmate\\Internals\\SettingsGroupTable" => "lib/internals/settingsgroup.php",
	"\\Yadadya\\Shopmate\\Internals\\SettingsRightTable" => "lib/internals/settingsright.php",
	"\\Yadadya\\Shopmate\\Internals\\SettingsGroupItemTable" => "lib/internals/settingsgroupitem.php",
	"\\Yadadya\\Shopmate\\Internals\\PersonalDepartmentTable" => "lib/internals/personaldepartment.php",
	"\\Yadadya\\Shopmate\\Internals\\PersonalPositionTable" => "lib/internals/personalposition.php",
	"\\Yadadya\\Shopmate\\Internals\\InventoryListTable" => "lib/internals/inventorylist.php",
	"\\Yadadya\\Shopmate\\Internals\\InventoryListProductTable" => "lib/internals/inventorylistproduct.php",
);

\Yadadya\Shopmate\Loader::createDbTables("yadadya.shopmate", $arInternals);
//\Yadadya\Shopmate\Rights::setDefaults();
?>
