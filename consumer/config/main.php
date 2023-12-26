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
    'modules' => [
        'staff' => [
            'class' => 'consumer\modules\staff\Module',
        ],
    ],
    'components' => [
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-consumer',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => $params['login_url'],
            'identityCookie' => ['name' => '_identity-app', 'httpOnly' => true, 'domain' => '.' . $params['main_domain']],
        ],
        'session' => [
            'cookieParams' => [
                'domain' => '.' . $params['main_domain'],
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

                'staff' => 'staff/default/index',
                'staff/<_c>' => 'staff/<_c>/index',
                'staff/<_c>/<_a>' => 'staff/<_c>/<_a>',

                'document/<id>' => 'site/view',
                '<_a:login|logout>' => 'site/<_a>',
                '<_c>' => '<_c>/index',
                '<_c>/<_a>' => '<_c>/<_a>',
            ],
        ],
    ],
    'params' => $params,
];
