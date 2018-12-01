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
global ${$arParams["FILTER_NAME"]};
$this->setFrameMode(false);
$date = new \Bitrix\Main\Type\DateTime();
$newCnt = $component->getNewCnt();?>
<div class="btn-group btn-group-list btn-group-notification notify_ajax notify__panel">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-bell-o"></i> <span class="badge notify_ajax__counter"<?if ($newCnt <= 0):?> style="display: none;"<?endif?>><?=$newCnt?></span> </button>
	<div class="dropdown-menu pull-right">
		<h5>Уведомления</h5>
		<ul class="media-list dropdown-list notify_ajax__list">
		<?foreach($arResult["ITEMS"] as $key => $arItem):?>
			<li class="media"> 
				<?/*<img class="img-circle pull-left noti-thumb" src="images/photos/user1.png" alt="">*/?>
				<div class="media-body"> 
					<strong><?=$arItem["USER_FORMATED"]?></strong> 
					<?=(!empty(GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"]))) ? GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"])) : $arItem["EVENT_TYPE"]) ?> <a href="<?=$arItem["URL"]?>"><?=(!empty(GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"]))) ? GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"])) : $arItem["ITEM_OBJECT"]) ?></a><br /><?=$arItem["DESCRIPTION"]?>
					<small class="date"><i class="fa fa-calendar"></i> <?=$arItem["DATE"]?></small> 
				</div>
			</li>
		<?endforeach?>
		</ul>
		<div class="dropdown-footer text-center"> <a href="/user/notify/" class="link">Смотреть все уведомления</a> </div>
	</div>
	<!-- dropdown-menu --> 
</div>
<!-- btn-group -->

<script type="text/javascript">
$(function(){

	var notify_date = '<?=$date->toString()?>',
		notify_cnt = <?=$newCnt?>;

	toastr.options = {
		closeButton: true,
		progressBar: true,
		showMethod: 'slideDown',
		timeOut: 4000
	};

	var timerId = setInterval(function() {
		$.ajax({
			url: '<?=$templateFolder."/ajax.php"?>',
			data: {
				date: notify_date
			},
			dataType: 'json',
			success: function(data) {
				notify_date = data.date;
				
				if (data.items.length > 0) {

					notify_cnt += data.items.length;

					$('.notify_ajax__counter').html(notify_cnt).show();

					$.each(data.items, function(index, value) {
						toastr.success(value);
					});

				}

				$('.notify_ajax__list').prepend(data.html);
			}
		});
	}, 15000);

});
</script>
