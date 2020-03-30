<?php
/**
 * Users Access
 *
 *  Customized Model for Application user access.
 *
 * @author: Saeed Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\ModuleEntity;

class Module extends ModuleEntity {
	
	/*
	public function UpdateModules($id) {
		$ignore = array();
		
		$files = array();
		// Make path into an array
		$path = array(Yii::getAlias('@app') . 'controllers/*');
		// While the path array is still populated keep looping through
		while (count($path) != 0) {
			$next = array_shift($path);
			foreach (glob($next) as $file) {
				// If directory add to path array
				if (is_dir($file)) {
					$path[] = $file . '/*';
				}
				// Add the file to the files to be deleted array
				if (is_file($file)) {
					$files[] = $file;
				}
			}
		}
		// Sort the file array
		sort($files);
					
		foreach ($files as $file) {
			$controller = substr($file, strlen(Yii::getAlias('@app') . 'controllers/'));
			$controller_name = substr($controller, 0, strrpos($controller, '.'));
			if (!in_array($controller_name, $ignore)) {
				$data['permissions'][] = $controller_name;
			}
		}
	}
	*/
}