<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'language' => 'fa',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'notifications' => [
            'class' => app\modules\notification\Module::class,
            //'queueIn' => 'queueNotifications',
            'channels' => [
               'mail' => [
                   'class' => 'app\modules\notification\channels\MailChannel',
                   'from' => ['noreply@ams.gnro.ir' => 'AMS Notification']
               ],
               'telegram' => [
                    'class' => 'app\modules\notification\channels\TelegramChannel'
               ]
            ],
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '1SAUtL3rZ4zs1suetSLWWOZdjOQkqXt6',
            'baseUrl' => ''
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\components\AuthManager',
            'identityClass' => 'app\models\User',
            'loginUrl' => ['site/login'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'notifications' => [
            'class' => app\modules\notification\Notifications::class,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['noreply@ams.gnrco.ir' => 'AMS notification'],
            ],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',                                
                '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
                '<action:(login|logout|captcha)>' => 'site/<action>'
            ]
        ],
        'pDate' => [
            'class' => 'app\components\persianDate\DateTime'
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
