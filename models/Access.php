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
use app\models\entities\AccessEntity;

class Access extends AccessEntity {
	
	/**
	 * for getting array of actions avilable for logedin user
	 * in a specefic controller
	 *
	 * @param integer $controller        	
	 */
	public function getAccess($controller, $role = null) {
		if (!empty($role)) {
			return $this->find()
			->joinWith([
				'module'
			])
			->where(["access.user_group_id" => $role, "module.controller" => $controller])
			->all();
		} else {
			return null;
		}
	}
	public function hasAccess($controller, $action, $role) {
		return $this->find()
		->joinWith([
			'userGroup',
			'module'
		])
		->where(["access.user_group_id" => $role, "module.controller" => $controller, "module.action" => $action])
		->one();
	}
	
	/**
	 * this function gets an array of user access which has been sent by create/update action
	 * and insert them in db
	 *
	 * @param unknown_type $access_array        	
	 */
	public static function insertAccess($access_array) {
		return Yii::$app->db->createCommand()->batchInsert(Access::tableName(), 
					['module_id', 'user_group_id'], $access_array)->execute();
	}
	
	/**
	 * this function delete all asigned access to user group
	 *
	 * @param int $id        	
	 */
	public function deleteAccess_byuserid($id) {
		$topic = Yii::app ()->db->createCommand ()->delete ( $this->tableName (), 'user_group_id=:id', array (
				':id' => $id 
		) );
		
		return $topic;
	}
}