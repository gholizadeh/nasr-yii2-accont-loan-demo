<?php
namespace app\assets;

use yii\web\AssetBundle;

class ColorPickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/color-picker.css',
    ];
    public $js = [
        'js/color-picker.js',
    ];
    public $depends = [
        'yii\bootstrap4\BootstrapAsset',
    ];
}