<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\LoanTypeEntity;
use yii\data\ActiveDataProvider;
use app\components\TimeLogBehavior;
use app\components\ChangeLogBehavior;
use yii\helpers\ArrayHelper;

class LoanType extends LoanTypeEntity
{
    public function behaviors(){
        return [
            TimeLogBehavior::className(),
            [
                'class' => ChangeLogBehavior::className(),
                'excludedAttributes' => ['modified_date','last_modified_by'],
            ]
        ];
    }
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['id','name','description'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'name' => 'عنوان',
            'description' => 'توضیح',        
        ];
    }

    public static function getLoanTypes(){
        return ArrayHelper::map(self::find()->all(), 'id','name');
    }

    public function search($params)
    {
        $query = LoanType::find();

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