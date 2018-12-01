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
$this->setFrameMode(false);?>
<div class="panel panel-body">
	<div class="row">
		<div class="col-sm-12 animated fadeInRight">
			<div class="mail-box">

				<table class="table table-hover table-mail">
					<tbody>
					<?foreach($arResult["ITEMS"] as $key => $arItem):?>
						<tr class="unread">
							<?/*<td class="mail-ontact"><a href="mail_detail.html">Anna Smith</a></td>
							<td class="mail-subject"><a href="mail_detail.html">Lorem ipsum dolor noretek imit set.</a></td>
							<td><i class="fa fa-paperclip"></i></td>
							<td class="text-right mail-date">6.10 AM</td>*/?>
							<td><?=$arItem["USER_FORMATED"]?> <?=(!empty(GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"]))) ? GetMessage("EVENT_TYPE_".strtoupper($arItem["EVENT_TYPE"])) : $arItem["EVENT_TYPE"]) ?> <a href="<?=$arItem["URL"]?>"><?=(!empty(GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"]))) ? GetMessage("ITEM_OBJECT_".strtoupper($arItem["ITEM_OBJECT"])) : $arItem["ITEM_OBJECT"]) ?></a><br /><?=$arItem["DESCRIPTION"]?></td>
							<td class="text-right mail-date"><?=$arItem["DATE"]?></td>
						</tr>
					<?endforeach?>
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<?=$arResult["NAV_STRING"]?>
	</div>
</div>