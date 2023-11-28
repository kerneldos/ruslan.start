<?php

namespace console\controllers;

use yii\console\Controller;

class NewConsumerController extends Controller {
    /**
     * @return false|int
     */
    public function actionInit(string $consumer) {
        $consumerConfig = $this->renderPartial('config', ['consumer' => $consumer]);

        return file_put_contents(dirname(__DIR__, 2). '/consumer/config/client/' . $consumer . '.php', $consumerConfig);
    }
}