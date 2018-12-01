<div class="alert alert-danger">
	<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
	<strong>ВНИМАНИЕ!</strong> Раздел находится в тестовом режиме!
	<iframe src="/egais/" width="100%" height="60" frameborder
="no" scrolling="no"></iframe>
</div>
<?$APPLICATION->IncludeComponent(
	"shopmate:egais", 
	".default", 
	array(
		"SEF_MODE" => "N",
		"GROUPS" => array(
			0 => "1",
		),
		"ALLOW_EDIT" => "Y",
		"ALLOW_DELETE" => "Y",
		"NAV_ON_PAGE" => "10",
		"USER_MESSAGE_ADD" => "",
		"USER_MESSAGE_EDIT" => "",
		"DEFAULT_INPUT_SIZE" => "30",
		"SEF_FOLDER" => "/"
	),
	false
);?>