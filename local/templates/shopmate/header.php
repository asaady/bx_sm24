<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($_REQUEST["ajax"] != "y"):?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?if (defined("NEED_AUTH") && NEED_AUTH===true && !$USER->IsAuthorized()):?>
		<meta name="cmsmagazine" content="f63e2e32706a843527690cdac7519325" />
	<?endif?>
	<?$APPLICATION->ShowHead()?>
	<title><?$APPLICATION->ShowTitle()?></title>
	<?//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/bootstrap.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/jquery.loader.min.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/jquery.select2.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/jquery.toastr.min.css");?>

	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/style.default.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/morris.css");?>
	<?//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/select2.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/js/css3clock/css/style.css");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/style.calendar.css");?>
	<?//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/jquery-ui.min.js");?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/static/css/style.css");?>
	<? include("js.php"); ?>
</head>

<body<?if ($_REQUEST["edit"] == "Y"):?> class="hidden-left"<?endif?>>
<?if($_REQUEST["iframe"] != "y"):?>
<?$APPLICATION->ShowPanel();?>
<header>
  <div class="headerwrapper">
	<div class="header-left"> <a href="/" class="logo"> <h3 style="margin: 0px; color: white;">STOREMATE</h3> </a>
	  <div class="pull-right"> <a href="#" class="menu-collapse"> <i class="fa fa-bars"></i> </a> </div>
	</div>
	<!-- header-left -->
	<div class="header-right">
	  <div class="pull-right">
	  	<div class="text-uppercase lead" id="topmenu" style="float: left;">
	  	<?/*Bitrix\Main\Page\Frame::getInstance()->StartDynamicWithID("topmenu");?>
			<?$APPLICATION->IncludeComponent("bitrix:menu", "top_menu", Array(
				"ROOT_MENU_TYPE" => "bottom",	// Тип меню для первого уровня
					"MAX_LEVEL" => "1",	// Уровень вложенности меню
					"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
					"MENU_CACHE_TYPE" => "N",	// Тип кеширования
					"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
					"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
					"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
					"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
					"DELAY" => "N",	// Откладывать выполнение шаблона меню
					"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
				),
				false
			);?>
		<?Bitrix\Main\Page\Frame::getInstance()->FinishDynamicWithID("topmenu", "", "topmenu");*/?>
		</div>
		<?if ($_REQUEST["edit"] == "Y" && !empty($_REQUEST["CODE"])):?>
		<div class="btn-group btn-group-list">
			<a target="_blank" class="btn btn-default" href="<?=$APPLICATION->GetCurPageParam("print=Y", ["print"])?>"> <i class="fa fa-print"></i> </a>
		</div>
		<?endif?>
		<?global $USER;
		if ($USER->IsAuthorized()):?>
			<?$APPLICATION->IncludeComponent("shopmate:shop.select", "", Array(), false);?>
			<?global $arrAppNewsFilter, $DB;
			$arrAppNewsFilter[">DATE_ACTIVE_FROM"] = date($DB->DateFormatToPHP(CLang::GetDateFormat()), strtotime($USER->GetParam("PREV_AUTH")));?>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "app_news_header", Array(
				"DISPLAY_DATE" => "Y",	// Выводить дату элемента
				"DISPLAY_NAME" => "Y",	// Выводить название элемента
				"DISPLAY_PICTURE" => "N",	// Выводить изображение для анонса
				"DISPLAY_PREVIEW_TEXT" => "N",	// Выводить текст анонса
				"AJAX_MODE" => "N",	// Включить режим AJAX
				"IBLOCK_TYPE" => "app_news",	// Тип информационного блока (используется только для проверки)
				"IBLOCK_ID" => "3",	// Код информационного блока
				"NEWS_COUNT" => "10",	// Количество новостей на странице
				"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
				"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
				"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
				"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
				"FILTER_NAME" => "arrAppNewsFilter",	// Фильтр
				"FIELD_CODE" => "",	// Поля
				"PROPERTY_CODE" => "",	// Свойства
				"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
				"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
				"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
				"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
				"SET_TITLE" => "N",	// Устанавливать заголовок страницы
				"SET_BROWSER_TITLE" => "N",	// Устанавливать заголовок окна браузера
				"SET_META_KEYWORDS" => "N",	// Устанавливать ключевые слова страницы
				"SET_META_DESCRIPTION" => "N",	// Устанавливать описание страницы
				"SET_LAST_MODIFIED" => "N",	// Устанавливать в заголовках ответа время модификации страницы
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",	// Включать инфоблок в цепочку навигации
				"ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
				"PARENT_SECTION" => "",	// ID раздела
				"PARENT_SECTION_CODE" => "",	// Код раздела
				"INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
				"CACHE_TYPE" => "A",	// Тип кеширования
				"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
				"CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
				"CACHE_GROUPS" => "Y",	// Учитывать права доступа
				"PAGER_TEMPLATE" => ".default",	// Шаблон постраничной навигации
				"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
				"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
				"PAGER_TITLE" => "Новости",	// Название категорий
				"PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
				"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
				"PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
				"PAGER_BASE_LINK_ENABLE" => "N",	// Включить обработку ссылок
				"SET_STATUS_404" => "N",	// Устанавливать статус 404
				"SHOW_404" => "N",	// Показ специальной страницы
				"MESSAGE_404" => "",	// Сообщение для показа (по умолчанию из компонента)
				"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
				"AJAX_OPTION_STYLE" => "N",	// Включить подгрузку стилей
				"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
				),
				false
			);?>
			<?$APPLICATION->IncludeComponent("shopmate:notify.list", "header");?>
		<?endif?>
		<?/*<form class="form form-search" action="search-results.html">
		  <input type="search" class="form-control" placeholder="Search" />
		</form>
		<div class="btn-group btn-group-list btn-group-notification">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-bell-o"></i> <span class="badge">5</span> </button>
		  <div class="dropdown-menu pull-right"> <a href="#" class="link-right"><i class="fa fa-search"></i></a>
			<h5>Notification</h5>
			<ul class="media-list dropdown-list">
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user1.png" alt="">
				<div class="media-body"> <strong>Nusja Nawancali</strong> likes a photo of you <small class="date"><i class="fa fa-thumbs-up"></i> 15 minutes ago</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user2.png" alt="">
				<div class="media-body"> <strong>Weno Carasbong</strong> shared a photo of you in your <strong>Mobile Uploads</strong> album. <small class="date"><i class="fa fa-calendar"></i> July 04, 2014</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user3.png" alt="">
				<div class="media-body"> <strong>Venro Leonga</strong> likes a photo of you <small class="date"><i class="fa fa-thumbs-up"></i> July 03, 2014</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user4.png" alt="">
				<div class="media-body"> <strong>Nanterey Reslaba</strong> shared a photo of you in your <strong>Mobile Uploads</strong> album. <small class="date"><i class="fa fa-calendar"></i> July 03, 2014</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user1.png" alt="">
				<div class="media-body"> <strong>Nusja Nawancali</strong> shared a photo of you in your <strong>Mobile Uploads</strong> album. <small class="date"><i class="fa fa-calendar"></i> July 02, 2014</small> </div>
			  </li>
			</ul>
			<div class="dropdown-footer text-center"> <a href="#" class="link">See All Notifications</a> </div>
		  </div>
		  <!-- dropdown-menu --> 
		</div>
		<!-- btn-group -->
		
		<div class="btn-group btn-group-list btn-group-messages">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-envelope-o"></i> <span class="badge">2</span> </button>
		  <div class="dropdown-menu pull-right"> <a href="#" class="link-right"><i class="fa fa-plus"></i></a>
			<h5>New Messages</h5>
			<ul class="media-list dropdown-list">
			  <li class="media"> <span class="badge badge-success">New</span> <img class="img-circle pull-left noti-thumb" src="images/photos/user1.png" alt="">
				<div class="media-body"> <strong>Nusja Nawancali</strong>
				  <p>Hi! How are you?...</p>
				  <small class="date"><i class="fa fa-clock-o"></i> 15 minutes ago</small> </div>
			  </li>
			  <li class="media"> <span class="badge badge-success">New</span> <img class="img-circle pull-left noti-thumb" src="images/photos/user2.png" alt="">
				<div class="media-body"> <strong>Weno Carasbong</strong>
				  <p>Lorem ipsum dolor sit amet...</p>
				  <small class="date"><i class="fa fa-clock-o"></i> July 04, 2014</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user3.png" alt="">
				<div class="media-body"> <strong>Venro Leonga</strong>
				  <p>Do you have the time to listen to me...</p>
				  <small class="date"><i class="fa fa-clock-o"></i> July 03, 2014</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user4.png" alt="">
				<div class="media-body"> <strong>Nanterey Reslaba</strong>
				  <p>It might seem crazy what I'm about to say...</p>
				  <small class="date"><i class="fa fa-clock-o"></i> July 03, 2014</small> </div>
			  </li>
			  <li class="media"> <img class="img-circle pull-left noti-thumb" src="images/photos/user1.png" alt="">
				<div class="media-body"> <strong>Nusja Nawancali</strong>
				  <p>Hey I just met you and this is crazy...</p>
				  <small class="date"><i class="fa fa-clock-o"></i> July 02, 2014</small> </div>
			  </li>
			</ul>
			<div class="dropdown-footer text-center"> <a href="#" class="link">See All Messages</a> </div>
		  </div>
		  <!-- dropdown-menu --> 
		</div>
		<!-- btn-group -->
		
		<div class="btn-group btn-group-option">
		  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <img class="img-circle avatar-img" alt="" src="images/photos/profile.png"> <span class="user-name">Salma</span> <i class="fa fa-caret-down"></i> </button>
		  <ul class="dropdown-menu pull-right" role="menu">
			<li><a href="#"><i class="glyphicon glyphicon-user"></i> My Profile</a></li>
			<li><a href="#"><i class="glyphicon glyphicon-star"></i> Activity Log</a></li>
			<li><a href="#"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>
			<li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>
			<li class="divider"></li>
			<li><a href="#"><i class="glyphicon glyphicon-log-out"></i>Sign Out</a></li>
		  </ul>
		</div>
		<!-- btn-group -->*/?> 
		
	  </div>
	  <!-- pull-right --> 
	  
	</div>
	<!-- header-right --> 
	
  </div>
  <!-- headerwrapper --> 
