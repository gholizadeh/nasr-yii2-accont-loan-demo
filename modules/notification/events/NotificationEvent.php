<?php

namespace app\modules\notification\events;

use app\modules\notification\NotifiableInterface;
use app\modules\notification\NotificationInterface;
use yii\base\Event;

class NotificationEvent extends Event
{
    /**
     * @var NotificationInterface
     */
    public $notification;
    /**
     * @var NotifiableInterface
     */
    public $recipient;
    /**
     * @var string
     */
    public $channel;
    /**
     * @var mixed
     */
    public $response;
}