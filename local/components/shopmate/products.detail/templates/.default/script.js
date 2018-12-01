$.fn.product_shelf_life = function(){

	var $root = this,
		$window = $(window);

	$root.find('.shelf_life').each(function(){
		var $sl = $(this),
			$sl_fake_block = $('<div class="input-group"></div>'),
			$sl_fake_input = $sl.clone(),
			$sl_fake_size = $('<select class="form-control"></select>'),
			sl_size_list = {
				day: {
					coef: 1,
					title: ['День', 'Дня', 'Дней']
				},
				week: {
					coef: 7,
					title: ['Неделя', 'Недели', 'Недель']
				},
				month: {
					coef: 30,
					title: ['Месяц', 'Месяца', 'Месяцев']
				},
				year: {
					coef: 365,
					title: ['Год', 'Года', 'Лет']
				}
			};

		$.each(sl_size_list, function(key, val) {
			$sl_fake_size.append( $('<option value="'+key+'">'+val.title[0]+'</option>'));
		});

		$sl_fake_block.css('width', '100%');
		$sl_fake_input.css('width', 'auto');
		$sl_fake_size.css('width', 'auto');

		$sl_fake_block.append($sl_fake_input);
		$sl_fake_block.append($sl_fake_size);

		$sl_fake_input.bind('click change keyup', function() {
			var fake_size = $sl_fake_size.val(),
				fake_val = $sl_fake_input.val();

			$sl_fake_size.find('option').each(function() {
				$(this).html(getWord(fake_val, sl_size_list[$(this).attr('value')].title));
			});

			$sl.val(fake_val * sl_size_list[fake_size].coef);
		});

		$sl_fake_size.bind('change', function() {
			var fake_size = $sl_fake_size.val(),
				fake_val = $sl_fake_input.val();
			
			$sl.val(fake_val * sl_size_list[fake_size].coef);
		});

		$.each(sl_size_list, function(key, val) {
			var sl_val = $sl.val();

			if ((sl_val > val.coef) && (sl_val % val.coef == 0)) {
				$sl_fake_input.val(sl_val / val.coef);
				$sl_fake_size.val(key);
				$sl_fake_input.change();
			}
			else
				return false;
		});

		$sl.hide();
		$sl_fake_block.run();
		$sl.after($sl_fake_block);
	});

}

$(function(){


	$('body').product_shelf_life();

});

var getWord = function(number, suffix) { //getWord(5, ['[1]минута', '[2]минуты', '[5]минут']);
	var keys = [2, 0, 1, 1, 1, 2],
		mod = number % 100,
		suffix_key = (mod > 7 && mod < 20) ? 2: keys[Math.min(mod % 10, 5)];
	return suffix[suffix_key];
};