</header>
<?endif?>
<?if($_REQUEST["iframe"] != "y"):?>
<section>
	<div class="mainwrapper">

		<div class="leftpanel">
			<div id="leftpanel">
			<?Bitrix\Main\Page\Frame::getInstance()->StartDynamicWithID("leftpanel");?>
			<?global $USER;
			if($USER->IsAuthorized()):?>
			<div class="media profile-left"> 
				<a class="pull-left profile-thumb" href="/user/profile/"> 
					<?/*<img class="img-circle" src="<?=SITE_TEMPLATE_PATH?>/static/images/photos/profile.png" alt="">*/?>
				</a>
				<div class="media-body">
					<h4 class="media-heading"><?=$USER->GetFullName()?></h4>
					<?/*<span class="user-options">
						<?$APPLICATION->IncludeComponent("shopmate:shop.select", "", Array(), false);?>
					</span>*/?>
					<span class="user-options">
						<?/*<a href="#"><i class="glyphicon glyphicon-user"></i></a> 
						<a href="#"><i class="glyphicon glyphicon-envelope"></i></a> 
						<a href="#"><i class="glyphicon glyphicon-cog"></i></a>*/?>
					<?if ($USER->IsAdmin()):?>
						<a href="<?=$APPLICATION->GetCurPageParam("tests=Y", array("tests"))?>" class="modal_ajax" data-title="tests" title="tests"><i class="glyphicon glyphicon-check"></i></a>
					<?endif?>
					<?if ($USER->IsAuthorized() && file_exists($_SERVER["DOCUMENT_ROOT"].$APPLICATION->GetCurDir()."README.md")):?>
						<a href="<?=$APPLICATION->GetCurPageParam("help=Y", array("help"))?>" class="modal_ajax" data-title="Справка по разделу <?=$APPLICATION->GetTitle(false)?>" title="Справка по разделу <?=$APPLICATION->GetTitle(false)?>"><i class="glyphicon glyphicon-info-sign"></i></a>
					<?endif?>
						<a href="<?=$APPLICATION->GetCurPageParam("logout=yes", array("logout"))?>" title="Выход"><i class="glyphicon glyphicon-log-out"></i></a> 
					</span>
					<span class="user-options">
					<?if($USER->IsAdmin() && stripos($APPLICATION->GetCurPage(), "/cash/") !== false):
						if(!empty($_REQUEST["corporate"]))
						{
							$_SESSION["cash_test"] = $_REQUEST["corporate"] != "Y" ? "N" : "Y"; 
							LocalRedirect($APPLICATION->GetCurPageParam("", array("corporate")));
						}
						if(isset($_REQUEST["cash_test"]))
						{
							$_SESSION["cash_test"] = $_SESSION["cash_test"] == "Y" ? "N" : "Y"; 
							LocalRedirect($APPLICATION->GetCurPageParam("", array("cash_test")));
						}?>
						<?/*<a href="<?=$APPLICATION->GetCurPageParam("cash_test=yes", array("cash_test"))?>"><i class="glyphicon<?if($_SESSION["cash_test"] == "Y"):?> glyphicon-ok<?else:?> glyphicon-remove<?endif?> cash_test"> тестовый режим</i></a>*/?>
					<?endif?>
					</span>
				</div>
			</div>
			<?endif?>
			<!-- media -->
			<?$APPLICATION->IncludeComponent("bitrix:menu", "main_menu", Array(
				"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
					"MAX_LEVEL" => "3",	// Уровень вложенности меню
					"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
					"MENU_CACHE_TYPE" => "N",	// Тип кеширования
					"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
					"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
					"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
					"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
					"DELAY" => "N",	// Откладывать выполнение шаблона меню
					"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
				),
				false
			);?>
		  <?/*<ul class="nav nav-pills nav-stacked">
		  
		  <!-- <li  class="active"> - активный -->
			<li><a href="/"><span>Home</span></a></li>
			<li><a href="products.php"><span>Товары | Накладные</span></a></li>
			<li><a href="product_in.php"><span>Оприходование товара</span></a></li>
			<li><a href="cashbox.php"><span>Касса</span></a></li>
			<li><a href="checkout.php"><span>Список чеков</span></a></li>
			<li><a href="finances.php"><span>Финансы</span></a>
			<ul class="children" style="display: block;">
			<li><a href="debitorka.php">Дебиторка</a></a></li>
			<li><a href="kreditorka.php">Кредиторка</a></a></li>
			<li><a href="reports.php">Отчеты</a></a>
				<ul class="children"  style="display: block;">
				<li><a style="padding-left: 50px;" href="expenses.php">Статьи расходов</a></li>
				<li><a style="padding-left: 50px;" href="report_detailed.php">Детальный отчёт</a></li>
				 
				</ul>
				</li>
			</ul>
			</li>
			<li><a href="providers.php"><span>Поставщики</span></a></li>
			<li><a href="clients.php"><span>Клиенты</span></a></li>
			<li><a href="manufacture.php"><span>Производство</span></a></li>
			<li><a href="employees.php"><span>Сотрудники</span></a></li>
			<li><a href="settings.php"><span>Настройки</span></a></li>
			 
		  </ul>*/?>
		 	<?Bitrix\Main\Page\Frame::getInstance()->FinishDynamicWithID("leftpanel", "", "leftpanel");?>
			</div>
		 	<div id="bx-composite-banner" style="position: absolute; bottom: 70px;"></div>
		</div>
		<!-- leftpanel -->

		<div class="mainpanel">
<?endif?>
			<div class="contentpanel">
<?else: $APPLICATION->RestartBuffer(); endif;?>
<?if(strpos($APPLICATION->GetCurPage(), "/cash/") === false)
{
	$staticHTMLCache = \Bitrix\Main\Data\StaticHTMLCache::getInstance();
	$staticHTMLCache->disableVoting();
}?>