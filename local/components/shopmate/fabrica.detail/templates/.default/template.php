<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
use Yadadya\Shopmate\Components\Template,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<?if($_REQUEST["successMessage"] == "ADD") $arResult["MESSAGE"] = Loc::getMessage("MESSAGE_ADD");?>
<?if($_REQUEST["successMessage"] == "UPDATE") $arResult["MESSAGE"] = Loc::getMessage("MESSAGE_UPDATE");?>
<?if (!empty($arResult["ERRORS"])):?>
	<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
<?endif;
if (strlen($arResult["MESSAGE"]) > 0):?>
	<?ShowNote($arResult["MESSAGE"])?>
<?endif?>
<style type="text/css">
#halfviz{
  top:0px;
  z-index:1;
  width: 100%;
  height: 100%;  
}
</style>
<div class="panel panel-body">
	<form action="<?=POST_FORM_ACTION_URI?>" method="post" id="fabrica_form" class="form-horizontal save_alert fabrica_form fabrica_form__simple" autocomplete="off">
	<?if($_REQUEST["iframe"] != "y"):?>
		<div class="mb30">
			<h1><?if($arParams["ID"] <= 0):?><?=Loc::getMessage("TITLE_ADD", array("#SECTION_NAME#" => Loc::getMessage("SITE_SECTION_NAME")))?><?else:?><?=Loc::getMessage("TITLE_UPDATE", array("#SECTION_NAME#" => Loc::getMessage("SITE_SECTION_NAME")))?><?endif?></h1>
		</div>
	<?endif?>
		<div id="halfviz" style="height: 300px; margin: 3px; border: 3px solid; padding: 10px; display: none;">
			<span class="btn btn-warning pull-right fabrica_form__item_add" style="position: absolute; right: 0px; margin-right: 30px;">Добавить элемент в рецепт</span>
			<canvas id="viewport" style="background-color:white;"></canvas>
		</div> 
		<?=bitrix_sessid_post()?>
		<?$propertyID = "ITEMS";
		$arProperty = $arResult["PROPERTY_LIST"][$propertyID];?>
		<div class="fabrica_form__items <?=$arProperty["CLASS"]?>">
		<?//print_p($arResult["ITEM"][$propertyID]);
		foreach($arResult["ITEM"][$propertyID] as $keyItem => $arItem):
			$arItem["CONNECT"][] = array("SAMPLE" => "Y", "ID" => "new_id");?>
			<div class="<?if ($keyItem != 1):?>modal fade <?endif?>form-group fabrica_form__item<?if($arItem["SAMPLE"] == "Y"):?> fabrica_form__item__sample<?endif?> deleted_block<?/* load_info*/?>" data-item="<?=$arItem["ID"]?>"<?if ($keyItem == 1):?>style="margin:0;"<?else:?>style="display:none;"<?endif?>>
				<?/*<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">Редактирование товара</h4>
						</div>

						<div class="modal-body">*/?>

							<table class="table mb30" style="position: absolute; top: 236px; width: calc(100% - 30px);">
								<tr>
									<td class="col-sm-3">
										<div style="display: inline-flex;">
											<?$propID = "ID";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

											<?$propID = "PRODUCT_ID";
											Template::ShowInput($propID, array_merge(
													$arProperty["PROPERTY_LIST"][$propID],
													array("PROPERTY_TYPE" => "H", "CLASS" => "item_product_id")
												), $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

											<?$propID = "NAME";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

											<label type="button"  data-toggle="buttons" class="btn btn-default item_find<?if($arItem["PRODUCT_ID"] > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

											<?$propID = "PRODUCT_ID";
											Template::ShowInput(
												"",
												array_merge(
													$arProperty["PROPERTY_LIST"][$propID],
													array(
														"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
														"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
														"ENUM" => $arItem[$propID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["ENUM"] : array($arItem[$propID] => $arItem["NAME"])
													)
												), 
												$arItem[$propID]
											);?>
										</div>
									</td>
									<td class="col-sm-2">
										<?$propID = "MEASURE";
										Template::ShowInput(
											$propID, 
											$arProperty["PROPERTY_LIST"][$propID], 
											$arItem[$propID], 
											$propertyID."[".$arItem["ID"]."]"
										);?>
									</td>
								</tr>
							</table>
							<?/*<h4>Параметры производства продукта</h4>
							<div class="row mb30">
								<label class="control-label col-lg-3">Придумайте название</label>
								<div class="col-lg-9">
									<div style="display: inline-flex;">

										<?$propID = "ID";
										Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

										<?$propID = "PRODUCT_ID";
										Template::ShowInput($propID, array_merge(
												$arProperty["PROPERTY_LIST"][$propID],
												array("PROPERTY_TYPE" => "H", "CLASS" => "item_product_id")
											), $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

										<?$propID = "NAME";
										Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

										<label type="button"  data-toggle="buttons" class="btn btn-default item_find<?if($arItem["PRODUCT_ID"] > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

										<?$propID = "PRODUCT_ID";
										Template::ShowInput(
											"",
											array_merge(
												$arProperty["PROPERTY_LIST"][$propID],
												array(
													"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
													"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
													"ENUM" => $arItem[$propID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["ENUM"] : array($arItem[$propID] => $arItem["NAME"])
												)
											), 
											$arItem[$propID]
										);?>
									</div>
								</div>
							</div>

							<div class="row mb30">
								<label class="control-label col-lg-3">Единица измерения</label>
								<div class="col-lg-9">
									<?$propID = "MEASURE";
									Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
								</div>
							</div>*/?>

							<h4>Выберите вид производства</h4>
							<div class="mb20">
								<div class="btn-list" data-toggle="buttons">
									<?$propID = "TYPE";
									Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
								</div>
							</div>

							<fieldset class="item_connects load_info" data-type="type_simple"<?if($arItem["TYPE"] != 0):?> disabled style="display:none;"<?endif?>>

								<h4>Выберите товар</h4>
								<table class="table mb30">
									<tr>
										<th class="col-sm-3">Выберите товар</th>
										<th class="col-sm-2">Ед. измерения</th>
									</tr>
									<tr>
										<td class="col-sm-3">
											<?/*<div style="display: inline-flex;">
												<?$propID = "ID";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "PRODUCT_ID";
												Template::ShowInput($propID, array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array("PROPERTY_TYPE" => "H", "CLASS" => "item_product_id")
													), $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "NAME";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<label type="button"  data-toggle="buttons" class="btn btn-default item_find<?if($arItem["PRODUCT_ID"] > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

												<?$propID = "PRODUCT_ID";
												Template::ShowInput(
													"",
													array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array(
															"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
															"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
															"ENUM" => $arItem[$propID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["ENUM"] : array($arItem[$propID] => $arItem["NAME"])
														)
													), 
													$arItem[$propID]
												);?>
											</div>*/?>
										</td>
										<td class="col-sm-2">
											<?/*$propID = "MEASURE";
											Template::ShowInput(
												$propID, 
												$arProperty["PROPERTY_LIST"][$propID], 
												$arItem[$propID], 
												$propertyID."[".$arItem["ID"]."]"
											);*/?>
										</td>
									</tr>
								</table>
								
							</fieldset>

							<fieldset class="item_connects load_info" data-type="type_merge"<?if($arItem["TYPE"] != 1):?> disabled style="display:none;"<?endif?>>

								<h4>Новый товар</h4>
								<table class="table mb30">
									<tr>
										<th class="col-sm-3">Название нового товара</th>
										<th class="col-sm-2">Ед. измерения</th>
										<?/*<th class="col-sm-3">Количество порций (вес)</th>
										<th class="col-sm-2"></th>*/?>
									</tr>
									<tr>
										<td class="col-sm-3">
											<?/*<div style="display: inline-flex; width: 100%;">
												<?$propID = "ID";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "PRODUCT_ID";
												Template::ShowInput($propID, array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array("PROPERTY_TYPE" => "H", "CLASS" => "item_product_id")
													), $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "NAME";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<label type="button"  data-toggle="buttons" class="btn btn-default item_find<?if($arItem["PRODUCT_ID"] > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

												<?$propID = "PRODUCT_ID";
												Template::ShowInput(
													"",
													array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array(
															"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
															"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
															"ENUM" => $arItem[$propID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["ENUM"] : array($arItem[$propID] => $arItem["NAME"])
														)
													), 
													$arItem[$propID]
												);?>
											</div>*/?>
										</td>
										<td class="col-sm-2">
											<?/*<?$propID = "MEASURE";
											Template::ShowInput(
												$propID, 
												$arProperty["PROPERTY_LIST"][$propID], 
												$arItem[$propID], 
												$propertyID."[".$arItem["ID"]."]"
											);?>*/?>
										</td>
										<?/*<td class="col-sm-3">
											<?$propID = "AMOUNT";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
										</td>
										<td class="col-sm-2">
											<?$propID = "AMOUNT_MEASURE";
											Template::ShowInput(
												$propID, 
												array_merge(
													$arProperty["PROPERTY_LIST"][$propID], 
													array("CLASS" => "width100p item_measure__slave", "READONLY" => "Y")
												),
												$arItem[$propID], 
												$propertyID."[".$arItem["ID"]."]"
											);?>
										</td>*/?>
									</tr>
								</table>
								
								<table class="table mb30">
									<tr>
										<th>Количество порций (вес)</th>
										<th></th>
									</tr>
									<tr>
										<td>
											<?$propID = "AMOUNT";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
										</td>
										<td>
											<?$propID = "AMOUNT_MEASURE";
											Template::ShowInput(
												$propID, 
												array_merge(
													$arProperty["PROPERTY_LIST"][$propID], 
													array("CLASS" => "width100p item_measure__slave", "READONLY" => "Y")
												),
												$arItem[$propID], 
												$propertyID."[".$arItem["ID"]."]"
											);?>
										</td>
									</tr>
								</table>

								<h4>Установите погрешности производства </h4>
								<table class="table mb20">
									<tr>
										<th>Погрешность соотношений<?/* (+/-)*/?></th>
									</tr>
									<tr>
										<td>
											<?$propID = "FAULT_RATIO";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
										</td>
									</tr>
								</table>

								<h4>Ингридиенты</h4>
								<div class="inclone">
									<table class="table mb30">
										<tr>
											<th>Ингредиент</th>
											<th colspan="2">кол-во ингредиента</th>
											<?/*<th>Процент отходов</th>*/?>
											<th></th>
										</tr>
										<?$propID = "CONNECT";
										foreach($arItem[$propID] as $arConnect) if($arItem["TYPE"] == 1 || $arConnect["SAMPLE"] == "Y"):?>
										<tr class="<?if($arConnect["SAMPLE"] == "Y"):?>inclone__block <?else:?>deleted_block <?endif?>item_connect load_info" data-item="<?=$arConnect["ID"]?>">
											<td>
												<div class="prod_select_tab" style="display: inline-flex;">

													<?$propCID = "ID";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], intval($arConnect[$propCID]) > 0 ? $arConnect[$propCID] : "", $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<?$propCID = "PRODUCT_ID";
													Template::ShowInput(
														$propCID, 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array("PROPERTY_TYPE" => "H", "CLASS" => "item_connect__product_id")
														),
														$arConnect[$propCID], 
														$propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]"
													);?>

													<?$propCID = "NAME";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<label type="button"  data-toggle="buttons" class="btn btn-default item_connect__find<?if(intval($arConnect["ID"]) > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

													<?$propCID = "PRODUCT_ID";
													Template::ShowInput(
														"", 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array(
																"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
																"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
																"ENUM" => $arConnect[$propCID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID]["ENUM"] : array($arConnect[$propCID] => $arConnect["NAME"])
															)
														), 
														$arConnect[$propCID]
													);?>
												</div>
											</td>
											<td>
												<?$propCID = "AMOUNT";
												Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>
											</td>
											<td style="display: inline-flex;">
												<?$propCID = "MEASURE";
												Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>
												<label class="measure_translate" style="display: inline-flex; line-height: 40px;"  data-toggle="tooltip" data-placement="top" title="Перевод ед. изм. ингредиента из сохраненного в базе в ед. изм. рецепта.">
													,&nbsp;где&nbsp;

													<?$propCID = "MEASURE_FROM";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<?$propCID = "MEASURE";
													Template::ShowInput(
														"", 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array(
																"READONLY" => "Y",
																"CLASS" => "width50 nopadding measure_translate__from_unit info_slave",
																"DATA" => array(
																	"info_set" => "CAT_MEASURE"
																)
															)
														)
													);?>

													&nbsp;=&nbsp;

													<?$propCID = "MEASURE_TO";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<?$propCID = "MEASURE";
													Template::ShowInput(
														"", 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array(
																"READONLY" => "Y",
																"CLASS" => "width50 nopadding measure_translate__to_unit",
																"DATA" => array()
															)
														)
													);?>
												</label>
											</td>
											<?/*<td><input type="text" name="ITEMS[<?=$arItem["ID"]?>][CONNECT][WASTE_RATE][]" value="<?=$arConnect["WASTE_RATE"]?>" class="form-control"></td>*/?>
											<td>
												<div class="clearfix">
													<span class="btn pull-right btn-lg btn-danger deleted_block_btn">X</span>
												</div>
											</td>
										</tr>
										<?endif?>
									</table>
									<div class="btn-list"><span class="btn btn-primary inclone__btn">+</span></div>
								</div>

							</fieldset>

							<fieldset class="item_connects load_info" data-type="type_split"<?if($arItem["TYPE"] != 2):?> disabled style="display:none;"<?endif?>>

								<h4>Разделяемый товар</h4>
								<table class="table mb30">
									<tr>
										<th class="col-sm-3">Выберите товар</th>
										<th class="col-sm-2">Ед. измерения</th>
									</tr>
									<tr>
										<td class="col-sm-3">
											<?/*<div style="display: inline-flex; width: 100%;">
												<?$propID = "ID";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "PRODUCT_ID";
												Template::ShowInput($propID, array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array("PROPERTY_TYPE" => "H", "CLASS" => "item_product_id")
													), $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "NAME";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<label type="button"  data-toggle="buttons" class="btn btn-default item_find<?if($arItem["PRODUCT_ID"] > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

												<?$propID = "PRODUCT_ID";
												Template::ShowInput(
													"",
													array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array(
															"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
															"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
															"ENUM" => $arItem[$propID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["ENUM"] : array($arItem[$propID] => $arItem["NAME"])
														)
													), 
													$arItem[$propID]
												);?>
											</div>*/?>
										</td>
										<td class="col-sm-2">
											<?/*$propID = "MEASURE";
											Template::ShowInput(
												$propID, 
												$arProperty["PROPERTY_LIST"][$propID], 
												$arItem[$propID], 
												$propertyID."[".$arItem["ID"]."]"
											);*/?>
										</td>
									</tr>
								</table>
								<br />

								<table class="table mb30">
									<tr>
										<th>Кол-во одной порции товара на входе (в относительной ед.)</th>
										<th>Относительная единица измерений</th>
									</tr>
									<tr>
										<td>
											<?$propID = "AMOUNT";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
										</td>
										<td style="display: inline-flex;" class="item_measure_translate">
											<?$propID = "AMOUNT_MEASURE";
											Template::ShowInput(
												$propID, 
												array_merge(
													$arProperty["PROPERTY_LIST"][$propID], 
													array("CLASS" => "width100 measure_split measure_input")
												),
												$arItem[$propID], 
												$propertyID."[".$arItem["ID"]."]"
											);?>
											<label class="measure_translate" style="display: inline-flex; line-height: 40px;"  data-toggle="tooltip" data-placement="top" title="Перевод из ед. изм. товара на выходе в ед. изм. исходного.">
												,&nbsp;где&nbsp;

												<?$propID = "MEASURE_FROM";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "MEASURE";
												Template::ShowInput(
													"", 
													array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array(
															"READONLY" => "Y",
															"CLASS" => "width50 nopadding measure_translate__from_unit item_measure__slave",
															"DATA" => array(
																"info_set" => "CAT_MEASURE"
															)
														)
													)
												);?>

												&nbsp;=&nbsp;

												<?$propID = "MEASURE_TO";
												Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>

												<?$propID = "MEASURE";
												Template::ShowInput(
													"", 
													array_merge(
														$arProperty["PROPERTY_LIST"][$propID],
														array(
															"READONLY" => "Y",
															"CLASS" => "width50 nopadding measure_translate__to_unit",
															"DATA" => array()
														)
													)
												);?>
											</label>
										</td>
									</tr>
								</table>

								<h4>Установите погрешности производства</h4>
								<table class="table mb20">
									<tr>
										<th>Погрешность соотношений<?/* (+/-)*/?></th>
										<?/*<th>Процент отходов (+/-)</th>*/?>
									</tr>
									<tr>
										<td>
											<?$propID = "FAULT_RATIO";
											Template::ShowInput($propID, $arProperty["PROPERTY_LIST"][$propID], $arItem[$propID], $propertyID."[".$arItem["ID"]."]");?>
										</td>
										<?/*<td><input type="text" name="ITEMS[<?=$arItem["ID"]?>][WASTE_RATE]" value="<?=$arItem["WASTE_RATE"]?>" class="form-control"></td>*/?>
									</tr>
								</table>

								<h4>Товары на выходе</h4>
								<div class="inclone">
									<table class="table mb30">
										<tr>
											<th>Товары на выходе</th>
											<th colspan="2">кол-во товара</th>
											<th>Соотношение товара</th>
											<th></th>
										</tr>
										<?$propID = "CONNECT";
										foreach($arItem[$propID] as $arConnect) if($arItem["TYPE"] == 2 || $arConnect["SAMPLE"] == "Y"):?>
										<tr class="<?if($arConnect["SAMPLE"] == "Y"):?>inclone__block <?else:?>deleted_block <?endif?>item_connect load_info" data-item="<?=$arConnect["ID"]?>">
											<td>
												<div class="prod_select_tab" style="display: inline-flex;">

													<?$propCID = "ID";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], intval($arConnect[$propCID]) > 0 ? $arConnect[$propCID] : "", $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<?$propCID = "PRODUCT_ID";
													Template::ShowInput(
														$propCID, 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array("PROPERTY_TYPE" => "H", "CLASS" => "item_connect__product_id")
														),
														$arConnect[$propCID], 
														$propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]"
													);?>

													<?$propCID = "NAME";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<label type="button"  data-toggle="buttons" class="btn btn-default item_connect__find<?if(intval($arConnect["ID"]) > 0):?> active<?endif?> glyphicon glyphicon-search"></label>

													<?$propCID = "PRODUCT_ID";
													Template::ShowInput(
														"", 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array(
																"DISABLED" => $arItem[$propID] <= 0 ? "Y" : "N",
																"READONLY" => $arItem[$propID] <= 0 ? "Y" : "N",
																"ENUM" => $arConnect[$propCID] > 0 ? $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID]["ENUM"] : array($arConnect[$propCID] => $arConnect["NAME"])
															)
														), 
														$arConnect[$propCID]
													);?>
												</div>
											</td>
											<td>
												<?$propCID = "AMOUNT";
												Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>
											</td>
											<td style="display: inline-flex;">
												<?$propCID = "MEASURE";
												Template::ShowInput(
													$propCID, 
													array_merge(
														$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
														array(
															"CLASS" => "width50 measure_separate measure_input", 
															"READONLY" => "Y"
														)
													)
												);?>
												<label class="measure_translate" style="display: inline-flex; line-height: 40px;">
													,&nbsp;где&nbsp;

													<?$propCID = "MEASURE_FROM";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<?$propCID = "MEASURE";
													Template::ShowInput(
														"", 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array(
																"READONLY" => "Y",
																"CLASS" => "width50 nopadding measure_translate__from_unit",
																"DATA" => array(
																	"info_set" => "CAT_MEASURE"
																)
															)
														)
													);?>

													&nbsp;=&nbsp;

													<?$propCID = "MEASURE_TO";
													Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>

													<?$propCID = "MEASURE";
													Template::ShowInput(
														"", 
														array_merge(
															$arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID],
															array(
																"READONLY" => "Y",
																"CLASS" => "width50 nopadding measure_translate__to_unit",
																"DATA" => array()
															)
														)
													);?>
												</label>
											</td>
											<td>
												<?$propCID = "AMOUNT_RATIO";
												Template::ShowInput($propCID, $arProperty["PROPERTY_LIST"][$propID]["PROPERTY_LIST"][$propCID], $arConnect[$propCID], $propertyID."[".$arItem["ID"]."][".$propID."][".$arConnect["ID"]."]");?>
											</td>
											<td>
												<div class="clearfix">
													<span class="btn pull-right btn-lg btn-danger deleted_block_btn">X</span>
												</div>
											</td>
										</tr>
										<?endif?>
									</table>
									<div class="btn-list"><span class="btn btn-primary inclone__btn scanner_detection_add">+</span></div>
								</div>

							</fieldset>

							<span class="btn btn-danger pull-left item_del">Удалить элемент</span>
						<?/*</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Сохранить рецепт</button>
						</div>
					</div>
				</div>*/?>
			</div>
		<?endforeach?>
		</div>
		<br>
		<div class="alert alert-info" style="display: none;">для редактирования нажмите на плавающий элемент</div>
		<ul class="list-inline mt20" style="display: none;">
			<li><kbd class="fc_head">&nbsp;&nbsp;&nbsp;</kbd> - Конечный продукт</li>
			<li><kbd class="fc_isset">&nbsp;&nbsp;&nbsp;</kbd> - В наличии</li>
			<li><kbd class="fc_split">&nbsp;&nbsp;&nbsp;</kbd> - Разделенный</li>
			<li><kbd class="fc_merge">&nbsp;&nbsp;&nbsp;</kbd> - Полученный объединением</li>
			<li><kbd class="fc_separate">&nbsp;&nbsp;&nbsp;</kbd> - Полученный разделением</li>
			<li><kbd class="fc_new">&nbsp;&nbsp;&nbsp;</kbd> - Ошибка</li>
		</ul>
		<br>
		<div class="btn-list clearfix">
		<?if (strlen($arParams["LIST_URL"]) > 0):?>
			<input class="btn btn-primary" type="submit" name="submit" value="<?if($arParams["ID"]):?>Сохранить рецепт<?else:?>Создать рецепт<?endif?>" />
		<?endif?>
				<?/*<input class="btn btn-primary" type="submit" name="apply" value="Применить" />*/?>
			<?if (strlen($arParams["LIST_URL"]) > 0):?>
				<input class="btn btn-default"
					type="button"
					name="iblock_cancel"
					value="Отменить"
					onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"])?>';"
				>
			<?endif?>
		</div>
	</form>
</div>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fabrica/arbor.js");?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fabrica/graphics.js");?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fabrica/renderer.js");?>
<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/fabrica/fabrica.js");?>