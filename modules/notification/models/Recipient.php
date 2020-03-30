<?php

namespace app\modules\notification\models;

use app\modules\notification\models\entities\RecipientEntity;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Recipient extends RecipientEntity
{
    const STAT_QUEUED = 1;
    const STAT_PROCESSING = 2;
    const STAT_SENT = 3;
    const STAT_READED = 4;
    const STAT_DELETED = 5;

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    public static function getStatuses(){
        return [
            self::STAT_QUEUED => 'In queue',
            self::STAT_PROCESSING => 'In process',
            self::STAT_SENT => 'New (Sent)',
            self::STAT_READED => 'Readed',
            self::STAT_DELETED => 'Deleted',
        ];
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(false, ['read_at' => date('Y-m-d H:i:s')]);
        }
    }

    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->update(false, ['read_at' => null]);
        }
    }
    /**
     * Determine if a notification has been read.
     *
     * @return bool
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }
    /**
     * Determine if a notification has not been read.
     *
     * @return bool
     */
    public function isUnread()
    {
        return $this->read_at === null;
    }

    public function getNotifiable()
    {
        return $this->hasOne($this->recipient, ['user_id' => 'recipient_id']);
    }

	public function updateNotificationStat($status, $message = null){
        $this->status = $status;
        $this->last_message = $message ? serialize($message) : $this->last_message;
        $this->update(false);
    }
}