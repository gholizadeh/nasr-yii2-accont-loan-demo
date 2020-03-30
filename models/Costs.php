<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\CostsEntity;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class Costs extends CostsEntity
{
    const STAT_ACTIVE = 1;
    const STAT_DEACTIVE = 2;

    public static function getStatuses(){
        return [
            self::STAT_ACTIVE => 'فعال',
            self::STAT_DEACTIVE => 'غیر فعال'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['id','name','amount','description', 'status'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'name' => 'عنوان',
            'amount' => 'مبلغ',
            'description' => 'توضیح',
            'status' => 'وضعیت',
        ];
    }

    public static function getCosts($field){
        return ArrayHelper::map(self::find()->all(), 'id', $field);
    }

    public function search($params)
    {
        $query = Costs::find();

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
                    'amount',
                    'description',
                    'status'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}