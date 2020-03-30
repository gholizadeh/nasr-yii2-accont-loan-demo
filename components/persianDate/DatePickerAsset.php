<?php

namespace app\components\persianDate;

use yii\web\AssetBundle;

/**
 * @author Mohammad Mahdi Gholomian.
 * @copyright 2014 mm.gholamian@yahoo.com
 */
class DatePickerAsset extends AssetBundle
{
	public $sourcePath = '@app/components/persianDate/assets';
	public $js = [
		'js/persianDatepicker.min.js',
		'js/jalali-moment.js'
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
