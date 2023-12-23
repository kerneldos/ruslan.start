<?php

namespace consumer\components\services;

use consumer\components\jobs\YandexIndexingJob;
use consumer\models\Config;
use Yii;
use yii\web\NotFoundHttpException;

class Yandex extends \yii\authclient\clients\Yandex implements ServiceInterface {

    const SERVICE_NAME  = 'yandex';

    const CATEGORY_NAME = 'Yandex Диск';

    const RETURN_URL = 'https://%s.%s/site/get-token?service=yandex';

    /**
     * @param string $consumer
     *
     * @return void
     * @throws NotFoundHttpException
     */
    public function indexing(string $consumer = ''): void {
        if (empty($consumer)) {
            throw new NotFoundHttpException('Param consumer is required');
        }

        Yii::$app->queue->push(new YandexIndexingJob(['consumer' => $consumer]));
    }

    /**
     * @return void
     * @throws NotFoundHttpException
     */
    public function init() {
        parent::init();

        $clientId = Config::findOne(['name' => 'yandex_client_id']);
        $clientSecret = Config::findOne(['name' => 'yandex_client_secret']);

        if (empty($clientId) || empty($clientSecret)) {
            throw new NotFoundHttpException('Client Id or Client Secret is empty');
        }

        $this->clientId     = $clientId->value;
        $this->clientSecret = $clientSecret->value;
        $this->returnUrl    = sprintf(self::RETURN_URL, Yii::$app->params['sub_domain'], Yii::$app->params['main_domain']);

        $this->stateStorage = 'consumer\components\services\DbStateStorage';
    }

    /**
     * @return bool
     */
    public function needAuth(): bool {
        return empty($this->accessToken);
    }

    /**
     * @param $request
     * @param $accessToken
     *
     * @return void
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $request->addHeaders(['Authorization' => $accessToken->getToken()]);
    }

    /**
     * @return string
     */
    public function getStateKeyPrefix(): string {
        return self::SERVICE_NAME . '_';
    }
}