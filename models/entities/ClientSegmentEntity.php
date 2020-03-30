<?php

namespace app\models\entities;

use Yii;
use app\models\Client;
use app\models\Segment;

/**
 * This is the model class for table "client_segment".
 *
 * @property int $id
 * @property int $client_id
 * @property int $segment_id
 * @property string $date
 * @property int $status
 *
 * @property Client $client
 * @property Segment $segment
 */
class ClientSegmentEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'segment_id', 'date'], 'required'],
            [['client_id', 'segment_id', 'status'], 'integer'],
            [['date'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'client_id']],
            [['segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Segment::className(), 'targetAttribute' => ['segment_id' => 'seg_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'segment_id' => 'Segment ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegment()
    {
        return $this->hasOne(Segment::className(), ['seg_id' => 'segment_id']);
    }
}
