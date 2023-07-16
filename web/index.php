<?php

// Local environment settings
$envDev = __DIR__ . '/../config/env.dev.php';
if (is_file($envDev)) {
    require __DIR__ . '/../config/env.dev.php';
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

ini_set('memory_limit', '-1');

(new yii\web\Application($config))->run();
