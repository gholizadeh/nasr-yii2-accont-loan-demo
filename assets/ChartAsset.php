<?php
namespace app\assets;

use yii\web\AssetBundle;

class ChartAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/chart.css',
    ];
    public $js = [
    ];
    public $jsOptions = [
    ];
}