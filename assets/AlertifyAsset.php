<?php
namespace app\assets;

use yii\web\AssetBundle;

class AlertifyAsset extends AssetBundle
{
    public $sourcePath = '@webroot/libs/alertify';
    public $css = [
        'css/alertify.min.css',
        'css/themes/default.min.css',
        'css/themes/semantic.min.css',
        'css/themes/bootstrap.min.css'
    ];
    public $js = [
    	'alertify.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset'
    ];
}