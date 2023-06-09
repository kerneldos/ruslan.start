<?php

use yii\helpers\ArrayHelper;

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'KXpWBRltNSnDqHCiHLnKDKtvJsxFtMHj',
            'baseUrl' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY, // используем временный редирект вместо постоянного
            ],
            'rules' => [
                '' => 'site/index',
                'document/<id>' => 'site/view',
                '<_a:login|logout>' => 'site/<_a>',
                '<_c>' => '<_c>/index',
                '<_c>/<_a>' => '<_c>/<_a>',
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'yandex' => [
                    'class' => 'app\components\services\Yandex',
                    'apiBaseUrl' => 'https://cloud-api.yandex.net/v1/',
                    'normalizeUserAttributeMap' => [
                        'email' => function ($attributes) {
                            return $attributes['email']
                                ?? $attributes['default_email']
                                ?? current($attributes['emails'] ?? [])
                                ?: null;
                        }
                    ],
                ],
                'samba' => [
                    'class' => 'app\components\services\Samba',
                ],
                'bitrix' => [
                    'class' => 'app\components\services\Bitrix',
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['172.21.0.1', '127.0.0.1', '::1'],
        'panels' => [
            'elasticsearch' => [
                'class' => 'yii\\elasticsearch\\DebugPanel',
            ],
        ],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['172.21.0.1', '127.0.0.1', '::1'],
    ];
}

// Local environment settings
$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $config = ArrayHelper::merge($config, require $localConfig);
}

return $config;
