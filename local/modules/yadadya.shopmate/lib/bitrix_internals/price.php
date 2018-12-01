<?php
namespace Yadadya\Shopmate\BitrixInternals;

use \Bitrix\Main\Entity\DataManager as DataManager;
use Bitrix\Main\Localization\Loc,
	\Bitrix\Main;

Loc::loadMessages(__FILE__);

class PriceTable extends \Bitrix\Catalog\PriceTable {}