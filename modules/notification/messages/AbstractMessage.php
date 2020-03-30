<?php

namespace app\modules\notification\messages;

use yii\base\BaseObject;

abstract class AbstractMessage extends BaseObject
{
    /**
     * The subject of the notification.
     * @var string
     */
    public $subject;
    /**
     * The notification's message body
     * @var string
     */
    public $body;
    /**
     * The channel parmas
     * @var array
     */
    public $channelParams = [];
}