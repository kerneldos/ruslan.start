<?php /** @var string $consumer */ ?>
<?= '<?php' ?>

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=mysql;dbname=consumer_<?= $consumer ?>',
            'username' => 'root',
            'password' => 'secret',
            'charset' => 'utf8',
        ],
    ],
    'params' => [
        'subDomain' => '<?= $consumer ?>',
    ],
];
