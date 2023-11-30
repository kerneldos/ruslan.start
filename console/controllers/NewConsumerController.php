<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Exception;

class NewConsumerController extends Controller {
    /**
     * @param string $consumer
     *
     * @return false|int
     * @throws Exception
     */
    public function actionInit(string $consumer) {
        $consumerConfig = $this->renderPartial('config', ['consumer' => $consumer]);

        $configRows = [
            ['Bitrix Domain', 'bitrix_domain'],
            ['Bitrix Client Id', 'bitrix_client_id'],
            ['Bitrix Client Secret', 'bitrix_client_secret'],
            ['Yandex Client Id', 'yandex_client_id'],
            ['Yandex Client Secret', 'yandex_client_secret'],
        ];

        $connection = Yii::$app->db;
        $connection->createCommand()->batchInsert('config', ['title', 'name'], $configRows)->execute();

        return file_put_contents(dirname(__DIR__, 2). '/consumer/config/client/' . $consumer . '.php', $consumerConfig);
    }
}