<?php

namespace app\models\entities;

use Yii;
use app\models\Access;

/**
 * This is the model class for table "module".
 *
 * @property int $module_id
 * @property string $controller
 * @property string $action
 * @property string $module_desc
 * @property int $status
 *
 * @property Access[] $accesses
 */
class ModuleEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'module';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller', 'action'], 'required'],
            [['status'], 'integer'],
            [['controller', 'action'], 'string', 'max' => 100],
            [['module_desc'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'module_id' => 'Module ID',
            'controller' => 'Controller',
            'action' => 'Action',
            'module_desc' => 'Module Desc',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccesses()
    {
        return $this->hasMany(Access::className(), ['module_id' => 'module_id']);
    }
}
