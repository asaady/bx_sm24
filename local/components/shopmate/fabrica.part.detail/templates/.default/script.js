$.fn.fabrica_part = function(){

	var $root = this,
		$window = $(window);

	$root.find('.fabrica_part_products[data-connect_url]').each(function(){
		var $products = $(this),
			$connects = $root.find('.fabrica_part_connects'),
			$connect_add_btn = $connects.find('.inclone__btn'),
			$connect_add_btn = $connects.find('.inclone__btn'),
			connect_url = $products.data('connect_url'),
			$products_input = $products.find('.fabrica_part_product'),
			connects = [];

		$connect_add_btn.hide();
		$connects.find('.deleted_block_btn').hide();

		var product_change = function() {
			var products_id = [];
			$products.find('.fabrica_part_product').each(function() {
				var id = $(this).val() == 'undefined' ? 0 : parseInt($(this).val());
				if (id > 0)
					products_id.push(id);
			});
			$connects.find('.deleted_block').remove();
			if (products_id.length > 0) {
				$.ajax({
					url: $products.data('connect_url'),
					data: {
						ppid: products_id
					},
					success: function(data){
						$connects.find('.deleted_block').remove();
						connects = data.items;
						$.each(data.items, function(index, value) {
							$connect_add_btn.click();
						});
					}
				});
			}
		};

		$products.find('.fabrica_part_product').bind("change", product_change);
		$products.bind("deleteBlockAfter", product_change);

		$products.find('.inclone__btn').bind('addClone', function(e, $prod_new) {
			$prod_new.find('.fabrica_part_product').bind("change", product_change);
		});

		$connect_add_btn.bind('addClone', function(e, $item_connect) {
			if (connects.length > 0)
			{
				var $select = $item_connect.find('.fabrica_part_connect');
					connect = connects.shift();
				$select.empty();
				$select.append( $('<option value="'+connect.id+'" selected>'+connect.text+'</option>'));
				$select.change();
			}
		});

	});

}

$(function(){

	// run scripts in .page block
	$('body').fabrica_part();

});