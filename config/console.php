<?php

use yii\helpers\ArrayHelper;

ini_set('memory_limit', '-1');

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
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
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

// Local environment settings
$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $config = ArrayHelper::merge($config, require $localConfig);
}

return $config;
