<?php
namespace app\assets;

use yii\web\AssetBundle;

class Digits2WordsAsset extends AssetBundle
{
    public $sourcePath = '@webroot/libs/digits2words';
    public $js = [
        'digits-to-words.js',
        'num2persian.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}