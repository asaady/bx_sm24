<?foreach($arResult["PRINT"] as $id):?>
	<iframe src="<?=$arParams["EDIT_URL"]?>?edit=Y&CODE=<?=$arItem["ID"]?>&print=Y" style="position:absolute;top:0px; left:0px;width:0px; height:0px;border:0px;overfow:none; z-index:-1"></iframe>
<?endforeach?>