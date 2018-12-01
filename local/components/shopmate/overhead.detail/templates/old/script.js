$(function(){

	// reinitialize
	$('.calc_summ__elem').off();

	function one_item_price(total, nds, quantity, parent){

		var total_summ = (total.length > 0) ? (total.val() > 0) ? total.val() : 1 : 1;
		var nds = (nds.length > 0) ? nds.val() : 0;
		var quantity = (quantity.length > 0) ? (quantity.val() > 0) ? quantity.val() : 1 : 1;

		if(total_summ == 1 || quantity <= 0) return false;

		if(parent.find('.purchasing_price').length > 0){
	
			parent.find('.purchasing_price').val(( total_summ * (100 - nds) / 100  / quantity));
			parent.find('.nds_value').val(total_summ * nds / 100);
		}

	}

	$('body').on('change keyup focus calc_summ', '.form-control.calc_summ__elem.scanner_detect', function(){
		
		var parent = $(this).parents('tr');
		var total = parent.find('.calc_summ__elem');
		var nds_selector = parent.find('.purchasing_nds_selecter');
		var quantity = parent.find('.product_quantity');

		one_item_price(total, nds_selector, quantity, parent);

	});

	$('body').on('change keyup focus calc_summ', '.purchasing_nds_selecter', function(){

		var parent = $(this).parents('tr');
		var total = parent.find('.calc_summ__elem');
		var nds_selector = parent.find('.purchasing_nds_selecter');
		var quantity = parent.find('.product_quantity');

		one_item_price(total, nds_selector, quantity, parent);

	});

});