<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Списки на инвентаризацию");
?>
<?$APPLICATION->IncludeComponent("shopmate:default", "", array("COMPONENT_CLASS" => "InventoryList"));?>
<a class="btn btn-primary pull-right list__inventory_btn" href="#" style="display:none;"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span> Список по продажам за последний месяц </a>
<script type="text/javascript">
if (!Array.isArray(loadedModules)) var loadedModules = [];
var usn = false;
loadedModules.push(function($root) {
	$root.find('.list__inventory_btn').each(function() {
		var $inv_btn = $(this),
			$add_btn = $('.list__add_btn');
		if ($add_btn.length > 0) {
			$inv_btn.attr('href', $add_btn.attr('href') + '&filter=last_month');
			$add_btn.after($inv_btn);
			$inv_btn.show();
		}
	});
});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>