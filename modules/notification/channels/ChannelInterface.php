<?php

namespace app\modules\notification\channels;

use app\modules\notification\models\Notification;
use app\modules\notification\NotifiableInterface;
use app\modules\notification\NotificationInterface;

interface ChannelInterface
{
    /**
     * @param NotifiableInterface $recipient
     * @param NotificationInterface $notification
     * @return mixed channel response
     * @throws \Exception
     */
    public function send(NotifiableInterface $recipient, NotificationInterface $notification);
}