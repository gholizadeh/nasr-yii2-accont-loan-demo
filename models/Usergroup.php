<?php
/**
 *  User Group Model
 *
 *  Customized Model for User Group management.
 *
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\entities\UsergroupEntity;
use app\models\Module;
use yii\helpers\ArrayHelper;
//use yii\web\NotFoundHttpException;

class Usergroup extends UsergroupEntity {

	const STAT_ACTIVE = 1;
	const STAT_DEACTIVE = 2;

    public function getModules() {
        return $this->hasMany(Module::className(), ['module_id' => 'module_id'])->via('accesses');
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['user_group_id','role','remarks','status'];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'user_group_id' => 'شناسه',
            'role' => 'نقش',
            'remarks' => 'توضیح',
            'status' => 'وضعیت',
        ];
    }

    public static function getStatuses(){
        return [
            self::STAT_ACTIVE => 'فعال',
            self::STAT_DEACTIVE => 'غیر فعال'
        ];
    }

	public static function getRoles(){
		return ArrayHelper::map(self::find()->all(), 'user_group_id','role');
	}

    public function getUserGroup($id){
        return $this->find()->with('modules')->andFilterWhere([
            'user_group_id' => $id
        ])->one();

        //throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function search($params)
    {
    	$query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'user_group_id' => $this->user_group_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'role', $this->role]);

        return $dataProvider;
    }
}