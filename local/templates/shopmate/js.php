<?if($_REQUEST["ajax"] != "y"):?>

	<?$APPLICATION->AddHeadString("<script>
	var addSelect2Result = [];
	var addSelect2ResultFunc = function(f_term) {
		var result = [];
		if(f_term != undefined && f_term.length > 0) {
			var needle = f_term.toLowerCase();
				haystack = '';
			for (index = addSelect2Result.length - 1; index >= 0; --index) {
				haystack = addSelect2Result[index].text.toLowerCase();
				if((i = haystack.indexOf(needle, 0)) > -1) {
					result.push(addSelect2Result[index]);
				}
			}
		}
		else
			result = addSelect2Result;

		return result;
	}
</script>");?>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<?/*$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.js");?>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/bootstrap.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jqueru.select2.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jqueru.select2.ru.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scannerdetection.compatibility.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scannerdetection.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.hotkeys.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/script.js");*/?>


	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery-1.11.1.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery-migrate-1.2.1.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/bootstrap.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/modernizr.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/pace.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/retina.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.cookies.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scrollTo.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.slimscroll.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.resize.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.spline.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/flot/jquery.flot.pie.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.sparkline.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/morris.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/raphael-2.1.0.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/bootstrap-wizard.min.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/select2.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/css3clock/js/css3clock.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery-ui-1.10.3.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/moment.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fullcalendar.min.js");?> 
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/custom.js");?> 
	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/dashboard.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jqueru.select2.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jqueru.select2.ru.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scannerdetection.compatibility.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.scannerdetection.js");?>

	<?//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.hotkeys.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.maskedinput.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.loader.min.js");?>

	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/jquery.toastr.min.js");?>

<?endif?>