$(function(){

	$fabrica = $('#fabrica_form').fabrica_form();
	//$fabrica.returnNodes();

});

$.fn.fabrica_form = function() {

	var $form = this,
		$halfviz = $form.find('#halfviz'),
		$viewport = $halfviz.find('#viewport'),
		$items_block = $form.find('.fabrica_form__items'),
		$item_sample = $items_block.find('.fabrica_form__item.fabrica_form__item__sample:first').clone(),
		items_list = {},
		network = {edges: {}, nodes: {}},
		type_list = {
			simple: 'type_simple', //простой
			merge: 'type_merge', //приготовить
			split: 'type_split', //разделить
			separate: 'type_separate' //получить
		},
		label_colors = {
			hide: 'white', //скрытый
			head: 'orange', //основной товар
			new: 'red', //новый неопределенный
			isset: 'gray', //существующий
			split: 'blue', //разделение
			merge: 'green', //получение
			separate: 'brown' //новый, полученный разделением
		},
		new_counter = 0,
		counter_list = [];

	$.each(label_colors, function(index, color) {
		$('.fc_'+index).css('backgroundColor', color);
	});

	trace = arbor.etc.trace
	objmerge = arbor.etc.objmerge
	objcopy = arbor.etc.objcopy

	sys = arbor.ParticleSystem(1000, 6000, 100, true, 25, 0.01, 0.1)

	// The parameters and their defaults are:

	// repulsion 1,000 the force repelling nodes from each other
	// stiffness 600 the rigidity of the edges
	// friction 0.5 the amount of damping in the system
	// gravity false an additional force attracting nodes to the origin
	// fps 55 frames per second
	// dt 0.02 timestep to use for stepping the simulation
	// precision 0.6 accuracy vs. speed in force calculations
	// 	(zero is fast but jittery, one is smooth but cpu-intensive)

	sys.renderer = Renderer("#viewport") // our newly created renderer will have its .init() method called shortly by sys...
	sys.screenPadding(20)

	var that = {
		init: function(){
			var clear = true,
				$product;
			$item_sample.removeClass('fabrica_form__item__sample');
			$items_block.find('.fabrica_form__item__sample').remove();
			$items_block.find('.fabrica_form__item').each(function(){
				//items_list.push($(this));
				var $item = that.addItem($(this));
				if(clear)
					$product = $item;
				clear = false;
			});

			if(clear)
				$product = that.addItem();
			 
			that.updateGraph();

			$(window).resize(that.resize);
			that.resize();

			that.interface();

			if(clear)
				$product.modal('show');
			
			return that
		},
		addItem: function($item_added) {
			var params = {};

			if(!(typeof($item_added) === 'object' && $item_added.jquery != undefined)) {
				if(typeof($item_added) === 'object')
					params = $item_added;
				var $item_added = $item_sample.clone(),
					sample_id = $item_added.data('item'),
					sample_re = new RegExp(sample_id, 'g'),
					$item_id = $item_added.find('.item_id');
				$item_added.appendTo($items_block);
				while($.inArray('n'+new_counter, counter_list) != -1) {
					new_counter++;
				}
				$item_id.val('n'+new_counter);
				$item_added.html($item_added.html().replace(sample_re, $item_id.val())).data('item', $item_id.val());
				if(params.product_id != undefined)
					$item_added.find('.item_product_id').val(params.product_id);
				if(params.name != undefined)
					$item_added.find('.item_name').val(params.name);
				if(params.measure != undefined)
					$item_added.find('.item_measure').val(params.measure);
				if(params.type != undefined)
					$item_added.find('.item_type[data-type='+params.type+']').prop('checked', true).attr('checked', 'checked').change().click();
			}

			var item_id = $item_added.find('.item_id').val();

			that.itemInterface($item_added);

			if(params.connects != undefined)
					that.addConnect($item_added, params.connects);
			
			counter_list.push(item_id);
			items_list[item_id] = $item_added;
			//that.updateGraph();
			return $item_added;
		},

		addConnect: function($item_added, connects) {
			var type = $item_added.find('.item_type:checked').data('type');
			if(type == undefined) type = $item_added.find('.item_type_label.active .item_type').data('type');
			var $connect_type = $item_added.find('.item_connects[data-type='+type+']'),
				$connect = $connect_type.find('.item_connect:last'),
				$add_connect = $connect_type.find('.inclone__btn:first');
			$.each(connects, function(index, connect) {
				if(connect.id != undefined || connect.product_id != undefined || connect.name != undefined) {
					if(
						$connect.find('.item_connect__id').val().length > 0 || 
						$connect.find('.item_connect__product_id').val().length > 0 || 
						$connect.find('.item_connect__name').val().length > 0
					) {
						$add_connect.click();
						$connect = $connect_type.find('.item_connect:last');
					}
					if(connect.id != undefined) $connect.find('.item_connect__id').val(connect.id);
					if(connect.product_id != undefined) $connect.find('.item_connect__product_id').val(connect.product_id);
					if(connect.name != undefined) $connect.find('.item_connect__name').val(connect.name);
				}
			});
		},
			
		resize:function(){
			var w = $halfviz.width()
			var h = $halfviz.height()
			var _canvas = $viewport.get(0)

			_canvas.width = w
			_canvas.height = h
			sys.screenSize(w, h)

			sys.renderer.redraw()
		},

		updateGraph: function(){
			var items = [],
				connect = [],
				counter = 0,
				changes = false;
			network = {edges: {}, nodes: {}};
			do {
				items = [];
				//connect = [];
				counter = 0;
				changes = false;
				$.each(items_list, function(index, $item) {
					//console.log(index);
					counter++;
					$item.find('.item_type_label.active .item_type').prop('checked', true).change().click();
					var item = {
							id: $item.find('.item_id').val(),
							product_id: $item.find('.item_product_id').val(),
							name: $item.find('.item_name').val(),
							type: $item.find('.item_type:checked').data('type'),
							color: label_colors.new
						};

					if(item.type == undefined) item.type = $item.find('.item_type_label.active .item_type').data('type');
					if(item.type == undefined) item.type = type_list.simple;


					if(counter == 1) {
						item.color = label_colors.head;
					}
					else {
						if(item.product_id.length > 0)
							item.color = label_colors.isset;
						else
							item.color = label_colors.new;

						if(item.type == type_list.split)
							item.color = label_colors.split;
						else if(item.type == type_list.separate)
							item.color = label_colors.separate;
						else if(item.type == type_list.merge)
							item.color = label_colors.merge;
					}

					if(item.type == type_list.merge || item.type == type_list.split) {
						$item.find('.item_connects .item_connect:not(.item_connects:disabled .item_connect)').each(function(){
							var $item_connect = $(this),
								$connect_id = $item_connect.find('.item_connect__id'),
								connect_id = $connect_id.val(),
								connect_product_id = $item_connect.find('.item_connect__product_id').val();
								connect_name = $item_connect.find('.item_connect__name').val(),
								connect_measure = $item_connect.find('.measure_input').val();
							if(!(connect_id.length > 0) && (connect_name.length > 0)) {
								changes = true;
								var $item_added = that.addItem({
									product_id: connect_product_id,
									name: connect_name,
									measure: connect_measure
								});
								connect_id = $item_added.find('.item_id').val();
								$connect_id.val(connect_id);
							}
							if(connect_id.length > 0) {
								if(item.type == type_list.merge)
									connect.push({from: connect_id, to: item.id});
								else if(item.type == type_list.split)
									connect.push({from: item.id, to: connect_id});
							}
						});
					}
					else if(item.type == type_list.separate) {
						changes = true;
						var $item_added = that.addItem({
							type: type_list.split,
							connects: [
								{
									id: item.id,
									product_id: item.product_id,
									name: item.name,
								}
							]
						});
						item.type = type_list.simple;
						//console.log($item_added.find('.item_type:checked').data('type'));
						$item.find('.item_type[data-type='+item.type+']').prop('checked', true).change().click();
						connect_id = $item_added.find('.item_id').val();
							connect.push({from: connect_id, to: item.id});
						//$item_added.modal('show');
					}
					
					$item.find('.item_type[data-type='+item.type+']').click();
					items.push(item);
					// items[item.id] = item;
				});
			} while(changes);
			
			if(items.length == 1)
				network.nodes['NULL'] = {color: 'white'};

			$.each(connect, function(index, cn) {
				if(typeof(network.edges[cn.from]) != 'object') network.edges[cn.from] = {};
				//console.log(cn);
				type = type_list.simple;
				$.each(items, function(index, item) {
					if(item.id == cn.from) {
						type = item.type;
						return false;
					}
				});
				if(type == type_list.split) {
					$.each(items, function(index, item) {
						if(item.id == cn.to) {
							items[index].color = label_colors.separate;
							return false;
						}
					});
				}

				//console.log(type);
				network.edges[cn.from][cn.to] = {directed: true};
			});

			addSelect2Result = [];

			$.each(items, function(index, item) {
				network.nodes[item.id] = {color: item.color, label: (item.name.length > 0 ? item.name : "noname")};
				if(item.name.length > 0)
					addSelect2Result.push({id: 'node_'+item.id, text: '* '+item.name});
			});
			//console.log(addSelect2Result);

			$.each(network.nodes, function(nname, ndata){
				if (ndata.label===undefined) ndata.label = nname
			})
			sys.merge(network)
			// console.log(network);

			that.resize()

			// console.log(network);
		},

		interface: function(){
			that.nodeClick();
			that.edgeAdd();
			that.changeType();
		},

		itemInterface: function($item_added){
			$item_added.find('.product_select:not(.ajax_select)').addClass('ajax_select');
			$item_added.run();

			//переключение Вида производства
			var $type_inputs = $item_added.find('input[data-type]'),
				$type_blocks = $item_added.find('[data-type]:not(input[data-type])');
			$type_inputs.change(function(){
				$type_blocks.hide().attr('disabled', 'disabled');
				$type_blocks.filter('[data-type='+$(this).data('type')+']').removeAttr('disabled').show();
			});

			//обновление графа при закрытии окна редактора
			$item_added.on('hidden.bs.modal', function (e) {
				that.updateGraph();
				//that.updateGraph();
			});

			//поиск по товарам
			that.productSearch($item_added, 'item');
			$item_added.find('.item_connect').each(function() {
				that.productSearch($(this), 'item_connect_');

			});
			$item_added.find('.inclone__btn').bind('addClone', function(e, $item_connect) {
				//that.itemInterface($item_connect);
				that.productSearch($item_connect, 'item_connect_');
			});

			that.reMeasure($item_added);		
			/*$item_added.find('.deleted_block_btn.deteted_item').bind('deleteBlock', function(e, $item_delete) {
				that.updateGraph();
				that.updateGraph();
				//alert();
				$('.modal-backdrop').remove();
			});*/
			/*$select.data('select2').dataAdapter.processResults = function (data, page) {
				data.items.push({id: 365, text: 'year'});
				return {
					results: data.items
				};
			};*/
		},

		productSearch: function($item, prefix) {
			var $id = $item.find('.'+prefix+'_id'),
				$product_id = $item.find('.'+prefix+'_product_id'),
				$name = $item.find('.'+prefix+'_name'),
				$find = $item.find('.'+prefix+'_find'),
				$search = $item.find('.'+prefix+'_search'),
				searchMinWidth = 200;
			var prodSelectTab = function($name, $search) {
				if($search.prop("disabled") == true) {
					$name.css({'width': searchMinWidth, 'min-width': searchMinWidth});
					$search.data('select2').$container.css({'width': 30, 'min-width': 0});
					$name.show();
					$search.data('select2').$container.hide();
					// $search.css({'width': '10%', 'min-width': 0});
				}
				else {
					$name.css({'width': 30, 'min-width': 0});
					$search.data('select2').$container.css({'width': searchMinWidth, 'min-width': searchMinWidth});
					//$search.css({'width': '80%', 'min-width': searchMinWidth});
					$name.hide();
					$search.data('select2').$container.show();
				}
			};
			prodSelectTab($name, $search);
			$find.click(function(){
				if($search.prop("disabled") == true) {
					// $name.prop("disabled", true);
					$search.prop("disabled", false).focus();
					$product_id.val($search.val());
					$name.val($search.find('[value='+$search.val()+']').html()).change();
				}
				else {
					$search.prop("disabled", true);
					// $name.prop("disabled", false).focus();
					$name.focus();
					$product_id.val('');
					$name.val('').change();
				}
				prodSelectTab($name, $search);
			});
			$search.change(function() {
				if($search.val() != null) {
					var search_id = $(this).val().toString();
					if((i = search_id.indexOf('node_', 0)) > -1) {
						$id.val(search_id.substr(5, 100));
					}
					else
						$product_id.val(search_id);
					$name.val($search.find('[value='+search_id+']').html()).change();
				}
			});
			/*if($item.hasClass('item_connect'))
			{
				$search.data('select2').dataAdapter.processResults = function (data, page) {
					alert();
					data.items.push({id: 365, text: 'year'});
					return {
						results: data.items
					};
				};
			}*/
		},

		reMeasure: function($item_added) {
			var $item_id = $item_added.find('.item_id'),
				$item_product_id = $item_added.find('.item_product_id'),
				$item_name = $item_added.find('.item_name'),
				$item_measure = $item_added.find('.item_measure'),
				$item_measure_slave = $item_added.find('.item_measure__slave');

			//смена ед. измерения у компонентов при разделении
			$item_added.find('.measure_split').change(function() {
				$item_added.find('.measure_separate').val($(this).val()).change();
			});
			$item_added.find('.measure_split').change();

			//обновление названий и ед. измерения
			$item_name.change(function() {
				$items_block.each(function() {
					$(this).find('.item_connects[data-type="' + type_list.merge + '"] .item_connect, .item_connects[data-type="' + type_list.split + '"] .item_connect').each(function() {
						if($(this).find('.item_connect__id').val() == $item_id.val()) {
							$(this).find('.item_connect__product_id').val($item_product_id.val());
							$(this).find('.item_connect__name').val($item_name.val());
						}
					});
				});

				if($item_product_id.val().length > 0) {
					$item_measure.prop('readonly', true).attr('readonly', 'readonly');
					$item_measure.prop('disabled', true);
				}
				else {
					$item_measure.prop('readonly', false).removeAttr('readonly');
					$item_measure.prop('disabled', false);
				}
			});

			$item_measure.change(function() {
				$item_measure_slave.val($item_measure.val()).change();
				$items_block.each(function() {
					$(this).find('.item_connects[data-type="' + type_list.merge + '"] .item_connect').each(function() {
						if($(this).find('.item_connect__id').val() == $item_id.val()) {
							$(this).find('.measure_translate__from_unit').val($item_measure.val()).change();
						}
					});
				});
			});

			$item_name.change();
			$item_measure.change();

			$item_added.find('.item_connects[data-type="' + type_list.split + '"] .item_connect, .item_connects[data-type="' + type_list.merge + '"] .item_connect').each(function() {
				that.reMeasureConnectChange($(this));
			});
			$item_added.find('.item_connects[data-type="' + type_list.split + '"] .inclone__btn, .item_connects[data-type="' + type_list.merge + '"] .inclone__btn').bind('addClone', function(e, $item_connect) {
				that.reMeasureConnectChange($item_connect);
			});
		},

		reMeasureConnectChange: function($item_connect) {
			var $connect_measure = $item_connect.find('.measure_input'),
				$measure_translate_from = $item_connect.find('.measure_translate__from_unit'),
				$measure_translate_to = $item_connect.find('.measure_translate__to_unit'),
				$translate_block = $item_connect.find('.measure_translate'),
				$item_id = $item_connect.find('.item_connect__id'),
				$item_product_id = $item_connect.find('.item_connect__product_id');

			$connect_measure.change(function() {
				$measure_translate_to.val($connect_measure.val());
				if($measure_translate_from.val() != $measure_translate_to.val() && ($item_product_id.val().length > 0 || $item_id.val().length > 0)) {
					$translate_block.show();
				}
				else {
					$translate_block.hide();
				}
			});

			$measure_translate_from.change(function() {
				if($measure_translate_from.val() != $measure_translate_to.val()) {
					$translate_block.show();
				}
				else {
					$translate_block.hide();
				}
			});

			$connect_measure.change();
		},

		nodeClick: function() {
			$viewport.bind('nodeClick', function(e, point) {
				// console.log(items_list);
				items_list[point.name].modal('show');
				// console.log(point)
			});
		},

		edgeAdd: function() {
			$viewport.bind('edgeAdd', function(e, from, to) {
				// console.log(from)
				// console.log(to)
			});
		},

		changeType: function() {
			// console.log(items_list);
		}/*,

		returnNodes: function() {
			alert();
		}*/
	}

	return that.init();
}