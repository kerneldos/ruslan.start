<?php

namespace console\controllers;

use common\models\Domain;
use Yii;
use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Exception;

class CheckFreeDomainsController extends Controller {
    /**
     * @return void
     * @throws Exception
     */
    public function actionIndex() {
        $freeDomainsCount = Domain::find()->where(['user_id' => null])->count();

        if ($freeDomainsCount < 5) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyz';

            $client = new Client();

            for ($i = $freeDomainsCount; $i <= 5; $i++) {
                $domain = new Domain();

                do {
                    $time = time();

                    $domain->temp_name = substr(str_shuffle($str), 0, 6);
                    $domain->created_at = $time;
                    $domain->updated_at = $time;
                } while (!$domain->save());

                $client->get('https://api.beget.com/api/domain/addSubdomainVirtual', [
                    'login' => Yii::$app->params['beget_login'],
                    'passwd' => Yii::$app->params['beget_password'],
                    'input_format' => 'json',
                    'output_format' => 'json',
                    'input_data' => json_encode([
                        'domain_id' => 9706501,
                        'subdomain' => $domain->temp_name,
                    ]),
                ])->send();

                $client->get('https://api.beget.com/api/dns/changeRecords', [
                    'login' => Yii::$app->params['beget_login'],
                    'passwd' => Yii::$app->params['beget_password'],
                    'input_format' => 'json',
                    'output_format' => 'json',
                    'input_data' => json_encode([
                        'fqdn' => $domain->temp_name . '.' . Yii::$app->params['main_domain'],
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
        }
    }
}