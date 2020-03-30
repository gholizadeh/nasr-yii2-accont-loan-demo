<?php
/**
 * Executes delayed send notifications
 */
namespace app\modules\notification\tasks;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\queue\RetryableJobInterface;
use app\modules\notification\models\Recipient;
use app\modules\notification\models\Notification;

class QueueNotificationTask extends BaseObject implements RetryableJobInterface
{
    public $notificationId;
    /** @var Notification - used in getModule() */
    private $notificationModel;
    /** @var Module */
    private $_module;
    /** @inheritdoc */
    public function execute($queue)
    {
        $this->log("Started with notificationId: {$this->notificationId}");
        $this->notificationModel = Notification::findOne($this->notificationId);
        $notification = Yii::createObject([
            'class' => $this->notificationModel->type,
            'params' => $this->notificationModel->params,
            'level' => $this->notificationModel->level,
        ]);

        if (!$notification) {
            $this->log("Model \"Notification\" with id \"{$this->notificationId}\" Not found", 'error');
            return false;
        }

        foreach ($this->notificationModel->recipients as $recipient) {
            if (!$recipient->notifiable->shouldReceiveNotification($notification)) {
                continue;
            }
            $channels = array_intersect(
                $recipient->notifiable->viaChannels(), 
                $notification->broadcastOn(),
                array_keys($this->getModule()->channels)
            );

            $recipient->updateNotificationStat(Recipient::STAT_PROCESSING);
            $results = [];
            foreach ($channels as $channel) {
                $channelInstance = $this->getModule()->getChannelInstance($channel);
                try {
                    \Yii::info("Sending notification " . get_class($notification) . " to " . get_class($recipient->notifiable) . " via {$channel}", __METHOD__);
                    $response = $channelInstance->send($recipient->notifiable, $notification);
                } catch (\Exception $e) {
                    $response = $e;
                }

                $results[] = $response;
                $this->trigger(self::EVENT_AFTER_SEND, new NotificationEvent([
                    'notification' => $notification,
                    'recipient' => $recipient,
                    'channel' => $channel,
                    'response' => $response
                ]));
            }
            $recipient->updateNotificationStat(Recipient::STAT_SENT, $results);
        }

        $this->log("Ended with notificationId: {$this->notificationId}");
    }
    /**
     * local logger
     * @param $data
     * @param string $type
     */
    private function log($data, $type='info')
    {
        Yii::{$type}($data, 'QueueNotificationTask');
    }
    /** @inheritdoc */
    public function getTtr()
    {
        return 15 * 60;
    }
    /** @inheritdoc */
    public function canRetry($attempt, $error)
    {
        return ($attempt < 5);
    }
    /**
     * @return Module
     * @throws InvalidConfigException
     */
    public function getModule()
    {
        if (!$this->_module) {
            $moduleId = $this->notificationModel->params['sysParams']['queue']['moduleId'];
            if (!Yii::$app->hasModule($moduleId)) {
                throw new InvalidConfigException(Yii::t('notifications', 'There is no configured module "{moduleId}"', [
                    'moduleId' => $moduleId,
                ]));
            }
            $this->_module = Yii::$app->getModule($moduleId);
        }
        return $this->_module;
    }
}