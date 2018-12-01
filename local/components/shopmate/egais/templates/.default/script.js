if (window.jQuery) {
	$(document).bind('egais-updateOptOut', function(e, reboot) {
		$('.egais_update__load').hide();
		reboot = parseInt(reboot);
		if(reboot > 0)
		{
			$('.egais_update__reload').show();
		}
	});
	$(document).bind('egais-updateOptOutStart', function(e, v) {
		$('.egais_update__load').show();
	});
}