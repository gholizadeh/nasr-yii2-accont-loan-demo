<?php
namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/font-awesome';
    public $css = [
        'css/all.min.css',
    ];

    /**
     * Sets the publishOptions property.
     * Needed because it's necessary to concatenate the namespace value.
     */
    public function init()
    {
        $this->publishOptions = [
            'forceCopy' => YII_DEBUG,
            'beforeCopy' => __NAMESPACE__ . '\FontAwesomeAsset::filterFolders'
        ];

        parent::init();
    }


    /**
     * Filters the published files and folders.
     * It's not necessary publish all files and folders from the font-awesome package
     * Just the CSS and FONTS folder.
     * @param string $from
     * @param string $to
     * @return bool true to publish to file/folder.
     */
    public static function filterFolders($from, $to)
    {
        $validFilesAndFolders = [
            'css',
            'webfonts',
            'all.min.css',
            'fa-brands-400.eot',
            'fa-brands-400.svg',
            'fa-brands-400.ttf',
            'fa-brands-400.woff',
            'fa-brands-400.woff2',
            'fa-regular-400.eot',
            'fa-regular-400.svg',
            'fa-regular-400.ttf',
            'fa-regular-400.woff',
            'fa-regular-400.woff2',
            'fa-solid-900.eot',
            'fa-solid-900.svg',
            'fa-solid-900.ttf',
            'fa-solid-900.woff',
            'fa-solid-900.woff2',
        ];

        $pathItems = array_reverse(explode(DIRECTORY_SEPARATOR, $from));
        if (in_array($pathItems[0], $validFilesAndFolders)) 
            return true;
        else 
            return false;
    }
}