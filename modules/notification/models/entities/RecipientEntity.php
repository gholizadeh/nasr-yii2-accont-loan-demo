<?php

namespace app\modules\notification\models\entities;

use Yii;

/**
 * This is the model class for table "{{%notifications}}".
 *
 * @property int $id
 * @property int $status
 * @property int $notification_id
 * @property int $recipient_id
 * @property string $recipient table-> user, customer, client_user, ...
 * @property string $read_at
 * @property string $last_message
 * @property string $created_at
 * @property string $updated_at
 */
class RecipientEntity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipient';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status','notification_id', 'recipient_id', 'recipient'], 'required'],
            [['notification_id', 'recipient_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('notifications', 'ID'),
            'status' => Yii::t('notifications', 'Status'),
            'notification_id' => Yii::t('notifications', 'Notification ID'),
            'recipient_id' => Yii::t('notifications', 'Recipient ID'),
            'recipient' => Yii::t('notifications', 'Recipient Type'),
            'read_at' => Yii::t('notifications', 'Read At'),
            'last_message' => Yii::t('notifications', 'Last Message'),
            'created_at' => Yii::t('notifications', 'Created At'),
            'updated_at' => Yii::t('notifications', 'Updated At'),
        ];
    }
}