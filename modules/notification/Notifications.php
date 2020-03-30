<?php

namespace app\modules\notification;

use function get_class;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use app\modules\notification\models\Recipient;
use app\modules\notification\tasks\QueueNotificationTask;
use app\modules\notification\events\NotificationEvent;

/**
 * Class Notifications
 * @package app\modules\notification
 *
 * @property Module $module
 */
class Notifications extends \yii\base\Component
{
    /**
     * @event NotificationEvent an event raised right after notification has been sent.
     */
    const EVENT_AFTER_SEND = 'afterSend';

    /** @var string Modul's name from config of the app */
    public $moduleId = 'notifications';
    /** @var Module */
    private $_module;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        if (!Yii::$app->hasModule($this->moduleId)) {
            throw new InvalidConfigException(Yii::t('notifications', 'There is no configured module "{moduleId}"', [
                'moduleId' => $this->moduleId,
            ]));
        }
        $this->_module = Yii::$app->getModule($this->moduleId);
        if (!empty($this->_module->queueIn) && empty(Yii::$app->queue)) {
            throw new InvalidConfigException(Yii::t('notifications', 'Queue is not configured'));
        }
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Sends the given notifications through available channels to the given notifiable entities.
     * You may pass an array in order to send multiple notifications to multiple recipients.
     * 
     * @param array|NotifiableInterface $recipients the recipients that can receive given notifications.
     * @param array|NotificationInterface $notifications the notification that should be delivered.
     * @return void
     * @throws InvalidConfigException
     */
    public function send($recipients, $notifications)
    {
        if (!is_array($recipients)) {
            /**
             * @var $recipients NotifiableInterface[]
             */
            $recipients = [$recipients];
        }
        
        if (!is_array($notifications)){
            /**
             * @var $notifications NotificationInterface[]
             */
            $notifications = [$notifications];
        }

        //store notifications
        foreach ($notifications as $notification) {
            $notification->storeNotif($recipients);
        }
        
        foreach ($notifications as $notification) {
            $isQueue = (!empty($this->getModule()->queueIn));
            if ($isQueue) {
                /** @var Queue $queue */
                $queue = Yii::$app->get($this->getModule()->queueIn);
                $jobId = $queue->push(new QueueNotificationTask([
                    'notificationId' => $notification->id,
                ]));
                $notification->params = ArrayHelper::merge($notification->params, [
                    'sysParams' => [
                        'queue' => [
                            'jobId' => $jobId,
                            'moduleId' => $this->moduleId,
                        ],
                    ],
                ]);
                $notification->update(false);
            }else{
                foreach ($notification->recipients as $recipient) {
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
            }
        }
    }
}