<?php

namespace app\models\entities;

use Yii;
use app\models\User;
use app\models\Segment;

/**
 * This is the model class for table "user_assets".
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property int $seg_id
 * @property string $collection
 *
 * @property User $user
 * @property Segment $seg
 */
class UserAssetsEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_assets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'user_id', 'seg_id', 'collection'], 'required'],
            [['user_id', 'seg_id'], 'integer'],
            [['collection'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'user_id' => 'User ID',
            'seg_id' => 'Seg ID',
            'collection' => 'Collection',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeg()
    {
        return $this->hasOne(Segment::className(), ['seg_id' => 'seg_id']);
    }
}
