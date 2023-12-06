<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-consumer',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'consumer\controllers',
    'components' => [
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-consumer',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => 'https://login.yanayarosh.ru/',
            'identityCookie' => ['name' => '_identity-app', 'httpOnly' => true, 'domain' => '.app.ru'],
        ],
        'session' => [
            'cookieParams' => [
                'domain' => '.app.ru',
                'httpOnly' => true,
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
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
