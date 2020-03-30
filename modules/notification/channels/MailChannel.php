<?php

namespace app\modules\notification\channels;

use Yii;
use yii\base\Component;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;
use app\modules\notification\NotifiableInterface;
use app\modules\notification\NotificationInterface;

class MailChannel extends Component implements ChannelInterface
{
    public $charset = null;
    public $from = null;
    public $replyTo = null;
    public $to = null;
    public $cc = null;
    public $bcc = null;
    public $subject = null;
    public $textBody = null;
    public $htmlBody = null;
    public $attachFilePath = null;
    public $attachOptions = [];
    /** @var string|array|null $view  */
    public $view = null;
    /** @var array The parameters (name-value pairs) available in the view file */
    public $viewData = [];

    /** @var Message */
    private $message;
    /** @var Mailer */
    private $mailer;
    /** @var bool Flag, view availability to send message */
    private $maySend = false;
    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->mailer = Yii::$app->mailer;
    }

    /** @inheritdoc */
    public function send(NotifiableInterface $recipient, NotificationInterface $notification)
    {
        $message = $notification->exportFor('mail');
        if (!empty($message->channelParams)) {
            Yii::configure($this, $message->channelParams);
        }

        $this->log('mailer message sending processing');
        try {
            $result = '';

            $this->message = $this->mailer->compose($this->view, $this->viewData);
            $this->to = $recipient->routeNotificationFor('mail');
            $this->subject = isset($message->subject) ? $message->subject : $this->subject;

            if (!empty($this->from) && !empty($this->to)) {
                $this->maySend = true;
            } else {
                if (empty($this->from)) {
                    $this->log('"from" data is empty', 'error');
                }
                if (empty($this->to)) {
                    $this->log('"to" data is empty', 'error');
                }
            }
            if ($this->maySend === true) {
                if ($this->charset !== null) {
                    $this->message->setCharset($this->charset);
                }
                if ($this->from !== null) {
                    $this->message->setFrom($this->from);
                }
                if ($this->replyTo !== null) {
                    $this->message->setReplyTo($this->replyTo);
                }
                if ($this->to !== null) {
                    $this->message->setTo($this->to);
                }
                if ($this->cc !== null) {
                    $this->message->setCc($this->cc);
                }
                if ($this->bcc !== null) {
                    $this->message->setBcc($this->bcc);
                }
                if ($this->subject !== null) {
                    $this->message->setSubject($this->subject);
                }
                if ($this->textBody !== null) {
                    $this->message->setTextBody($this->textBody);
                }
                if ($this->htmlBody !== null) {
                    $this->message->setHtmlBody($this->htmlBody);
                }
                if ($this->attachFilePath !== null) {
                    $this->message->attach($this->attachFilePath, $this->attachOptions);
                }
                $this->message->send();
                $result = 'Message send';  
            }
            if ($this->maySend === false) {
                $result = 'Nothing to send, not enough data';
            }
            if (!$this->maySend && empty($result)) {
                $result = 'Something went wrong';
            }

            $this->log($result);
            return 'Mail result: '.$result;

        } catch (\Exception $e) {
            $this->log('Exception: ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString(), 'error');
            return $e->getMessage();
        }
    }
    /**
     * Logging
     * @param $data
     * @param string $type
     */
    private function log($data, $type = 'info')
    {
        Yii::$type($data, __CLASS__);
    }
}