<?
define("NEED_AUTH", true);
define("EMPTY_REDIRECT", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
die('<a>ku</a>');
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
?>
 <div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="btn-list">
					<?/*<button class="btn btn-default">Сделать бэкап</button>*/?>
					<a href="" class="btn btn-primary">Добавить товар</a>
				</div>
			</div>
		</div>
		<!-- panel --> 
	</div>
	<!-- col-sm-6 -->
</div>

<?/*      <div class="row"> 
        <div class="col-md-4">
          <div class="panel panel-primary-head widget-todo">
            <div class="panel-heading">
              <div class="pull-right"> <a title="" data-toggle="tooltip" class="tooltips mr5" href="#" data-original-title="Settings"><i class="glyphicon glyphicon-cog"></i></a> <a title="" data-toggle="tooltip" class="tooltips" id="addnewtodo" href="#" data-original-title="Add New"><i class="glyphicon glyphicon-plus"></i></a> </div>
              <!-- panel-btns -->
              <h3 class="panel-title">Список задач</h3>
            </div>
            <ul class="panel-body list-group nopadding">
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" id="washcar" value="1">
                  <label for="washcar">Wash car in neighbors house</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" checked="checked" id="eatpizza" value="1">
                  <label for="eatpizza">Find and eat pizza anywhere</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" checked="checked" id="washdish" value="1">
                  <label for="washdish">Wash the dishes and map the floor</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" id="buyclothes" value="1">
                  <label for="buyclothes">Buy some clothes</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" checked="checked" id="throw" value="1">
                  <label for="throw">Throw the garbage</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" id="reply" value="1">
                  <label for="reply">Reply all emails for this week</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
              <li class="list-group-item">
                <div class="ckbox ckbox-default">
                  <input type="checkbox" checked="checked" id="throw" value="1">
                  <label for="throw">Go to Hospital</label>
                  <a href="#" class="pull-right"><i class="fa fa-pencil"></i></a> </div>
              </li>
            </ul>
          </div>
        </div>
        <!-- col-md-4 -->
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="panel-btns" style="display: none;"> <a href="#" class="panel-minimize tooltips" data-toggle="tooltip" title="" data-original-title="Minimize Panel"><i class="fa fa-minus"></i></a> <a href="#" class="panel-close tooltips" data-toggle="tooltip" title="" data-original-title="Close Panel"><i class="fa fa-times"></i></a> </div>
              <!-- panel-btns -->
              <h5 class="panel-title">Последние операции</h5>
              <p>A collapsible components inside a modal</p>
            </div>
            <div class="panel-body"><p>Кто-то совершил операцию</p><p>Кто-то совершил операцию</p><p>Кто-то совершил операцию</p> </div>
          </div>
        </div>
      <div class="col-md-4">  <div class="panel panel-primary widget-messaging">
                                    <div class="panel-heading">
                                        <div class="pull-right">
                                            <a href="#" class="new-msg"><i class="fa fa-edit"></i></a>
                                        </div><!-- pull-right -->
                                        <h3 class="panel-title">Последние операции</h3>
                                    </div>
                                    
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <small class="pull-right">Dec 10</small>
                                            <h4 class="sender">Jennier Lawrence</h4>
                                            <p>Добавила <a href="#">товар</a></p>
                                        </li>
                                        <li class="list-group-item">
                                            <small class="pull-right">Dec 9</small>
                                            <h4 class="sender">Marsha Mellow</h4>
                                            <p>Удалила товар</p>
                                        </li>
                                        <li class="list-group-item">
                                            <small class="pull-right">Dec 9</small>
                                            <h4 class="sender">Holly Golightly</h4>
                                            <p>Сделал бэкап</p>
                                        </li> 
                                    </ul>
                                </div><!-- panel --></div>
    
  </div>*/?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>