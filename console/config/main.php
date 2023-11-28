<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'queue' => [
            'class' => 'yii\queue\amqp_interop\Queue',
            'host' => 'rabbitmq',
            'port' => 5672,
            'user' => 'rmuser',
            'password' => 'rmpassword',
            'queueName' => 'queue-new',
            'driver' => \yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
            'dsn' => 'amqp:',
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => 'opensearch:9200'],
            ],
            'autodetectCluster' => false,
            'dslVersion' => 7, // по умолчанию - 5
            'defaultProtocol' => 'http',
            'auth' => [
                'username' => 'admin',
                'password' => 'admin',
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'yandex' => [
                    'class' => 'consumer\components\services\Yandex',
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
                    'class' => 'consumer\components\services\Samba',
                ],
                'bitrix' => [
                    'class' => 'consumer\components\services\Bitrix',
                ],
            ],
        ],
    ],
    'params' => $params,
];
