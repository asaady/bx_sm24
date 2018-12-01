// Run scripts in selected jQuery block
$.fn.run = function(){

	var $root = this,
		$window = $(window);

	$root.find('a.modal_ajax').each(function(){
		var $link = $(this),
			url = $link.attr('href'),
			title = $link.data('title'),
			backdrop = $link.data('backdrop');
		$(this).click(function() {
			$.ajax({
				url: $(this).attr('href'),
				success: function(data){
					var $modal = $('<div class="modal fade bs-example-modal-lg-checkout" tabindex="-1" data-show="true" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button><h4 class="modal-title">'+title+'</h4></div><div class="modal-body">'+data+'</div></div></div></div>');
					$modal.modal({
						show: true,
						backdrop: backdrop
					});
					$modal.on('hidden.bs.modal', function (e) {
						$modal.remove();
					});
				}
			});
			return false;
		});
	});

	$root.find('a.modal_noajax').each(function(){
		var $link = $(this),
			url = $link.attr('href'),
			title = $link.data('title'),
			backdrop = $link.data('backdrop');
		url += (url.indexOf('?') > -1 ? '&' : '?') + 'iframe=y';
		if(title == undefined || title == '')
			title = $link.text();
		$(this).click(function() {
			var $modal = $('<div class="modal fade bs-example-modal-lg bs-example-modal-lg-checkout" tabindex="-1" data-show="true" role="dialog"><div class="modal-dialog modal-lg" style="height:100%;"><div class="modal-content" style="height:100%;"><div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button><h4 class="modal-title">'+title+'</h4></div><div class="modal-body" style="height:90%;"><iframe style="border: none;" src="'+url+'" width="100%" height="100%"></iframe></div></div></div></div>');

			$modal.modal({
				show: true,
				backdrop: backdrop
			});
			$modal.on('hidden.bs.modal', function (e) {
				$modal.remove();
			});
			return false;
		});
	});

	$root.find('.modal_open').each(function(){
		$(this).modal('show').on('hidden.bs.modal', function (e) {
			window.location.reload();
		});
	});

	$root.find('[data-toggle=tooltip][title]').each(function () {
		$(this).tooltip();
	});

	$root.find('[data-verification]').each(function () {
		var check_verification = function ($input) {
			var verif = $input.data('verification'),
				val = $input.val();
			var pos = doGetCaretPosition($input[0]) - val.length;

			if(verif == 'float') {
				val = val.replace(/[^0-9,.]/g, '');
				val = val.replace(/,/g, '.');
				var last_delim = val.indexOf('.');
				if (last_delim >= 0) {
					val = val.replace(/[.]/g, '');
					val = val.substring(0, last_delim) + '.' + val.substring(last_delim);
				}
			}

			$input.val(val);
			setCaretPosition($input[0], pos + val.length);
		}
		
		switch ($(this).data('verification')) {
			case 'float':
				$(this).on('keyup change blur', function() { check_verification($(this)); });
				break;
			case 'phone':
				$(this).mask('+7 (999) 99-99-999');
				break;
			case 'date':
				$(this).mask('99.99.9999');
				break;
			case 'datetime':
				$(this).mask('99.99.9999 99:99:00');
				break;
			case 'email':
				$(this).attr('type', 'email');
				break;
			default:
				$(this).mask($(this).data('verification'));
				break;
		}
	});

	$root.find('.nmrc_item').each(function() {
		$(this).parents('.nmrc').each(function() {
			$(this).find('.nmrc_item').each(function(i, n) {
				$(this).html((i+1)+'.');
			});
		});
	});

	$root.find('.modal_iframe').each(function(){
		$(this).on('show.bs.modal', function (e) {
			$(this).find('.modal_iframe__block').html('<iframe style="border: none;" src="'+$(this).data('src')+'"></iframe>');
		});
		$(this).on('hidden.bs.modal', function (e) {
			window.location.reload();
		});
	});

	$root.find('.keyboard_block').each(function() {
		var $kb = $(this),
			$kb_p = $kb.find('.kb_p'),
			$kb_bs = $kb.find('.kb_bs'),
			inpt_selector = '.keyboard.keyboard_np';
			//$inpt_kb = $('.keyboard.keyboard_np:focus');

		$kb_p.unbind('click');
		$kb_bs.unbind('click');
		$kb.on('click', function() {
			var $inpt_kb = $(inpt_selector+'.keyboard_active');
			if($inpt_kb.prop('readonly')) return false;
			$inpt_kb.focus();
			//$kb.fadeIn(10);
			return false;
		});
		$kb_p.on('click', function() {
			var $inpt_kb = $(inpt_selector+'.keyboard_active');
			if($inpt_kb.prop('readonly')) return false;
			if($inpt_kb.hasClass('keyboard_select')) {
				$inpt_kb.removeClass('keyboard_select');
				$inpt_kb.val("0");
			}
			var	kb_p = $(this).html(),
				ikb = parseFloat($inpt_kb.val());
			$inpt_kb.val(ikb > 0 || kb_p == '.' || $inpt_kb.val().indexOf('.', 0) > -1 ? $inpt_kb.val() + kb_p : kb_p).focus();
			//$kb.fadeIn(10);
			return false;
		});
		$kb_bs.on('click', function() {
			var $inpt_kb = $(inpt_selector+'.keyboard_active');
			if($inpt_kb.prop('readonly')) return false;
			if($inpt_kb.hasClass('keyboard_select')) {
				$inpt_kb.removeClass('keyboard_select');
				$inpt_kb.val("0");
			}
			$inpt_kb.val($inpt_kb.val().substring(0, $inpt_kb.val().length - 1)).focus();
			$kb.fadeIn(10);
			if($inpt_kb.val().length == 0)
				$inpt_kb.val(0);
			return false;
		});
	});
	$root.find('.keyboard.keyboard_np').each(function() {
		var $inpt_kb = $(this),
			inpt_selector = '.keyboard.keyboard_np';
		$inpt_kb.on('focus', function() {
			if($inpt_kb.prop('readonly')) return false;
			$('.keyboard_block').show();
			if(!$inpt_kb.hasClass('keyboard_active'))
				$inpt_kb.addClass('keyboard_select').select();
			$(inpt_selector).removeClass('keyboard_active');
			$inpt_kb.addClass('keyboard_active');
			//$inpt_kb = $ikbc;
		});
		if($inpt_kb.hasClass('keyboard_open'))
			$inpt_kb.focus();
	});

	$root.find('select[readonly]').each(function(){
		var $sel_read = $(this),
			$hide_input = $sel_read.next(".readonly_hidden");
		if ($sel_read.attr('name') != undefined && $sel_read.attr('name').length > 0) {
			$sel_read.prop('disabled', true);
			if($hide_input.length <= 0) {
				$hide_input = $('<input type="hidden" name="' + $sel_read.attr('name') + '" value="' + $sel_read.val() + '" class="readonly_hidden">');
				$sel_read.after($hide_input);
			}		
			$sel_read.change(function() {
				$hide_input.val($sel_read.val());
			});
		}
	});

	$root.find('.inclone').each(function(){
		var $inclone = $(this),
			$block = $inclone.find('.inclone__block:first'),
			$btn = $inclone.find('.inclone__btn');
		if($block.length)
		{
			var $cloned_block = $block.clone();
			$btn.unbind('click');
			$btn.click(function(){
				var $added_block = $cloned_block.clone();
				$added_block.addClass('deleted_block');
				$added_block.addClass('new_block');
				$added_block.removeClass('inclone__block');
				if($added_block.data('item') != undefined && $added_block.data('item') != '') {
					var item_tmp = $added_block.data('item'),
						item_max = 0;
					$inclone.find('[data-item]').each(function() {
						var item_val = parseInt($(this).data('item'));
						item_max = item_val > item_max ? item_val : item_max;
					});
					item_max++;
					$added_block.attr('data-item', item_max);
					$added_block.data('item', item_max);
					$added_block.find('[name]').each(function() {
						$(this).attr('name', $(this).attr('name').replace(item_tmp, item_max));
					});
				}
				$inclone.find('.inclone__block:last').before($added_block);
				$added_block.show();
				$added_block.run();
				$btn.trigger('addClone', [$added_block]);
				return false;
			});

			if ($block.css('display') != "none") {
				$block.hide();
				$btn.click();
			}
		}
	});

	$root.find('.deleted_block_btn').each(function(){
		var $btn = $(this),
			$block = $btn.parents('.deleted_block:first'),
			$block_parent = $block.parents('.inclone:first');
		if($block.length)
		{
			$btn.click(function(){
				$btn.trigger('deleteBlock', [$block]);
				$block.remove();
				$block_parent.trigger('deleteBlockAfter');
				return false;
			});
		}
	});

	$root.find('select.sel_section').each(function(){
		var $parent = $(this),
			parent_id = $parent.data('sect'),
			$children = $root.find('select.sel_subsection'+(parent_id != undefined && parent_id.length > 0 ? '[data-subsect="'+parent_id+'"]' : ''));
		if($children.length)
		{
			$parent.change(function(){
				$children.find('option[value!=""]').hide();
				$children.find('option[data-parent="'+$parent.val()+'"]').show();
			});
			$parent.change();
		}
	});

	$root.find('.ajax_select').each(function(){
		var $select = $(this),
			select2_params = {
				ajax: {
					url: $select.data('url'),
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							page: params.page
						};
					},
					processResults: function (data, page) {
						if($select.hasClass('ajax_select_add') && typeof(addSelect2ResultFunc) == 'function') {
							var addResult = addSelect2ResultFunc(page.term)
							data.items = data.items.concat(addResult);
						}
						/*if(data.items.length == 0)
						{
							var input = $select.next().find('input');
							var parent
							if(input)
							{
								input.val('');
							}
							//$select.select2("val", "");
						}*/
						
						$select.data('term', page.term);
						return {
							results: data.items
						};
					},
					cache: true
				},
				placeholder: $select.attr('placeholder'),
				escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				minimumInputLength: 1,
				language: "ru"
			};

		if ($select.data('add') != undefined) {
			$select.wrap('<div class="input-group" style="width:100%;"></div>');
		}

		if($select.val() == null || !$select.val().length) $select.html("");
		if($select.filter( ":not([multiple])" ).length)
		{
			select2_params['multiple'] = true;
			select2_params['maximumSelectionLength'] = 1;
		}
		$select.select2(select2_params);

		if ($select.data('add') != undefined) {
			var $add_btn = $('<span class="input-group-addon glyphicon glyphicon-plus"></span>');
			$add_btn.click(function() {
				$root.loader('show');
				$.ajax({
					url: $select.data('add'),
					data: {ajax: 'y'},
					success: function(data) {
						var $modal = $('<div class="modal fade bs-example-modal-lg-checkout" tabindex="-1" data-show="true" role="dialog"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button><h4 class="modal-title"></h4></div><div class="modal-body">'+data+'</div></div></div></div>'),
							$form = $modal.find('form');

						$root.loader('hide');

						$modal.find('.modal-title').html($modal.find('.modal-body h4').html());
						$modal.find('.modal-body h4').remove();
						$modal.modal({
							show: true,
							backdrop: false
						});
						$modal.run();
						$modal.on('hidden.bs.modal', function (e) {
							$modal.remove();
						});

						$form.submit(function() {
							var form_data = $form.serialize(),
								form_url = $form.attr('action');

							form_data += (form_data == '' ? '' : '&') + 'apply=Y';
							form_url += (form_url == '' ? '?' : '&') + 'ajax_mode=Y';

							$modal.find('.modal-content').loader('show');

							$.ajax({
								url: form_url,
								data: form_data,
								method: 'post',
								success: function(data_req) {
									$modal.find('.modal-content').loader('hide');
									if (data_req.ITEM.CODE > 0) {
										$select.html('');
										$select.append( $('<option value="'+data_req.ITEM.CODE+'" selected>'+data_req.ITEM.TITLE+'</option>'));
										$select.change();
										//$select.select2({data: [{id : data_req.ITEM.ID, text: data_req.ITEM.SECTION_NAME}]});
										//$select.val([data_req.ITEM.ID]).trigger("change");
										$modal.remove();
									}
								}
							});
							return false;
						});
					}
				});
				return false;
			});
			$select.parent().append($add_btn);
		}
		$select.trigger('select2:create', [$select.data('select2')]);
		$select.data('select2').on('results:all', function (params) {
			if(params.data.results.length == 1)
			{
				this.trigger('results:select');
			}
		});

		$select.data('select2').$selection.run();
		$select.on("select2:select", function (e) { 
			$select.data('select2').$selection.find('.scanner_detect').removeClass('scanner_detect');
			$select.data('select2').$selection.run(); 
			$select.parents('.product_block').find('.product_quantity').focus().select();
			$select.parents('.product_block').find('input[readonly!=""][readonly!="readonly"][disabled!=""][disabled!="disabled"]:eq(2)').focus().select();

		});
		if($select.data('style') != undefined) {
			$select.data('select2').$container.attr('style', $select.data('style'));
		}
	});
	//<span class="input-group-addon btn glyphicon glyphicon-plus"></span>

	$root.find('[data-info_url]').each(function(){
		var $select = $(this),
			$product_block = $select.parents('.product_block, .load_info:first'),
			$loader = $select.parents('.loader:first');
		$select["prevValue"] = "";
		$select.on("keyup", function(){
			var count = $select.val().length;
			if(count == 10 || count == 12)
				$select.trigger('change');
		});
		$select.on("change", function(){
			if($select.val() > 0 && String($select.val()) != String($select["prevValue"]))
			{
				var user_id = parseInt($('.price_user_id:first').val()),
					term = $select.data('term');
					bid = parseInt($product_block.find('.product_basket_id').val());
				$select["prevValue"] = $select.val();
				$product_block.loader('show');
				$.ajax({
					url: $select.data('info_url'),
					data: {
						pid: parseInt($select.val()),
						uid: user_id,
						term: term,
						bid: bid,
					},
					success: function(data){
						var $focus = $(':focus');
						$.each(data, function(index, value) {
							$product_block.find('[data-info_set="'+index+'"]').each(function(){
								if($(this).val() == undefined || $(this).val() == "" || $(this).hasClass('info_slave')) {
									if ($(this).hasClass('info_slave') && value != undefined && value != '')
										$(this).prop('readonly', true);
									$(this).val(value).change();
								}
							});
						});
						$product_block.find('.product_price').each(function(){
							if(parseFloat($(this).val()) > 0)
								$(this).prop('readonly', true);
							else
								$(this).prop('readonly', false);
						});
						$focus.focus().select();
						if(data.SHELF_LIFE == undefined || data.SHELF_LIFE == "" || data.SHELF_LIFE == 0)
							$product_block.find('.start_date').parent().css('display', 'none');
						else
							$product_block.find('.start_date').parent().css('display', 'table');
						$product_block.loader('hide');
						$select.trigger('load_info', [data]);
					}
				});
			}
		});
		$select.change();
	});

	$root.find('.shop_price').each(function(){
		var $sp = $(this),
			$product_block = $sp.parents('.product_block'),
			$pp = $product_block.find('.purchasing_price'),
			$sp_sel = $product_block.find('.shop_price__sel'),
			$sp_dialog = $product_block.find('.shop_price__dialog'),
			$sp_dialog_pp = $sp_dialog.find('.shop_price__dialog__pp'),
			$sp_dialog_pps = $sp_dialog.find('.shop_price__dialog__pps'),
			$sp_dialog_sps = $sp_dialog.find('.shop_price__dialog__sps'),
			$sp_dialog_ppsm = $sp_dialog.find('.shop_price__dialog__ppsm'),
			$sp_dialog_ppsm_old = $sp_dialog_ppsm.filter('.old'),
			$sp_dialog_ppsm_lst = $sp_dialog_ppsm.filter('.last'),
			$sp_dialog_ppsm_eve = $sp_dialog_ppsm.filter('.eve'),
			$sp_dialog_ppsm_max = $sp_dialog_ppsm.filter('.max'),
			sps = parseFloat($sp_dialog_sps.val()),
			pps = [($pp.val().length > 0 ? parseFloat($pp.val()) : 0)],
			pps_min = 0, pps_max = 0, pps_ave = 0, pps_summ = 0, pps_len = 0, pp = 0, pps_last = pps[0],
			$spp = $sp_dialog.find('.shop_price__dialog__ppp'),
			$spc = $sp_dialog.find('.shop_price__dialog__ppc'),
			$save_btn = $sp_dialog.find('.shop_price__dialog__save');

		var calcPrice = function() {
			var $ppsm = $sp_dialog_ppsm.find('input:checked').parents('.shop_price__dialog__ppsm:first'),
				percent = $spp.val().length > 0 ? parseFloat($spp.val()) : 0;

			if($ppsm.hasClass('old'))
				$spc.val(sps);
			else if($ppsm.hasClass('last'))
				$spc.val(pps_last * (1 + percent / 100));
			else if($ppsm.hasClass('eve'))
				$spc.val(pps_ave * (1 + percent / 100));
			else if($ppsm.hasClass('max'))
				$spc.val(pps_max * (1 + percent / 100));

			$spc.val($spc.val().length > 0 ? Math.ceil(parseFloat($spc.val())*100)/100 : 0);
		};

		$sp_sel.click(function() {
			$sp_dialog_pp.val($pp.val().length > 0 ? parseFloat($pp.val()) : 0);
			$sp_dialog.modal('show');

			sps = parseFloat($sp_dialog_sps.val());
			pps = $sp_dialog_pps.val().length > 0 ? JSON.parse($sp_dialog_pps.val()) : [$sp_dialog_pp.val()];
			pps_min = 0; pps_max = 0; pps_ave = 0; pps_summ = 0; pps_len = 0; pp = 0; pps_last = pps[0];

			for (index = 0, len = pps.length; index < len; ++index) {
				pp = parseFloat(pps[index]);
				//pps_min = (pps_min <= 0 || pp < pps_min) ? pp : pps_min;
				pps_max = pp > pps_max ? pp : pps_max;
				pps_summ += pp;
				pps_len++;
			}
			pps_ave = pps_summ / pps_len;

			$sp_dialog_ppsm_old.find('span').html(sps);
			$sp_dialog_ppsm_lst.find('span').html(pps_last);
			$sp_dialog_ppsm_eve.find('span').html(pps_ave);
			$sp_dialog_ppsm_max.find('span').html(pps_max);

			calcPrice();
		});

		$sp_dialog_ppsm.find('input').change(function(){
			var $ppsm = $(this),
				$ppsm_block = $ppsm.parents('.shop_price__dialog__ppsm:first');
			$sp_dialog_ppsm.find('input').prop('checked', false);
			$ppsm.prop('checked', true);

			calcPrice();
		});

		$spp.keyup(function() {
			calcPrice();
		});

		$save_btn.click(function() {
			$sp.val($spc.val());
		});
	});

	$root.find('.price_user_id').each(function(){
		var $price_user = $(this);
		$price_user.change(function(){
			$root.find('[data-info_url]').each(function(){
				$(this).change();
			});
		});
	});

	$root.find('[data-calc_input]').each(function(){
		var $product_block = $(this).parents('.product_block'),
			$quantity = $product_block.find('[data-calc_input="QUANTITY"]'),
			$price = $product_block.find('[data-calc_input="PRICE"]'),
			$summ = $product_block.find('[data-calc_input="SUMM"]'),
			$pamount = $product_block.find('[data-calc_input="PURCHASING_AMOUNT"]'),
			$pprice = $product_block.find('[data-calc_input="PURCHASING_PRICE"]'),
			$pnds = $product_block.find('[data-calc_input="PURCHASING_NDS"]'),
			$psumm = $product_block.find('[data-calc_input="PURCHASING_SUMM"]'),
			$nds_value = $product_block.find('.nds_value');
		if($quantity.length > 0)
		{
			$quantity.on("blur", function(){
				if($quantity.val().length <= 0)
					$quantity.val(1).change();
			});
			$quantity.on("keyup", function(event){
				if(event.keyCode==13) {
					if($('.product_block .select2-hidden-accessible:last').val() > 0)
					{
						$('.inclone__btn.scanner_detection_add').click();
						$('.product_block .select2-search__field:last').focus();
					}
				}
			});
		}
		
		$(this).on("change keyup focus", function(){
			if (jQuery.inArray($(this).data('calc_input'), ['QUANTITY', 'PRICE', 'SUMM']) > -1) {
				var quantity = parseFloat($quantity.val().replace(/,/g,'.')),
					price = parseFloat($price.val().replace(/,/g,'.'));
				if(quantity >= 0 && price >= 0)
					$summ.each(function(){$(this).val(Math.round(quantity*price*100)/100).trigger('calc_summ');});
			}
			else if (jQuery.inArray($(this).data('calc_input'), ['PURCHASING_AMOUNT', 'PURCHASING_PRICE', 'PURCHASING_NDS', 'PURCHASING_SUMM']) > -1) {
				var pamount = parseFloat($pamount.val().replace(/,/g,'.')),
					pnds = parseFloat($pnds.val().replace(/,/g,'.')),
					psumm = parseFloat($psumm.val().replace(/,/g,'.'));
				if(isNaN(pamount)) pamount = 0;
				if(isNaN(pnds)) pnds = 0;
				if(isNaN(psumm)) psumm = 0;
				if(pamount > 0 && psumm > 0) {
					$pprice.each(function(){$(this).val(Math.ceil(psumm*100/(100+pnds)/pamount*100)/100);});
					$nds_value.each(function(){$(this).val(psumm*(pnds/100));});
				}
			}
		});
		//$(this).change();
	});

	$root.find('.calc_summ__elem').each(function(){
		var $elem = $(this),
		$block = $elem.parents('.calc_summ');
		if($block.length <= 0)
			$block = $('body');
		var $result = $block.find('.calc_summ__result');

		$elem.on("change keyup focus calc_summ", function(){
			summ = 0;
			$block.find('.calc_summ__elem').each(function(){
				var elem_val = parseFloat($(this).val());
				if(isNaN(elem_val)) elem_val = 0;
				summ += elem_val;
			});
			$result.val(Math.round(summ*100)/100);
		});
	});

	$root.find('.frm_rfrsh').each(function(){
		var $select = $(this),
			$form = $select.parents('form');

		$form.find("[data-rfrsh_title]").each(function(){
			$(this).attr("data-rfrsh_title_crnt", $(this).html());
		});

		$select.change(function(){
			$form.find("[data-rfrsh_id = "+$select.val()+"]").prop('disabled', false).show();
			$form.find("[data-rfrsh_id][data-rfrsh_id != "+$select.val()+"]").prop('disabled', true).hide();
			$form.find("[data-rfrsh_title][data-rfrsh_title_id = "+$select.val()+"]").each(function(){
				$(this).html($(this).data('rfrsh_title'));
			});
			$form.find("[data-rfrsh_title_id][data-rfrsh_title][data-rfrsh_title_id != "+$select.val()+"]").each(function(){
				$(this).html($(this).data('rfrsh_title_crnt'));
			});
		});
		$select.change();
	});

	/**/
	$root.find('input:not(.scanner_detect)').each(function(){
		$(this).addClass('scanner_detect');
		if ($('.ajax_select').length > 0)
			$(this).scannerDetection({
				endChar:[13],
				minLength:5,
				onComplete: function(data){
					$(this).scannerFunc(data);
				},
				onError: function(data){
					$(this).scannerError(data);
				}	
			});
	});

	$root.find('.save_alert').each(function() {
		$(this).change(function() {
			window.onbeforeunload = function(e) {
				return 'Эта страница просит вас подтвердить, что вы хотите уйти — при этом введённые вами данные не сохранятся.';
			};
		});
		$(this).submit(function() {
			window.onbeforeunload = null;
		});
	});

	$(".phone_mask").each(function(){
		var phone = $(this).val().replace(/[^\d]/g, '');
		if (phone.length > 10)
			$(this).val(phone.substr(-10));
		$(this).mask("8 (999) 999-99-99");
	});

	$root.find('.piechart__data').each(function() {
		var $data = $(this),
			$piechart = $data.parents('.piechart:first'),
			$labels = $data.find('.piechart__data__label'),
			$piechart_parent = $piechart.parent();

		$piechart.unbind();

		var piedata = [];
		$labels.each(function() {
			var $label = $(this);
			piedata.push({
				label: $label.html(), 
				data: [[1,$label.data('val')]], 
				url: $label.data('url')
			});
		});

		var gotosection = function(url = '', block_id = '') {
			console.log(url);
			console.log(block_id);
			var $section_block = block_id != '' ? $('#'+block_id) : $piechart_parent;
			$section_block.loadByURL(url);
		}

		$.plot($piechart, piedata, {
			series: {
				pie: {
					show: true,
					radius: 1,
					innerRadius: 0.3,
					label: {
						show: true,
						radius: 2/3,
						formatter: labelFormatter,
						threshold: 0.1
					}
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			legend: {
				show: false
			}
		});

		var previousPoint = null;
		$piechart.tooltip({title: '', html: true});
		$piechart.bind("plothover", function (event, pos, obj) {
			if(obj) {
				if (previousPoint != obj.seriesIndex) {
					previousPoint = obj.seriesIndex;
					$piechart.data('bs.tooltip').options.title = obj.series.label;
					$piechart.tooltip('show');
				}
			}
			else {
				previousPoint = null;
			}
		});

		$piechart.bind("plotclick", function(event, pos, obj) {
			if (!obj) return;
			gotosection(obj.series.url, $piechart.data('loadto') != undefined ? $(this).data('loadto') : '');
		});

		$piechart.parent().find('[data-url]').each(function() {
			$(this).click(function() {
				gotosection($(this).data('url'), $(this).data('loadto') != undefined ? $(this).data('loadto') : '');
			});
		});
	});

	$root.find('[data-load_url]').each(function() {
		$(this).loadByURL($(this).data('load_url'));
	});

	$root.find('[data-loadto]').each(function() {
		var $link = $(this),
			url = $link.data('url') != undefined ? $link.data('url') : $link.attr('href'),
			$load_block = $('#'+$link.data('loadto'));
		$link.click(function() {
			$load_block.loadByURL(url);
			return false;
		});
	});

	$root.find('.form_switch').each(function() {
		var $block = $(this),
			$vals = $block.find('.form_switch__vals input'),
			$cases = $block.find('.form_switch__case');
		$vals.each(function(){
			var $val = $(this),
				$case = $cases.filter('[data-case="'+$val.val()+'"]');
			$val.change(function() {
				$cases.prop('disabled', true).hide();
				if($val.prop('checked')) $case.prop('disabled', false).show();
			});
		});
		$vals.first().change();
		$vals.filter(':checked').change();
	});

	$root.find('.price_ins').each(function() {
		var $block = $(this),
			$copy = $block.find('.price_ins__copy'),
			$paste = $block.find('.price_ins__paste');
		$copy.change(function() {
			$paste.val($(this).find('option:selected').data('price')).change();
		});
	});

	$root.find('.pricelist_price[data-pricelist!="y"]').each(function() {
		var $base_price = $(this),
			$product = $base_price.parents('.pricelist, .product_block, .load_info, form').first().find('.pricelist_product'),
			last_price = $base_price.val();
		$base_price.attr('data-pricelist', 'y');
		$base_price.change(function() {
			if (empty(last_price) || empty($product.val()))
			{
				last_price = $base_price.val();
				return false;
			}
			$root.loader('show');
			$.ajax({
				url: $base_price.data('update'),
				data: {ajax: 'y', CODE: $product.val()},
				success: function(data) {
					var $modal = $('<div class="modal fade bs-example-modal-lg-checkout" tabindex="-1" data-show="true" role="dialog"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button><h4 class="modal-title"></h4></div><div class="modal-body">'+data+'</div></div></div></div>'),
						$form = $modal.find('form');

					$root.loader('hide');

					$modal.find('.modal-title').html($modal.find('.modal-body h1').html());
					$modal.find('.modal-body h1').remove();
					$modal.modal({
						show: true,
						backdrop: false
					});
					$modal.run();
					$modal.on('hidden.bs.modal', function (e) {
						$modal.remove();
					});

					$form.submit(function() {
						var form_data = $form.serialize(),
							form_url = $form.attr('action');

						form_data += (form_data == '' ? '' : '&') + 'apply=Y';
						form_url += (form_url == '' ? '?' : '&') + 'ajax_mode=Y';

						$modal.find('.modal-content').loader('show');

						$.ajax({
							url: form_url,
							data: form_data,
							method: 'post',
							success: function(data_req) {
								$modal.find('.modal-content').loader('hide');
								console.log(data_req);
								if (data_req.ERRORS.length == 0)
									$modal.remove();
							}
						});
						return false;
					});
				}
			});
			return false;
		});
	});

	$root.find('input:visible').each(function() {
		$(this).keypress(function(event) {
			if (event.keyCode == 13 && !$(this).is(":button")) {
				return false;
			}
		});
		$(this).keyup(function(event) {
			if (event.keyCode == 13 && !$(this).is(":button")) {
				var $inputs_visible = $('input:visible'),
					input_index = $inputs_visible.index($(this)),
					$next_input = $inputs_visible.eq(input_index + 1);

				if ($next_input != undefined && $next_input.length > 0)
					$next_input.focus();
			}
		});
	});

	$root.find('.list__added_btn').each(function() {
		var $added_btn = $(this),
			$add_btn = $('.list__add_btn'),
			$list_form = $('[name="list_form"]');
		$add_btn.after($added_btn);
		$added_btn.show();
	});

	$root.find("[data-pager]").each(function() {
		var $pager = $(this),
			pagerId = $pager.data('pager'),
			$pagerBlock = $('[data-pager_block="' + pagerId + '"]');

		$pager.on("click", function () {

			if (!$pager.prop('disabled')) {

				$pager.loader('show');
				//$pager.button('loading'); //button.js
				$pager.prop('disabled', true);

				$.ajax({
					url: $pager.attr('href'),
					success: function (data) {
						var $dataContent = $('<div>' + data + '</div>'),
							$curBlock = $dataContent.find('[data-pager_block="' + pagerId + '"]'),
							$curPager = $dataContent.find('[data-pager="' + pagerId + '"]');
							
						if ($.trim($curBlock.text()) == "" ) {
							$pager.remove();
						}
						else {
							$pagerBlock.append($curBlock.html());
							$pagerBlock.run();
							if ($curPager.length > 0) {
								$pager.attr('href', $curPager.attr('href'));
							}
							else
								$pager.remove();
						}

						$pager.loader('hide');
						//$pager.button('reset');
						$pager.prop('disabled', false);
					}
				});

			}

			return false;
		});

		$(window).scroll(function() {
			if ($pager != undefined && $pager.length > 0)
				if($(window).scrollTop() + $(window).height() >= $pager.offset().top)
					$pager.click();
		});
	});
	
	$root.find('input:visible:first').focus();

	if (typeof(loadedModules) != "undefined" && Array.isArray(loadedModules)) {
		loadedModules.forEach(function(loadedModule/*, i, arr*/) {
			loadedModule($root);
		});
	}
};

function labelFormatter(label, series) {
	return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}

if ($('.ajax_select').length > 0)
	$(document).scannerDetection({
		endChar:[13],
		minLength:5,
		onComplete: function(data){
			$(this).scannerFunc(data);
		},
		onError: function(data){
			$(this).scannerError(data);
		}
	});

var scannerTimeOut = new Date;


$.fn.scannerError = function(data){
	data = parseInt(data.replace(/\D+/g,""));

	var $this_obj = this;
	//alert(data);
	$this_obj.val($this_obj.val().replace(data, '')).change();
}

$.fn.scannerFunc = function(data){

	//data = parseInt(data.replace(/\D+/g,""));

	var scannerCurTime = new Date;
	if(scannerCurTime - scannerTimeOut <= 1000) return;

	var $this_obj = $scanner_input = this;

	if(!$this_obj.hasClass('select2-search__field') || $this_obj.parents('.product_block').length <= 0) {
		$this_obj.val($this_obj.val().replace(data, '')).change();
		$scanner_input = $('.product_block .select2-search__field:visible:last');
	}
	/*else if($this_obj.hasClass('select2-search__field')) {
		$this_obj.val(data).change();
	}*/

	var $scanner_select = $scanner_input.parents('.select2-container').siblings('.scanner_detection');
	$scanner_select.trigger('select2:unselect');

	if($this_obj == $scanner_input) {
		//$scanner_select.val(null).trigger("change");
		$.ajax({
			url: $scanner_select.data('url'),
			data: {
				q: data
			},
			success: function(data){
				$scanner_select.select2({
					data: data.items
				});
				$scanner_select.val(data.items[0].id).trigger("change");
			}
		});
	}
	else { 
		if($scanner_select.val() > 0) {
			$('.inclone__btn.scanner_detection_add').click();
			$scanner_input = $('.product_block .select2-search__field:last');
			$scanner_select = $scanner_input.parents('.select2-container').siblings('.scanner_detection');
		}
		$scanner_input.val(data).trigger("change").focus();
	}
};

$.fn.loadByURL = function(url = '') {
	var $load_block = this,
		$window = $(window);

	if(url != '' && url != undefined) {
		$load_block.loader('show');
		$.ajax({
			url: url,
			data:{ajax_link: 'Y'},
			success: function(data) {
				$load_block.loader('hide');
				$load_block.html(data).run();
			}
		});
	}
}

var fixListTable = function() {
	var $table = $('table.fixed_list');
		$thead = $table.find('thead'),
		$tbody = $table.find('tbody'),
		$tfoot = $table.find('tfoot'),
		$pagination = $('.pagination'),
		$bxpanel = $('#bx-panel'),
		$headerwrapper = $('.headerwrapper');
	
	/*$tbody.css({
		'overflow-y': 'auto',
		position: 'absolute',
		height: ($(window).height() - $thead.height() - $headerwrapper.height() - $bxpanel.height()) + "px",
		width: $thead.width() + 15 + "px",
	});
	//$pagination.hide();
	$table.css({
		'margin-bottom': $tbody.height() - 90 + 'px',
	});*/
}

// DOM ready event
$(function(){

	// run scripts in .page block
	$('body').run();

	fixListTable();
	$( window ).resize(fixListTable);
});

function doGetCaretPosition (ctrl) {

	var CaretPos = 0;
	// IE Support
	if (document.selection) {

		ctrl.focus ();
		var Sel = document.selection.createRange ();

		Sel.moveStart ('character', -ctrl.value.length);

		CaretPos = Sel.text.length;
	}
	// Firefox support
	else if (ctrl.selectionStart || ctrl.selectionStart == '0')
		CaretPos = ctrl.selectionStart;

	return (CaretPos);

}


function setCaretPosition(ctrl, pos)
{

	if(ctrl.setSelectionRange)
	{
		//ctrl.focus();
		ctrl.setSelectionRange(pos,pos);
	}
	else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}

function empty(val)
{
	return val == undefined || val == 0 || val == '';
}

