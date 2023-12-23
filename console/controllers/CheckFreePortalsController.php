<?php

namespace console\controllers;

use common\models\Portal;
use consumer\models\Document;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class CheckFreePortalsController extends Controller {
    /**
     * @return void
     * @throws Exception
     * @throws InvalidRouteException
     * @throws InvalidConfigException
     * @throws \yii\console\Exception
     * @throws \yii\db\Exception
     */
    public function actionIndex() {
        $freePortalsCount = Portal::find()->where(['user_id' => null])->count();

        if ($freePortalsCount < 5) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyz';

            $httpClient = new Client();

            $commonConfig = require dirname(__DIR__, 2) . '/common/config/main-local.php';

            for ($i = $freePortalsCount; $i < 5; $i++) {
                Yii::$app->set('db', $commonConfig['components']['db']);

                $portal = new Portal();

                do {
                    $portal->temp_name = substr(str_shuffle($str), 0, 6);
                } while (!$portal->save());

                if (!empty(Yii::$app->params['beget_login']) && !empty(Yii::$app->params['beget_password'])) {
                    $httpClient->get('https://api.beget.com/api/domain/addSubdomainVirtual', [
                        'login' => Yii::$app->params['beget_login'],
                        'passwd' => Yii::$app->params['beget_password'],
                        'input_format' => 'json',
                        'output_format' => 'json',
                        'input_data' => json_encode([
                            'domain_id' => 9706501,
                            'subdomain' => $portal->temp_name,
                        ]),
                    ])->send();

                    $httpClient->get('https://api.beget.com/api/dns/changeRecords', [
                        'login' => Yii::$app->params['beget_login'],
                        'passwd' => Yii::$app->params['beget_password'],
                        'input_format' => 'json',
                        'output_format' => 'json',
                        'input_data' => json_encode([
                            'fqdn' => $portal->temp_name . '.' . Yii::$app->params['main_domain'],
                            'records' => [
                                'A' => [
                                    [
                                        'priority' => 10,
                                        'value' => '57.129.5.67',
                                    ],
                                ],
                                'MX' => [
                                    [
                                        'priority' => 10,
                                        'value' => 'mx1.beget.ru'
                                    ],
                                    [
                                        'priority' => 20,
                                        'value' => 'mx2.beget.ru'
                                    ]
                                ],
                                'TXT' => [
                                    [
                                        'priority' => 10,
                                        'value' => 'v=spf1 redirect=beget.com'
                                    ]
                                ]
                            ],
                        ]),
                    ])->send();
                }

                $consumerDbName = 'consumer_' . $portal->temp_name;
                Yii::$app->preInstallDb->createCommand('CREATE DATABASE ' . $consumerDbName)->execute();

                Yii::$app->runAction('new-consumer/init', [$portal->temp_name]);

                $consumerConfig = require dirname(__DIR__, 2) . '/consumer/config/client/' . $portal->temp_name . '.php';

                Yii::$app->set('db', $consumerConfig['components']['db']);
                Yii::$app->params['sub_domain'] = $portal->temp_name;

                Yii::$app->runAction('migrate/up', ['migrationPath' => '@console/migrations/consumer/', 'interactive' => false]);

                $configRows = [
                    ['Bitrix Domain', 'bitrix_domain'],
                    ['Bitrix Client Id', 'bitrix_client_id'],
                    ['Bitrix Client Secret', 'bitrix_client_secret'],
                    ['Yandex Client Id', 'yandex_client_id'],
                    ['Yandex Client Secret', 'yandex_client_secret'],
                ];

                $connection = Yii::$app->db;
                $connection->createCommand()->batchInsert('config', ['title', 'name'], $configRows)->execute();

                Document::createIndex();
            }
        }
    }
}