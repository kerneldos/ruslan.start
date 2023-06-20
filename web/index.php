<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

defined('YII_DEBUG') or define('YII_DEBUG', $config['yii_debug'] ?? false);
defined('YII_ENV') or define('YII_ENV', $config['yii_env'] ?? 'prod');

error_reporting(0);
ini_set('memory_limit', '-1');

(new yii\web\Application($config))->run();
