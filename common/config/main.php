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
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db' => 'loginDb',
            'defaultRoles' => ['portal_member'],
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
];
