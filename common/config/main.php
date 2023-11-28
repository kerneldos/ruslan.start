<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
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
    ],
];
