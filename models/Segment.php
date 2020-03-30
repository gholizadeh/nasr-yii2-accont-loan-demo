<?php
/**
 *  Segment Model
 *
 *  Customized Model for Application segment.
 *
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use app\models\entities\SegmentEntity;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class Segment extends SegmentEntity
{
    const TYPE_NORMAL = 1;
    const TYPE_MASTER = 2;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['seg_id','name','remarks','type'];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'seg_id' => 'شناسه',
            'name' => 'نام صندوق',
            'remarks' => 'توضیح',
            'type' => 'نوع',
        ];
    }

    public static function getSegments(){
        return ArrayHelper::map(self::find()->all(), 'seg_id','name');
    }

    public static function getTypes(){
        return [
            self::TYPE_NORMAL => 'عادی',
            self::TYPE_MASTER => 'فرا صندوق'
        ];
    }

    public function search($params)
    {
        $query = Segment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'seg_id' => SORT_DESC,
                ],
                'attributes' => [
                    'seg_id',
                    'name',
                    'remarks',
                    'type'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'seg_id' => $this->seg_id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}