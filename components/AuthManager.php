<?php
namespace app\components;

use Yii;
use app\models\Access;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

class AuthManager extends \yii\web\User {
	
	public function __get($name){
		try{
			return parent::__get($name);
		}catch (\yii\base\UnknownPropertyException $e){
			if($this->identity && $this->identity->hasAttribute($name)){
				return $this->identity->$name;
			}else{
				throw $e;
			}
		}
	}
	
	public function getAccess($cur_ctrl) {
		$useracc_class = new Access ();
		$useraccess = [];

		if(!$this->isGuest)
			$useraccess = $useracc_class->getAccess ( $cur_ctrl, $this->user_group_id );

		$rules = [];
		$actions = [];
		if(!empty($useraccess)){
			$actions = ArrayHelper::map($useraccess, 'module.module_id', 'module.action');

			if ($cur_ctrl == "segment" && $this->segment_master)
				$actions[] = "change";

			array_push ( $rules, [
					'allow' => true, // allow logged in user to perform ...
					'actions' => $actions,
					'roles' => ['@']
				]
			);
		}
		
		if ($cur_ctrl == Yii::$app->defaultRoute)
			array_push ( $rules, [
					'actions' => ['login', 'logout', 'captcha', 'error', 'about'],
					'allow' => true, // allow all users to perform only above actions
				]
			);

		return [
			'class' => AccessControl::className(),
			'rules' => $rules,
		];
	}

	public function getSegment(){
		$session = Yii::$app->session;
		return $session->get('seg_id', Yii::$app->user->seg_id);
	}

	public function setSegment($seg_id){
		$session = Yii::$app->session;
		return $session->set('seg_id', $seg_id);
	}
	
	public function hasAccess($controller, $action) {
		if(!$this->isGuest){
			if($this->segment_master && $controller == "segment" && $action == "change"){
				return true;
			}else{
				$access_model = new Access ();
				return $access_model->hasAccess ( $controller, $action, $this->user_group_id);
			}
		}

		return false;
	}
	
	/*
	public function is_related($data){
		$user_id = (int)$this->id;
		//just user and their managers can see the pip
		if ($user_id == (int)$data->user_id)
			return true;

		return false;
	}
	*/
	
}