if (!Array.isArray(loadedModules)) var loadedModules = [];
var usn = false;
loadedModules.push(function($root) {
	
	var hideNDS = function() {
		$('.table_products tr').each(function() {
			$(this).find('th:nth-child(8),th:nth-child(10),td:nth-child(8),td:nth-child(10)').hide();
		});
	}
	var showNDS = function() {
		$('.table_products tr').each(function() {
			$(this).find('th:nth-child(8),th:nth-child(10),td:nth-child(8),td:nth-child(10)').show();
		});
	}

	if (usn)
		hideNDS();
	else
		showNDS();

	$root.find('.contractor_id').each(function (){
		var $contractor = $(this);
		$contractor.on('load_info')
		$contractor.on('load_info', function ($obj, data) {
			if (data.TAX_TYPE == 'usn') {
				usn = true;
				hideNDS();
			}
			else {
				usn = false;
				showNDS();
			}
		});
	});

	$root.find('.product_quantity').each(function() {
		var $quantity = $(this),
			$amount = $quantity.parents('.product_block').find('.product_amount');
		
		$amount.new_value = $amount.val() == '';
		$quantity.on('keyup change focus', function() {
			if ($amount.new_value == true)
				$amount.val($quantity.val());
		});
		$amount.on('keyup change', function() {
			$amount.new_value = $amount.val() == '';
		});
	});

	$root.find('.start_date').each(function() {
		if ($(this).val() == '')
			$(this).val($('.doc_date').val());
	});

	$root.find('.doc_date').each(function() {
		var $doc_date = $(this);
		$doc_date.on('change', function() {
			if ($doc_date.val() != '')
				$('.start_date').each(function() {
					if ($(this).val() == '')
						$(this).val($doc_date.val());
				});
		});
	});
});