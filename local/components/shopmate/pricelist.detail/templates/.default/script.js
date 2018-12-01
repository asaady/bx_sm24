$(function(){

	$('.discount__value').each(function() {

		var $discount = $(this),
			$products = $('.discount__product'),
			products = [];
		
		$products.each(function() {
			products.push({
				base_price: $(this).find('.discount__base_price').val(),
				ob_price: $(this).find('.discount__price'),
			});
		});

		$discount.on('keyup', function() {

			var discount = parseFloat($discount.val());

			console.log(discount);

			$.each(products, function(index, product) {
				product.ob_price.val(Math.round(product.base_price*((100-discount)))/100);
			});
		});
	});

});