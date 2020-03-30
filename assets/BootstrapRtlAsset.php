<?php
namespace app\assets;

use yii\web\AssetBundle;

class BootstrapRtlAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-rtl/dist';
    public $css = [
        'css/bootstrap-rtl.css',
    ];
    public $depends = [
        'yii\bootstrap4\BootstrapAsset',
    ];
}