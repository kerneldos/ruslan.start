<?php

namespace app\components\services;

use app\components\jobs\YandexIndexingJob;
use app\models\Config;
use Yii;
use yii\web\NotFoundHttpException;

class Yandex extends \yii\authclient\clients\Yandex implements ServiceInterface {

    const SERVICE_NAME  = 'yandex';

    const CATEGORY_NAME = 'Yandex Диск';

    const RETURN_URL = 'https://45.12.74.245/site/get-token?service=yandex';
//    const RETURN_URL = 'https://127.0.0.1/site/get-token?service=yandex';

    /**
     * @return void
     */
    public function indexing(): void {
        Yii::$app->queue->push(new YandexIndexingJob());
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
        $this->returnUrl    = self::RETURN_URL;

        $this->stateStorage = 'app\components\services\DbStateStorage';
    }

    /**
     * @return bool
     */
    public function needAuth(): bool {
        return empty($this->accessToken);
    }

    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $request->addHeaders(['Authorization' => $accessToken->getToken()]);
    }

}