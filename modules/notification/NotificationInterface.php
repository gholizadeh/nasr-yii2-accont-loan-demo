<?php

namespace app\modules\notification;

use app\modules\notification\messages\AbstractMessage;

interface NotificationInterface
{
    /**
     * Export notification as message for given channel.
     * @param string $channel
     * @return AbstractMessage
     */
    public function exportFor($channel);
    /**
     * Determines on which channels the notification will be delivered
     * @return array
     */
    public function broadcastOn();
}