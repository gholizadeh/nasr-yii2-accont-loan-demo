<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\ClientEntity;
use app\models\entities\ClientSegmentEntity;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class Client extends ClientEntity
{
    const STAT_ACTIVE = 1;
    const STAT_DEACTIVE = 2;

    public $status;
    public $date_added;

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['insert'] = $scenarios['update'] = ['client_id','name','cellphone','remarks','status'];
        $scenarios['search'] = ['client_id','name','cellphone','remarks','status'];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'client_id' => 'شناسه',
            'name' => 'نام کامل',
            'cellphone' => 'تلفن',
            'remarks' => 'توضیحات',
            'status' => 'وضعیت',
        ];
    }

    public static function getStatuses(){
        return [
            self::STAT_ACTIVE => 'فعال',
            self::STAT_DEACTIVE => 'غیر فعال'
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $clientSegment = $this->getClientToSegment();
        $clientSegment->status = $this->status;
        $clientSegment->save();
        parent::afterSave($insert, $changedAttributes);
    }

    public function getClientToSegment(){
        if (($model = ClientSegmentEntity::find()->where(['client_id'=>$this->client_id, 'segment_id'=>Yii::$app->user->getSegment()])->one()) !== null) {
            return $model;
        }
        $clientSegment = new ClientSegmentEntity();
        $clientSegment->client_id = $this->client_id;
        $clientSegment->segment_id = Yii::$app->user->getSegment();
        $clientSegment->date = new Expression('UTC_TIMESTAMP()');

        return $clientSegment;
    }

    public function afterFind() {
        parent::afterFind();
        $this->status = $this->clientSegments[0]->status;
        $this->date_added = $this->clientSegments[0]->date;
        return $this;
    }

    public static function getSegmentClients(){
        return self::find()
                    ->joinWith(['clientSegments'])
                    ->select(['client.client_id', 'name as value', 'name as label','client.client_id as id'])
                    ->where(['client_segment.segment_id'=>Yii::$app->user->getSegment()])
                    ->asArray()->all();
    }
    
    public static function segmentClientsCount(){
        return self::find()
                    ->joinWith(['clientSegments'])
                    ->where(['client_segment.segment_id'=>Yii::$app->user->getSegment()])
                    ->count();
	}

    public function search($params)
    {
        $query = Client::find()->joinWith(['clientSegments']);

        $query->where(['client_segment.segment_id'=>Yii::$app->user->getSegment()]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'client_id' => SORT_DESC,
                ],
                'attributes' => [
                    'client_id',
                    'name',
                    'cellphone',
                    'remarks',
                    'status' => [
                        'asc' => ['client_segment.status' => SORT_ASC],
                        'desc' => ['client_segment.status' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'client_segment.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'cellphone', $this->cellphone])
              ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}