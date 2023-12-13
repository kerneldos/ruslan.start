<?php /** @var string $consumer */ ?>
<?= '<?php' ?>

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql;port=<?= Yii::$app->params['db_port'] ?>;dbname=consumer_<?= $consumer ?>',
            'username' => <?= Yii::$app->params['db_user'] ?>,
            'password' => <?= Yii::$app->params['db_password'] ?>,
            'charset' => 'utf8',
        ],
    ],
    'params' => [
        'subDomain' => '<?= $consumer ?>',
    ],
];
