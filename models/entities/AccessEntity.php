<?php

namespace app\models\entities;

use Yii;
use app\models\Usergroup;
 
/**
 * This is the model class for table "access".
 *
 * @property int $access_id
 * @property int $module_id
 * @property int $user_group_id
 *
 * @property Module $module
 * @property Usergroup $userGroup
 */
class AccessEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['module_id', 'user_group_id'], 'required'],
            [['module_id', 'user_group_id'], 'integer'],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModuleEntity::className(), 'targetAttribute' => ['module_id' => 'module_id']],
            [['user_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usergroup::className(), 'targetAttribute' => ['user_group_id' => 'user_group_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'access_id' => 'Access ID',
            'module_id' => 'Module ID',
            'user_group_id' => 'User Group ID'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(ModuleEntity::className(), ['module_id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroup()
    {
        return $this->hasOne(Usergroup::className(), ['user_group_id' => 'user_group_id']);
    }
}
