<?php
namespace app\assets;

use yii\web\AssetBundle;

class TimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/jqueryui-timepicker-addon/dist';
    public $js = [
        'jquery-ui-timepicker-addon.min.js',
    ];
    public $css = [
        'jquery-ui-timepicker-addon.min.css',
    ];
    public $depends = [
        'yii\jui\JuiAsset',
    ];
}