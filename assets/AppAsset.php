<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/site.js',
        'js/jquery.cookie.js',
        'js/storage.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'app\assets\BootstrapRtlAsset',
        'app\assets\FontAwesomeAsset',
        'app\assets\FontAsset',
        'app\assets\AlertifyAsset'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}
