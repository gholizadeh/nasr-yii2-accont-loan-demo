<?php
namespace app\components;

use Yii;
use yii\helpers\StringHelper;
use yii\helpers\Json;
class MapboxHelper extends StringHelper
{
    /**
     * Mapbox and plugins scripts
     * @var array
     */
    private static $_scripts = [
        'default' => [
            'js' => ['https://api.tiles.mapbox.com/mapbox.js/v3.1.1/mapbox.js'],
            'css' => ['https://api.tiles.mapbox.com/mapbox.js/v3.1.1/mapbox.css']
        ],
        'drawing' => [
            'js' => [
                'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-geodesy/v0.1.0/leaflet-geodesy.js',
                'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-draw/v0.4.10/leaflet.draw.js'
            ],
            'css' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-draw/v0.4.10/leaflet.draw.css'],
        ],
        'fullScreen' => [
            'js' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'],
            'css' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'],
        ],
        'minimap' => [
            'js' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-minimap/v1.0.0/Control.MiniMap.js'],
            'css' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.0/leaflet.fullscreen.css'],
        ],
        'omnivore' => [
            'js' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.2.0/leaflet-omnivore.min.js'],
            'css' => [],
        ],
        'markercluster' => [
            'js' => ['https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v1.0.0/leaflet.markercluster.js'],
            'css' => [
                'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v1.0.0/MarkerCluster.css',
                'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v1.0.0/MarkerCluster.Default.css',
            ],
        ],
    ];
    /**
     * Registra scripts do MapBox
     * @param Object $view
     * @param array $plugins
     * @param boolean $useDefault
     * @return void
     */
    public static function registerScript($view, $plugins = [], $useDefault = true)
    {
        if($useDefault) {
            self::_registerScriptFiles($view, 'default');
        }
        if(isset($plugins['default'])) {
            unset($plugins['default']);
        }
        foreach($plugins as $plugin) {
            if(!isset(self::$_scripts[$plugin])) {
                continue;
            }
            self::_registerScriptFiles($view, $plugin);
        }
        return;
    }
    /**
     * Register script files into view file
     * @return void
     */
    private static function _registerScriptFiles($view, $plugin)
    {
        foreach(self::$_scripts[$plugin]['js'] as $script) {
            $view->registerJsFile($script);
        }
        foreach(self::$_scripts[$plugin]['css'] as $style) {
            $view->registerCssFile($style);
        }
        return;
    }
}