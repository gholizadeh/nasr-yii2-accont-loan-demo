# yii2 Notifications

This module provides a way to store notifications in a database and send them across a variety of delivery channels, including mail, telegram, SMS, etc. Or may be displayed in your web interface.

Typically, notifications should be short, informational messages that notify users of something that occurred in your application.

# Installation

## Install

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist gholizadeh/yii2-notifications "*"
```

or add

```php
"gholizadeh/yii2-notifications": "*"
```

to the require section of your `composer.json` file.

## Config

configure in main config file `main.php`
```php
return [
    'components' => [
        ...
        'notifications' => [
            'class' => \app\modules\notification\Notifications::class,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => true,
        ],
    ],
    'modules' => [
        ...
        'notifications' => [
            'class' => app\modules\notification\Module::class,
            //'queueIn' => 'queueNotifications',
            'channels' => [
               'mail' => [
                   'class' => 'app\modules\notification\channels\MailChannel',
                   'from' => [{SENDER EMAIL} => {SENDER NAME}]
               ],
               'telegram' => [
                    'class' => 'app\modules\notification\channels\TelegramChannel'
               ],
               'whatsapp' => [
                   'class' => 'app\modules\notification\channels\TwilioChannel',
                   'channel' => 'whatsapp',
                   'accountSid' => '...',
                   'authToken' => '...',
                   'from' => '+1234567890'
               ],
               'sms' => [
                   'class' => 'app\modules\notification\channels\TwilioChannel',
                   'accountSid' => '...',
                   'authToken' => '...',
                   'from' => '+1234567890'
               ],
            ],
        ],
    ],
];
```

## Use Queue

This module can use queue to manage sending notifications
read [Yii2 Queue](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/README.md) docs for example.

The preferred way to install this extension is through [composer](http://getcomposer.org/download/):

```
php composer.phar require --prefer-dist yiisoft/yii2-queue
```

to configure queue in main config file `main.php`  
```php
return [
    'bootstrap' => [
        ...
        'queueNotifications',
    ],
    'components' => [
        ...
        'queueNotifications' => [
            'class' => \yii\queue\db\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'db' => 'db', // DB connection component or its config
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'notification', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex that used to sync queries
            'mutexTimeout' => 0,
            'ttr' => 5 * 60, // Max time for anything job handling
            'attempts' => 5, // Max number of attempts
        ],
        ...
    ],
    'modules' => [
        ...
        'notifications' => [
            'class' => app\modules\notification\Module::class,
            'queueIn' => 'queueNotifications',
            ...
        ],
    ],
];
```

To use the same database as provided in example config, you have to add a table to the database. Example schema for MySQL:

```SQL
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT 0,
  `priority` int(11) unsigned NOT NULL DEFAULT 1024,
  `reserved_at` int(11) DEFAULT NULL,
  `attempt` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`),
  KEY `reserved_at` (`reserved_at`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB
```
 A complete explenation is available from [Yii2 Queue](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/driver-db.md) and migrations are available.

## Logs to different file

In app config file

```php
'components' => [
    'log' => [
        'targets' => [
            ...
            [
                'class' => 'yii\log\FileTarget',
                'categories' => [
                    'app\modules\notification\channels\*',
                ],
                'logFile' => '@app/runtime/logs/notification-channels.log',
                'logVars' => [],
                'prefix' => function ($message) {
                    return '';
                }
            ],
        ],
    ],
],
```

# Usage

Once the component is configured it may be used for sending notifications:

```php
$recipient = User::findOne(1);
$notification = new InvoicePaid(['params' => ['invoice_id' => $invoice->id], 'level' => 'success']);

Yii::$app->notifications->send($recipient, $nofitication);
```
You can also send multi notifications to multi recipients by using array of notifications and/or recipients

Each notification class should implement NotificationInterface and contains a via method and a variable number of message building methods (such as `exportForMail`) that convert the notification to a message optimized for that particular channel.
Example of notification that covers the case when an invoice has been paid:

