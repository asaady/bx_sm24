<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Yadadya\Shopmate\Components\Overhead;
$overhead = new Overhead;
$parametrs = array(
	"select" => array("FULL_SUMM"),
	"filter" => $overhead->getFilter($overhead->checkFields($_REQUEST, $overhead->getFilterList()))
);
if ($overhead = Overhead::getList($parametrs)->fetch())
	$arResult["ITEM"]["TOTAL_SUMM"] = "∑ = ".PriceFormat($overhead["FULL_SUMM"]);
?>
<a class="btn btn-primary pull-right list__added_btn" href="<?=$arParams["EDIT_URL"]?>?edit=Y&DOC_TYPE=R" style="display:none;"><span style="font-size:13px" class="glyphicon glyphicon-plus"></span> Добавить накладную на возврат</a>