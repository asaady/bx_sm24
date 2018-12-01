<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?
ShowMessage($arParams["~AUTH_RESULT"]);
ShowMessage($arResult['ERROR_MESSAGE']);
?>


	<form name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" class="form-horizontal">

		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<?if (strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>

		<div class="form-group">
			<label for="USER_LOGIN" class="control-label col-sm-3"><?=GetMessage("AUTH_LOGIN")?></label>
			<div class="col-sm-9">
				<input class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
			</div>
		</div>
		<div class="form-group">
			<label for="USER_PASSWORD" class="control-label col-sm-3"><?=GetMessage("AUTH_PASSWORD")?></label>
			<div class="col-sm-9">
				<input class="form-control" type="password" name="USER_PASSWORD" maxlength="255" />
<?if($arResult["SECURE_AUTH"]):?>
				<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
				<noscript>
				<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
				</noscript>
<script type="text/javascript">
document.getElementById('bx_auth_secure').style.display = 'inline-block';
</script>
<?endif?>
			</div>
		</div>
	<?if($arResult["CAPTCHA_CODE"]):?>
		<div class="form-group">
			<label for="captcha_word" class="control-label col-sm-3">Текст с картинки</label>
			<div class="col-sm-5">
				<input class="form-control" type="text" name="captcha_word" maxlength="50" value="" size="15" />
			</div>
			<div class="col-sm-4">
				<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</div>
		</div>
	<?endif?>
	<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
		<div class="form-group">
			<label for="USER_REMEMBER" class="control-label col-sm-3"></label>
			<div class="col-sm-9 inline-block">
				<input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;<?=GetMessage("AUTH_REMEMBER_ME")?></label>
			</div>
		</div>
	<?endif?>

	<div class="form-group">
		<div class="col-md-3"></div>
		<div class="col-md-9">
			<div class="btn-list clearfix">
				<input class="btn btn-primary" type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" />
			</div>
		</div>
	</div>

<?if ($arParams["NOT_SHOW_LINKS"] != "Y"):?>
		<noindex>
			<div class="form-group">
				<div class="col-md-3"></div>
				<div class="col-md-9">
					<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a><?/*if($arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"):?> / <a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow" title="<?=GetMessage("AUTH_FIRST_ONE")?>"><?=GetMessage("AUTH_REGISTER")?></a><?endif*/?>
				</div>
			</div>
		</noindex>
<?endif?>
	</form>

<script type="text/javascript">
<?if (strlen($arResult["LAST_LOGIN"])>0):?>
try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
<?else:?>
try{document.form_auth.USER_LOGIN.focus();}catch(e){}
<?endif?>
</script>