```php
use app\modules\notification\models\Notification;

class InvoicePaid extends Notification
{    
    private $invoice;

    public function init(){
        $this->invoice = Invoice::findOne($this->params['invoice_id']);
    }

    public function exportForScreen(){
        return \Yii::createObject([
            'class' => '\app\modules\notification\messages\ScreenMessage',
            'subject' => "invoice paid",
            'body' => "Your <a href='{/invoices/info/$this->invoice->id}'>invoice #{$this->invoice->id}</a> has been paid",
            'bold' => false,
        ]);
    }
    
    public function exportForMail() {
        return \Yii::createObject([
           'class' => '\app\modules\notification\messages\GeneralMessage',
           'subject' => "invoice paid",
           'channelParams' => [
                'from' => 'no-reply@yourdomain.com',
                'view' => ['html' => 'invoice-paid'],
                'viewData' => [
                    'invoiceNumber' => $this->invoice->id,
                    'amount' => $this->invoice->amount
                ]
            ]
        ])
    }
    
    public function exportForTelegram()
    {
        return \Yii::createObject([
            'class' => '\app\modules\notification\messages\GeneralMessage',
            'body' => "Your invoice #{$this->invoice->id} has been paid",
            'channelParams' => [
                'botInComponent' => 'MyTelegramBot',
                'botConfig' => [...] // to add more configs
                'file' => $this->invoice->getPdfFilePath(),
                'fileParams' => [
                    // custom params
                    'fileType' => TelegramChannel::FILE_TYPE_DOCUMENT,
                    'messageMergeType' => TelegramChannel::FILE_MESSAGE_MERGE_TYPE_AS_REPLY,
                    // any other Telegram API params, see below
                    'disable_notification' => true
                ],
            ]
        ]);
    }

    public function exportForWhatsapp()
    {
        return \Yii::createObject([
            'class' => '\app\modules\notification\messages\GeneralMessage',
            'body' => "Your invoice #{$this->invoice->id} has been paid",
            'channelParams' => [
                'mediaUrl' => [$this->invoice->getPdfFilePath()]
            ]
        ]);
    }

    public function exportForWhatsapp()
    {
        return \Yii::createObject([
            'class' => '\app\modules\notification\messages\GeneralMessage',
            'body' => "Your invoice #{$this->invoice->id} has been paid",
        ]);
    }
}
```
## Twilio 

The module uses Twilio SDK to send messages. The recommended method for installing the Twilio SDK is via Composer. 
```
composer require twilio/sdk
```

## Telegram Message with file

* `file` full path to file
* `fileParams` params to manage with file
    * `messageMergeType`
        * TelegramChannel::FILE_MESSAGE_MERGE_TYPE_AS_NO_MERGE **default** send message and file as separate messages
        * TelegramChannel::FILE_MESSAGE_MERGE_TYPE_AS_REPLY send file as reply for sent message
    * `fileType`
        * TelegramChannel::FILE_TYPE_DOCUMENT **default** [Telegram API for more params](https://core.telegram.org/bots/api#senddocument)
        * TelegramChannel::FILE_TYPE_PHOTO [Telegram API for more params](https://core.telegram.org/bots/api#sendphoto)
        * TelegramChannel::FILE_TYPE_AUDIO [Telegram API for more params](https://core.telegram.org/bots/api#sendaudio)
        * TelegramChannel::FILE_TYPE_VIDEO [Telegram API for more params](https://core.telegram.org/bots/api#sendvideo)

To send only file (without message) just set message `body` to `null`

You may use the NotifiableInterface and NotifiableTrait on any of your models:
 
```php
use yii\db\ActiveRecord;
use app\modules\notification\NotifiableTrait;
 
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    use NotifiableTrait;
    
    //default is only mail
    public function viaChannels()
    {
        return ['mail','telegram'];
    }

    public function routeNotificationForMail() 
    {
         return $this->email;
    }

    public function routeNotificationForTelegram() 
    {
        /* should contain telegram chat id */
        return isset($this->socials['telegram']) ? $this->socials['telegram'] : false;
    }
}
 ```

## Exclude a notification for user

you can exclude getting an speceific notification by setting `notificationSettings` attribute 

```php
class User extends ActiveRecord implements \yii\web\IdentityInterface 
{
    ...
    private $notificationSettings = [
      'invoice-paid' => function() {
          return false;
      },
      'some-notification' => false
    ];
    ...
}
 ```

## Event

the notification component trigger a NotificationEvent after each notification send.
you can extend NotificationEvent by onAfterSend function to use the event in your application


mirkhamidov
webzop
tuyakhov
machour