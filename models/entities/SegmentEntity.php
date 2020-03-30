<?php

namespace app\models\entities;

use Yii;

/**
 * This is the model class for table "segment".
 *
 * @property int $seg_id
 * @property string $name
 * @property string $remarks
 * @property int $type
 *
 * @property Account[] $accounts
 * @property ClientSegment[] $clientSegments
 * @property User[] $users
 */
class SegmentEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['remarks'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'seg_id' => 'Seg ID',
            'name' => 'Name',
            'remarks' => 'Remarks',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['seg_id' => 'seg_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientSegments()
    {
        return $this->hasMany(ClientSegment::className(), ['segment_id' => 'seg_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['seg_id' => 'seg_id']);
    }
}
