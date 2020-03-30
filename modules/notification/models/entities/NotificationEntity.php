<?php

namespace app\modules\notification\models\entities;

use Yii;
use app\modules\notification\models\Recipient;

/**
 * This is the model class for table "{{%notifications}}".
 *
 * @property int $id
 * @property string $level Info, Warning ...
 * @property string $type Notification`s full class name
 * @property array $params Notification`s params
 * @property string $created_at
 * @property string $updated_at
 */
class NotificationEntity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('notifications', 'ID'),
            'level' => Yii::t('notifications', 'Level'),
            'type' => Yii::t('notifications', 'Type'),
            'params' => Yii::t('notifications', 'Params'),
            'created_at' => Yii::t('notifications', 'Created At'),
            'updated_at' => Yii::t('notifications', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipients()
    {
        return $this->hasMany(Recipient::className(), ['notification_id' => 'id']);
    }
}