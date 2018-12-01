<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новый товар");?>
<?$arResult["ITEMS"] = array(
	array(
		"SAMPLE" => "Y",
		"ID" => "sample_id",
	),
	/*array(
		"ID" => 1,
		"PRODUCT_ID" => 23,
		"NAME" => "test1",
		"TYPE" => 1,
		"CONNECT" => array(
			array(
				"ID" => 2,
				"PRODUCT_ID" => 24,
				"NAME" => "test2",
			),
			array(
				"ID" => 3,
				"PRODUCT_ID" => 25,
				"NAME" => "test3",
			),
		)
	),
	array(
		"ID" => 2,
		"PRODUCT_ID" => 24,
		"NAME" => "test2",
		"TYPE" => 0,
	),
	array(
		"ID" => 3,
		"PRODUCT_ID" => 25,
		"NAME" => "test3",
		"TYPE" => 0,
	),*/
);?>
<style type="text/css">
#halfviz{
  top:0px;
  z-index:1;
  width: 100%;
  height: 100%;  
}
</style>
<form action="" method="post" id="fabrica_form" class="form-horizontal" autocomplete="off">
	<div id="halfviz" style="height: 300px;">
		<canvas id="viewport"></canvas>
	</div> 
	<div class="fabrica_form__items">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<div class="modal fade form-group fabrica_form__item<?if($arItem["SAMPLE"] == "Y"):?> fabrica_form__item__sample<?endif?> deleted_block" data-item="<?=$arItem["ID"]?>" style="display:none;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Редактирование товара <?/*<button class="glyphicon glyphicon-trash deleted_block_btn deteted_item"aria-hidden="true"></button>*/?></h4>
					</div>

					<div class="modal-body">

						
						<div class="form-group">
							<label class="col-sm-4">Название продукта </label>
							<div class="col-sm-8">
								<input type="hidden" name="ID[<?=$arItem["ID"]?>]" value="<?=$arItem["ID"]?>" class="item_id form-control">
								<input type="hidden" name="PRODUCT_ID[<?=$arItem["ID"]?>]" value="<?=$arItem["PRODUCT_ID"]?>" class="item_product_id form-control">
								<input type="text" name="NAME[<?=$arItem["ID"]?>]" value="<?=$arItem["NAME"]?>" class="item_name form-control"<?if($arItem["PRODUCT_ID"] > 0):?> disabled<?endif?>>
								<label type="button"  data-toggle="buttons" class="btn btn-default item_find<?if($arItem["PRODUCT_ID"] > 0):?> active<?endif?>">find >>></label>
								<select class="form-control product_select item_search" data-url="/fabrica/search_element.php"<?if($arItem["PRODUCT_ID"] <= 0):?> disabled<?endif?>>
									<option value="<?=$arItem["PRODUCT_ID"]?>"  selected="selected"><?=$arItem["NAME"]?></option>
								</select>
							</div>
						</div>

						<div class="form-group" data-toggle="buttons">
							<p>Вид производства</p>
							<label class="btn btn-default<?if($arItem["TYPE"] === 0):?> active<?endif?> item_type_label"><input type="radio" name="TYPE[<?=$arItem["ID"]?>]" value="0"<?if($arItem["TYPE"] === 0):?> checked<?endif?> class="item_type" data-type="type_simple"> существует</label>
							<label class="btn btn-default<?if($arItem["TYPE"] == 1):?> active<?endif?> item_type_label"><input type="radio" name="TYPE[<?=$arItem["ID"]?>]" value="1"<?if($arItem["TYPE"] == 1):?> checked<?endif?> class="item_type" data-type="type_merge"> приготовить</label>
							<label class="btn btn-default<?if($arItem["TYPE"] == 2):?> active<?endif?> item_type_label"><input type="radio" name="TYPE[<?=$arItem["ID"]?>]" value="2"<?if($arItem["TYPE"] == 2):?> checked<?endif?> class="item_type" data-type="type_split"> разделить</label>
							<label class="btn btn-default item_type_label"><input type="radio" name="TYPE[<?=$arItem["ID"]?>]" value="0" class="item_type" data-type="type_separate"> получить</label>
						</div>

						<fieldset class="item_connects" data-type="type_simple"<?if($arItem["TYPE"] != 0):?> disabled style="display:none;"<?endif?>>
							
						</fieldset>

						<fieldset class="item_connects" data-type="type_merge"<?if($arItem["TYPE"] != 1):?> disabled style="display:none;"<?endif?>>
							<div class="form-group">
								<label class="col-sm-4">Получаемая порция </label>
								<div class="col-sm-6">
									<input type="text" name="AMOUNT[<?=$arItem["ID"]?>]" value="<?=$arItem["AMOUNT"]?>" class="form-control">
								</div>
								<div class="col-sm-2">
									<select name="MEASURE[<?=$arItem["ID"]?>]" class="form-control">
										<option>шт</option>
										<option>кг</option>
										<option>л</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Погрешность соотношений </label>
								<div class="col-sm-8">
									<input type="text" name="FAULT_RATIO[<?=$arItem["ID"]?>]" value="<?=$arItem["FAULT_RATIO"]?>" class="form-control">
								</div>
							</div>



							<div class="form-group inclone ">
								<div class="row text-center">
									<div class="col-md-6">Ингридиент</div>
									<div class="col-md-2">Количество ингридиента</div>
									<div class="col-md-1"></div>
									<div class="col-md-2">Процент отходов</div>
									<div class="col-md-1"></div>
								</div>
							<?if($arItem["TYPE"] == 1 && is_array($arItem["CONNECT"])) foreach($arItem["CONNECT"] as $arConnect):?>
								<div class="row deleted_block item_connect">
									<div class="col-md-6">
										<input type="hidden" name="ID[<?=$arItem["ID"]?>][]" value="<?=$arConnect["ID"]?>" class="item_connect__id form-control">
										<input type="hidden" name="PRODUCT_ID[<?=$arItem["ID"]?>][]" value="<?=$arConnect["PRODUCT_ID"]?>" class="item_connect__product_id form-control">
										<input type="text" name="NAME[<?=$arItem["ID"]?>][]" value="<?=$arConnect["NAME"]?>" class="item_connect__name form-control"<?if($arConnect["PRODUCT_ID"] > 0):?> disabled<?endif?>>
										<label type="button"  data-toggle="buttons" class="btn btn-default item_connect__find<?if($arConnect["PRODUCT_ID"] > 0):?> active<?endif?>">find >>></label>
										<select class="form-control product_select item_connect__search ajax_select_add" data-url="/fabrica/search_element.php"<?if($arConnect["PRODUCT_ID"] <= 0):?> disabled<?endif?>>
											<option value="<?=$arConnect["PRODUCT_ID"]?>"  selected="selected"><?=$arConnect["NAME"]?></option>
										</select>
									</div>
									<div class="col-md-2"><input type="text" name="AMOUNT[<?=$arItem["ID"]?>][]" value="<?=$arConnect["AMOUNT"]?>" class="form-control"></div>
									<div class="col-md-1"></div>
									<div class="col-md-2"><input type="text" name="WASTE_RATE[<?=$arItem["ID"]?>][]" value="<?=$arConnect["WASTE_RATE"]?>" class="form-control"></div>
									<div class="col-md-1"><span class="btn btn-default deleted_block_btn">x</span></div>
								</div>
							<?endforeach?>
								<div class="row inclone__block item_connect">
									<div class="col-md-6">
										<input type="hidden" name="ID[<?=$arItem["ID"]?>][]" value="" class="item_connect__id form-control">
										<input type="hidden" name="PRODUCT_ID[<?=$arItem["ID"]?>][]" value="" class="item_connect__product_id form-control">
										<input type="text" name="NAME[<?=$arItem["ID"]?>][]" value="" class="item_connect__name form-control">
										<label type="button"  data-toggle="buttons" class="btn btn-default item_connect__find">find >>></label>
										<select class="form-control product_select item_connect__search ajax_select_add" data-url="/fabrica/search_element.php" disabled>
											<option value=""></option>
										</select>
									</div>
									<div class="col-md-2"><input type="text" name="AMOUNT[<?=$arItem["ID"]?>][]" value="" class="form-control"></div>
									<div class="col-md-1"></div>
									<div class="col-md-2"><input type="text" name="WASTE_RATE[<?=$arItem["ID"]?>][]" value="" class="form-control"></div>
									<div class="col-md-1"><span class="btn btn-default deleted_block_btn">x</span></div>
								</div>
								<span class="btn btn-default inclone__btn">+</span>
							</div>
						</fieldset>

						<fieldset class="item_connects inclone" data-type="type_split"<?if($arItem["TYPE"] != 2):?> disabled style="display:none;"<?endif?>>
							<div class="form-group">
								<label class="col-sm-4">Количество на входе </label>
								<div class="col-sm-6">
									<input type="text" name="AMOUNT[<?=$arItem["ID"]?>]" value="<?=$arItem["AMOUNT"]?>" class="form-control">
								</div>
								<div class="col-sm-2">
									<select name="MEASURE[<?=$arItem["ID"]?>]" class="form-control">
										<option>шт</option>
										<option>кг</option>
										<option>л</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Погрешность соотношений </label>
								<div class="col-sm-8">
									<input type="text" name="FAULT_RATIO[<?=$arItem["ID"]?>]" value="<?=$arItem["FAULT_RATIO"]?>" class="form-control">
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4">Процент отходов </label>
								<div class="col-sm-8">
									<input type="text" name="WASTE_RATE[<?=$arItem["ID"]?>]" value="<?=$arItem["WASTE_RATE"]?>" class="form-control">
								</div>
							</div>

							<div class="form-group inclone">
								<div class="row text-center">
									<div class="col-md-6">Ингридиент</div>
									<div class="col-md-2">Количество ингридиента</div>
									<div class="col-md-1"></div>
									<div class="col-md-2">Соотношение товара</div>
									<div class="col-md-1"></div>
								</div>
							<?if($arItem["TYPE"] == 2 && is_array($arItem["CONNECT"])) foreach($arItem["CONNECT"] as $arConnect):?>
								<div class="row deleted_block item_connect">
									<div class="col-md-6">
										<input type="hidden" name="ID[<?=$arItem["ID"]?>][]" value="<?=$arConnect["ID"]?>" class="item_connect__id form-control">
										<input type="hidden" name="PRODUCT_ID[<?=$arItem["ID"]?>][]" value="<?=$arConnect["PRODUCT_ID"]?>" class="item_connect__product_id form-control">
										<input type="text" name="NAME[<?=$arItem["ID"]?>][]" value="<?=$arConnect["NAME"]?>" class="item_connect__name form-control"<?if($arConnect["PRODUCT_ID"] > 0):?> disabled<?endif?>>
										<label type="button"  data-toggle="buttons" class="btn btn-default item_connect__find<?if($arConnect["PRODUCT_ID"] > 0):?> active<?endif?>">find >>></label>
										<select class="form-control product_select item_connect__search ajax_select_add" data-url="/fabrica/search_element.php"<?if($arConnect["PRODUCT_ID"] <= 0):?> disabled<?endif?>>
											<option value="<?=$arConnect["PRODUCT_ID"]?>"  selected="selected"><?=$arConnect["NAME"]?></option>
										</select>
									</div>
									<div class="col-md-2"><input type="text" name="AMOUNT[<?=$arItem["ID"]?>][]" value="<?=$arConnect["AMOUNT"]?>" class="form-control"></div>
									<div class="col-md-1"></div>
									<div class="col-md-2"><input type="text" name="AMOUNT_RATIO[<?=$arItem["ID"]?>][]" value="<?=$arConnect["AMOUNT_RATIO"]?>" class="form-control"></div>
									<div class="col-md-1"><span class="btn btn-default deleted_block_btn">x</span></div>
								</div>
							<?endforeach?>
								<div class="row inclone__block item_connect">
									<div class="col-md-6">
										<input type="hidden" name="ID[<?=$arItem["ID"]?>][]" value="" class="item_connect__id form-control">
										<input type="hidden" name="PRODUCT_ID[<?=$arItem["ID"]?>][]" value="" class="item_connect__product_id form-control">
										<input type="text" name="NAME[<?=$arItem["ID"]?>][]" value="" class="item_connect__name form-control">
										<label type="button"  data-toggle="buttons" class="btn btn-default item_connect__find">find >>></label>
										<select class="form-control product_select item_connect__search ajax_select_add" data-url="/fabrica/search_element.php" disabled>
											<option value=""></option>
										</select>
									</div>
									<div class="col-md-2"><input type="text" name="AMOUNT[<?=$arItem["ID"]?>][]" value="" class="form-control"></div>
									<div class="col-md-1"></div>
									<div class="col-md-2"><input type="text" name="AMOUNT_RATIO[<?=$arItem["ID"]?>][]" value="" class="form-control"></div>
									<div class="col-md-1"><span class="btn btn-default deleted_block_btn">x</span></div>
								</div>
								<span class="btn btn-default inclone__btn">+</span>
							</div>

						</fieldset>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
					</div>
				</div>
			</div>
		</div>
	<?endforeach?>
	</div>
</form>
<?$APPLICATION->AddHeadScript("/local/modules/yadadya.shopmate/install/js/arbor.js");?>
<?$APPLICATION->AddHeadScript("/local/modules/yadadya.shopmate/install/js/graphics.js");?>
<?$APPLICATION->AddHeadScript("/local/modules/yadadya.shopmate/install/js/renderer.js");?>
<?$APPLICATION->AddHeadScript("/local/modules/yadadya.shopmate/install/js/fabrica.js");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>