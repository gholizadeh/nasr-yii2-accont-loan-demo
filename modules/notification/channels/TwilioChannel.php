<?php

namespace app\modules\notification\channels;

use Yii;
use app\modules\notification\NotifiableInterface;
use app\modules\notification\NotificationInterface;
use yii\base\Component;
use yii\di\Instance;
use Twilio\Rest\Client;

class TwilioChannel extends Component implements ChannelInterface
{
    /**
     * A Twilio account SID
     * @var string
     */
    public $accountSid;
    /**
     * A Twilio account auth token
     * @var string
     */
    public $authToken;
    /**
     * A Twilio phone number (in E.164 format) or alphanumeric sender ID enabled for the type of message you wish to send.
     * @var string
     */
    public $from;
    /**
     * A media address ex: ["https://c1.staticflickr.com/3/2899/14341091933_1e92e62d12_b.jpg"]
     * @var string|array
     */
    public $mediaUrl;
    /**
     * @var Client|array|string
     */
    public $restClient;
    /**
     * @var Channel sms|whatsapp
     */
    public $channel = 'sms';
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->restClient)) {
            $this->restClient = [
                'class' => Client::className(),
                'sid' => $this->accountSid,
                'token' => $this->authToken,
            ];
        }
        $this->restClient = Instance::ensure($this->restClient, Client::className());
    }
    /**
     * @inheritdoc
     */
    public function send(NotifiableInterface $recipient, NotificationInterface $notification)
    {
        $message = $notification->exportFor($this->channel);
        if (!empty($message->channelParams)) {
            Yii::configure($this, $message->channelParams);
        }

        $channel = $this->channel == 'whatsapp' ? 'whatsapp:' : '';
        $to = $channel.$recipient->routeNotificationFor($this->channel);
        $data = [
            'from' => $channel.$this->from,
            'body' => $message->body
        ];
        if (isset($this->mediaUrl)) {
            $data['mediaUrl'] = $this->mediaUrl;
        }

        $result = $this->restClient->messages->create($to, $data);
        return $result->sid;
    }
}