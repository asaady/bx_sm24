<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
$this->setFrameMode(false);?>

<?$arParams["COMPONENT"] = $component;?>
<div class="row">
	<div class="col-md-7">
		<?$APPLICATION->IncludeComponent("bitrix:iblock.element.add.form", "", $arParams, $component, array('HIDE_ICONS' => 'Y'));?>
	</div>
	<?/*<div class="col-md-5">
		<div class="panel panel-primary widget-messaging">
		  <div class="panel-heading"> 
		    <!-- pull-right -->
		    <h3 class="panel-title">Последние операции</h3>
		  </div>
		  <ul class="list-group">
		    <li class="list-group-item"> <small class="pull-right">Dec 10</small>
		      <h4 class="sender">Jennier Lawrence</h4>
		      <p>Добавила <a href="#">товар</a></p>
		    </li>
		    <li class="list-group-item"> <small class="pull-right">Dec 9</small>
		      <h4 class="sender">Marsha Mellow</h4>
		      <p>Удалила товар</p>
		    </li>
		    <li class="list-group-item"> <small class="pull-right">Dec 9</small>
		      <h4 class="sender">Holly Golightly</h4>
		      <p>Сделал бэкап</p>
		    </li>
		  </ul>
		  <div class="panel">
		    <table class="table table-hover">
		      <tr>
		        <td>10/04/2014</td>
		        <td>кол-во</td>
		        <td>приход </td>
		        <td> было: 0шт</td>
		        <td></td>
		        <td>стало: 640 шт</td>
		      </tr>
		      <tr>
		        <td>10/04/2014</td>
		        <td>долг</td>
		        <td>оплата</td>
		        <td>было: 128.000 руб</td>
		        <td></td>
		        <td>стало: 28.000 руб</td>
		      </tr>
		    </table>
		  </div>
		</div>
	<!-- panel --> 
	</div>*/?>
</div>