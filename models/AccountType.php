<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\AccountTypeEntity;
use yii\data\ActiveDataProvider;
use app\components\TimeLogBehavior;
use yii\helpers\ArrayHelper;

class AccountType extends AccountTypeEntity
{
    public function behaviors(){
        return [TimeLogBehavior::className()];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['id','name','description','charge_rate'];
        return $scenarios;
    }

    public static function getAccountTypes(){
        return ArrayHelper::map(self::find()->all(), 'id','name');
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'name' => 'عنوان',
            'description' => 'توضیح',
            'charge_rate' => 'شارژ ماهانه',
        ];
    }

    public function search($params)
    {
        $query = AccountType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => [
                    'id',
                    'name',
                    'description',
                    'charge_rate'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}