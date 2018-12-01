<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?> 
<?if(strpos($APPLICATION->GetCurPage(), "/cash/") === false)
	$staticHTMLCache->enableVoting();?>
			</div>
<?if($_REQUEST["iframe"] != "y"):?>
		</div>
		<!-- mainpanel --> 
	</div>
	<!-- mainwrapper -->
</section>
<?endif?>
<?if($_REQUEST["ajax"] != "y"):?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/static/js/script.js");?>
</body>
</html>
<?else: die(); endif;?>