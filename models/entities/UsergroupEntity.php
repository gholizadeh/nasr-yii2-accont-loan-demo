<?php

namespace app\models\entities;

use Yii;
use app\models\User; 
use app\models\Access; 

/**
 * This is the model class for table "usergroup".
 *
 * @property int $user_group_id
 * @property string $role
 * @property string $remarks
 * @property int $status
 *
 * @property Access[] $accesses
 * @property User[] $users
 */
class UsergroupEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usergroup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role'], 'required'],
            [['status'], 'integer'],
            [['role'], 'string', 'max' => 20],
            [['remarks'], 'string', 'max' => 200],
            [['role'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_group_id' => 'User Group ID',
            'role' => 'Role',
            'remarks' => 'Remarks',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccesses()
    {
        return $this->hasMany(Access::className(), ['user_group_id' => 'user_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['user_group_id' => 'user_group_id']);
    }
}
