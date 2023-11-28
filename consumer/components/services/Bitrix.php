<?php

namespace consumer\components\services;

use consumer\components\jobs\BitrixIndexingJob;
use consumer\models\Config;
use Yii;
use yii\authclient\OAuth2;
use yii\web\NotFoundHttpException;

class Bitrix extends OAuth2 implements ServiceInterface {
    const SERVICE_NAME  = 'bitrix';

    const CATEGORY_NAME = 'Bitrix Диск';

    const RETURN_URL = 'https://yanayarosh.ru/site/get-token?service=bitrix';
//    const RETURN_URL = 'https://127.0.0.1/site/get-token?service=bitrix';

    /** @inheritdoc */
    public $authUrl = 'https://%s/oauth/authorize';

    /** @inheritdoc */
    public $tokenUrl = 'https://oauth.bitrix.info/oauth/token/';

    /** @inheritdoc */
    public $apiBaseUrl = 'https://%s/rest/';

    /**
     * @return void
     */
    public function indexing(): void {
        Yii::$app->queue->push(new BitrixIndexingJob());
    }

    /**
     * @return void
     * @throws NotFoundHttpException
     */
    public function init() {
        parent::init();

        $this->stateStorage = 'consumer\components\services\DbStateStorage';

        $domain       = Config::findOne(['name' => 'bitrix_domain']);
        $clientId     = Config::findOne(['name' => 'bitrix_client_id']);
        $clientSecret = Config::findOne(['name' => 'bitrix_client_secret']);

        if (empty($clientId) || empty($clientSecret) || empty($domain)) {
            throw new NotFoundHttpException('Client Id or Client Secret is empty');
        }

        $this->clientId     = $clientId->value;
        $this->clientSecret = $clientSecret->value;
        $this->returnUrl    = self::RETURN_URL;
        $this->authUrl      = sprintf($this->authUrl, $domain->value);
        $this->apiBaseUrl   = sprintf($this->apiBaseUrl, $domain->value);

        if (!empty($this->accessToken)) {
            if (time() >= ($this->accessToken->createTimestamp + $this->accessToken->getParam('expires_in'))) {
                $this->refreshAccessToken($this->accessToken);
            }
        }
    }

    /**
     * @return bool
     */
    public function needAuth(): bool {
        return empty($this->accessToken);
    }

    /**
     * Initializes authenticated user attributes.
     *
     * @return array auth user attributes.
     */
    protected function initUserAttributes(): array {
        return [];
    }

    /**
     * @param $request
     * @param $accessToken
     *
     * @return void
     */
    public function applyAccessTokenToRequest($request, $accessToken) {
        $data = $request->getData();
        $data['auth'] = $accessToken->getToken();

        $request->setData($data);
    }

    /**
     * @return string
     */
    public function getStateKeyPrefix(): string {
        return self::SERVICE_NAME . '_';
    }
}