<?php

namespace app\models\entities;

use Yii;
use app\models\Usergroup;
use app\models\Segment;

/**
 * This is the model class for table "user".
 *
 * @property int $user_id
 * @property int $user_group_id
 * @property string $email
 * @property int $seg_id
 * @property int $segment_master
 * @property string $full_name
 * @property string $password
 * @property string $remarks
 * @property string $socials
 * @property int $status
 *
 * @property Segment $seg
 * @property Usergroup $userGroup
 */
class UserEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_group_id', 'email', 'seg_id', 'full_name'], 'required'],
            [['user_group_id', 'seg_id', 'segment_master', 'status'], 'integer'],
            [['socials'], 'string'],
            [['email', 'full_name'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 512],
            [['remarks'], 'string', 'max' => 200],
            [['email'], 'unique'],
            [['seg_id'], 'exist', 'skipOnError' => true, 'targetClass' => Segment::className(), 'targetAttribute' => ['seg_id' => 'seg_id']],
            [['user_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usergroup::className(), 'targetAttribute' => ['user_group_id' => 'user_group_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_group_id' => 'User Group ID',
            'email' => 'Email',
            'seg_id' => 'Segment',
            'segment_master' => 'Segment Master',
            'full_name' => 'Full Name',
            'password' => 'Password',
            'remarks' => 'Remarks',
            'socials' => 'Socials',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */ 
    public function getSeg()
    {
        return $this->hasOne(Segment::className(), ['seg_id' => 'seg_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroup()
    {
        return $this->hasOne(Usergroup::className(), ['user_group_id' => 'user_group_id']);
    }
}
