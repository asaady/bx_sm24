if (!Array.isArray(loadedModules)) var loadedModules = [];
var usn = false;
loadedModules.push(function($root) {

	$root.find('[data-row_title_id]').each(function() {
		var $row_title = $(this).parents('.row_title td'),
			row_title_id = $(this).data('row_title_id');

		$('[data-row_title_id="'+row_title_id+'"]:not(:first)').remove();
		$('.row_title td:empty').remove();
	});

	$root.find('.list__inventory_btn').each(function() {
		var $inv_btn = $(this),
			$add_btn = $('.list__add_btn'),
			$list_form = $('[name="list_form"]');
		$add_btn.after($inv_btn);
		$inv_btn.show();
		$inv_btn.click(function() {
			$list_form.submit();
			return false;
		});
	});
});