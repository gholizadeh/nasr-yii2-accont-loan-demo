<?php

namespace app\modules\notification\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Inflector;
use app\modules\notification\NotificationInterface;
use app\modules\notification\models\entities\NotificationEntity;
use app\modules\notification\messages\AbstractMessage;

class Notification extends NotificationEntity implements NotificationInterface
{
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

    /**
     * @return array
     */
    public function broadcastOn()
    {
        $channels = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (strpos($method, 'exportFor') === false) {
                continue;
            }
            $channel = str_replace('exportFor', '', $method);
            if (!empty($channel)) {
                $channels[] = Inflector::camel2id($channel);
            }
        }
        return $channels;
    }

    public function storeNotif($recipients){
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $this->level = $this->level ?? 'info';
            $this->type = get_class($this);
            $this->params = is_array($this->params) ? serialize($this->params) : '';
            $this->save();

            foreach ($recipients as $recipient) {
                $rec = new Recipient();
                $rec->status = Recipient::STAT_QUEUED;
                $rec->recipient_id = $recipient->user_id;
                $rec->recipient = get_class($recipient);
                $rec->notification_id = $this->getPrimaryKey();
                $rec->save();
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    /**
     * Determines on which channels the notification will be delivered.
     * ```php
     * public function exportForMail() {
     *      return Yii::createObject([
     *          'class' => 'app\modules\notification\messages\GeneralMessage',
     *          'view' => ['html' => 'welcome'],
     *          'viewData' => [...]
     *      ])
     * }
     * ```
     * @param $channel
     * @return AbstractMessage
     * @throws \InvalidArgumentException
     */
    public function exportFor($channel)
    {
        if (method_exists($this, $method = 'exportFor'.Inflector::id2camel($channel))) {
            return $this->{$method}();
        }
        throw new \InvalidArgumentException("Can not find message export for chanel `{$channel}`");
    }
}