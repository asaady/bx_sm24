<?
define("EMPTY_REDIRECT", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Финансы");

?>		<?/*global $arrFilter;
?>
<?$APPLICATION->IncludeComponent(
	"shopmate:finance.transactions", 
	"", 
	array(
		"TRANSACTION" => "",
		"ITEM_ID" => "",
		"SORT_FIELD" => "ID",
		"SORT_ORDER" => "asc",
		"FILTER_NAME" => "arrFilter",
		"NAV_ON_PAGE" => "0",
		"GROUPS" => array(
		),
		"ALLOW_EDIT" => "Y",
		"ALLOW_DELETE" => "Y",
		"SEF_MODE" => "N",
		"SEF_FOLDER" => "/"
	),
	$component
);?>
      <div class="panel panel-body">
        <div class="mb30">
          <h1>Финансы</h1>
        </div>
        <div class="row mb20">
            <div class="col-lg-6">
            
            
            <div class="mb15"><h4><a href="#">Дебиторка</a></h4></div>
                                <div class="panel panel-default">
                                    <div class="panel-body padding15">
                                        <h5 class="md-title mt0 mb10">Site Statistics</h5>
                                        <div id="basicFlotLegend" class="flotLegend"></div>
                                        <div id="basicflot" class="flotChart"></div>
                                    </div><!-- panel-body -->
                                    <div class="panel-footer">
                                        <div class="tinystat pull-left">
                                            <div id="sparkline" class="chart mt5"></div>
                                            <div class="datainfo">
                                                <span class="text-muted">Average</span>
                                                <h4>$9,201</h4>
                                            </div>
                                        </div><!-- tinystat -->
                                        <div class="tinystat pull-right">
                                            <div id="sparkline2" class="chart mt5"></div>
                                            <div class="datainfo">
                                                <span class="text-muted">Total</span>
                                                <h4>$8,201</h4>
                                            </div>
                                        </div><!-- tinystat -->
                                    </div><!-- panel-footer -->
                                </div><!-- panel -->
                          
            </div>
            
            <div class="col-lg-6"> 
            
            <div class="mb15"><h4><a href="#">Кредиторка</a></h4></div>
                                <div class="panel panel-default">
                                    <div class="panel-body padding15">
                                        <h5 class="md-title mt0 mb10">Site Visitors</h5>
                                        <div id="basicFlotLegend2" class="flotLegend"></div>
                                        <div id="basicflot2" class="flotChart"></div>
                                    </div><!-- panel-body -->
                                    <div class="panel-footer">
                                        <div class="tinystat pull-left">
                                            <div id="sparkline3" class="chart mt5"></div>
                                            <div class="datainfo">
                                                <span class="text-muted">Average</span>
                                                <h4>52,201</h4>
                                            </div>
                                        </div><!-- tinystat -->
                                        <div class="tinystat pull-right">
                                            <div id="sparkline4" class="chart mt5"></div>
                                            <div class="datainfo">
                                                <span class="text-muted">Total</span>
                                                <h4>11,201</h4>
                                            </div>
                                        </div><!-- tinystat -->
                                    </div><!-- panel-footer -->
                                </div><!-- panel -->
                          
            </div>
            
            <div class="col-lg-12">
            <div class="mb15"><h4>Быстрая сводка</h4></div>            
            <table class="table  table-striped table-hover">
                <tr>
                    <th>Прибыль текущего месяца</th>
                    <th>Оборот текущего месяца</th>
                    <th>Закупки текущего месяца</th>
                    <th>Расходы на зарплаты месяца</th>
                    <th>Прочие расходы текущего месяца</th>
                </tr>
                
                   
                
                <tr>
                    <td class="text-success">125.671 руб.</td>
                    
                    <td class="text-primary">516.388 руб.</td>
                    
                    <td class="text-warning">234.211 руб.</td>
                    
                    <td class="text-danger">76.900 руб.</td>
                    
                    <td class="text-info">79.606 руб.</td>
                </tr>
                
                
            </table>
                
                <!-- Всё что внутри МОЖНО ГРОХАТЬ -->
                <div>
                
                 <p class="text-muted">Muted: This text is grayed out.</p>
    <p class="text-primary">Important: Please read the instructions carefully before proceeding.</p>
    <p class="text-success">Success: Your message has been sent successfully.</p>
    <p class="text-info">Note: You must agree with the terms and conditions to complete the sign up process.</p>
    <p class="text-warning">Warning: There was a problem with your network connection.</p>
    <p class="text-danger">Error: An error has been occurred while submitting your data.</p>
                </div>                
                
                <!-- ДО СЮДА ГРОХАТЬ-->
            </div>
        </div>
      </div>*/?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>