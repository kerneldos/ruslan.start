<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-login',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'main/default/login',
    'bootstrap' => ['log'],
    'modules' => [
        'main' => [
            'class' => 'login\modules\main\Module',
        ],
    ],
    'components' => [
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-login',
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
            'errorAction' => 'main/default/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
            ],
            'rules' => [
//                '' => 'main/default/index',
                '<_a:login|logout|signup|reset-password|verify-email>' => 'main/default/<_a>',
                '<_m>' => '<_m>/default/index',
                '<_m>/<_c>' => '<_m>/<_c>/index',
                '<_m>/<_c>/<_a>' => '<_m>/<_c>/<_a>',
            ],
        ],
    ],
    'params' => $params,
];
